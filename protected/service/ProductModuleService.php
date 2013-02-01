<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ProductModuleService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ProductModuleService
{
    const ERROR_PARENT_MODULE_NOTEXIST = 'parent module not existed';

    /**
     * separate module as product
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int             $id               module id
     * @return  array                             module separate result
     */
    public static function separateModule($id)
    {
        $resultInfo = array();
        $productModule = ProductModule::model()->findByPk($id);
        $productModel = ProductService::loadModel($productModule['product_id']);
        $productInfo = $productModel->attributes;
        $productInfo['name'] = $productModule['name'];
        $productInfo['group_name'] = $productModel->group_name;
        $productInfo['product_manager'] = $productModel->product_manager;
        unset($productInfo['id']);
        $createNewProductResult = ProductService::copyProduct($productModule['product_id'], $productInfo);
        if(CommonService::$ApiResult['FAIL'] == $createNewProductResult['status'])
        {
            return $createNewProductResult;
        }
        else
        {
            $newProductId = $createNewProductResult['detail']['id'];
            try
            {
                //update module's first layer child
                Yii::app()->db->createCommand()->setText('update {{product_module}} set product_id=' .
                        $newProductId . ',grade=grade-' . $productModule['grade'] .
                        ',full_path_name=name,parent_id=null ' .
                        ' where product_id=' . $productModule['product_id'] .
                        ' and parent_id=' . $id)->execute();
                //update other layer child
                Yii::app()->db->createCommand()->setText('update {{product_module}} set product_id=' .
                        $newProductId . ',grade=grade-' . $productModule['grade'] .
                        ',full_path_name=REPLACE(full_path_name,'.
                        Yii::app()->db->quoteValue($productModule['full_path_name'] . ProductModule::MODULE_SPLITTER).',"")'.
                        ' where product_id=' . $productModule['product_id'] .
                        ' and full_path_name like ' .
                        Yii::app()->db->quoteValue($productModule['full_path_name'] . ProductModule::MODULE_SPLITTER . '%'))->execute();

                $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
                foreach($infoTypeArr as $info)
                {
                    Yii::app()->db->createCommand()->setText('update {{' . $info . '_info}} set product_id=' .
                            $newProductId . ',productmodule_id=null where productmodule_id=' . $id)->execute();
                    Yii::app()->db->createCommand()->setText('update {{' . $info . '_info}} set product_id=' .
                            $newProductId . ' where productmodule_id in( select id from {{product_module}} where product_id=' .
                            $newProductId . ')')->execute();
                    Yii::app()->db->createCommand()->setText('insert into {{etton' . $info . '_' .
                            $newProductId . '}} select * from {{etton' . $info . '_' .
                            $productModule['product_id'] .
                            '}} where ' . $info . '_id in(select id from {{' . $info . '_info}} where product_id=' . $newProductId . ')')->execute();
                    Yii::app()->db->createCommand()->setText('delete from {{etton' . $info . '_' .
                            $productModule['product_id'] .
                            '}} where ' . $info . '_id in(select id from {{' . $info . '_info}} where product_id=' . $newProductId . ')')->execute();
                }
                //delete separate module
                Yii::app()->db->createCommand()->setText('delete from {{product_module}} where id=' . $id)->execute();
            }
            catch(Exception $e)
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail']['id'] = $e->getMessage();
                return $resultInfo;
            }
        }
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    /**
     * delete module
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int             $id               module id
     * @return  array                             module delete result
     */
    public static function deleteModule($id)
    {
        $resultInfo = array();
        if(self::hasChildModule($id))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('ProductModule', 'The module has child module, cannot be deleted');
            return $resultInfo;
        }

        $productModule = ProductModule::model()->findByPk($id);
        $parentId = $productModule->parent_id;
        if(!$productModule->delete())
        {
            $editResult['status'] == CommonService::$ApiResult['FAIL'];
            $editResult['detail'] == $deleteModel->getErrors();
            return $resultInfo;
        }

        $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
        foreach($infoTypeArr as $type)
        {
            self::updateInfoModule($type, $id, $parentId);
        }
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    /**
     * delete module
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string          $infoType         bug,case or result
     * @param   $int            $oldId            old module id
     * @param   int             $newId            new module id
     * @return
     */
    private static function updateInfoModule($infoType, $oldId, $newId)
    {
        Yii::app()->db
                ->createCommand()
                ->update('{{' . $infoType . '_info}}',
                        array('productmodule_id' => $newId),
                        'productmodule_id=:oldId',
                        array(':oldId' => $oldId));
    }

    /**
     * check if has child module
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int             $id               module id
     * @return  boolean                           has child module or not
     */
    private static function hasChildModule($id)
    {
        $moduleInfos = Yii::app()->db->createCommand()
                        ->select('count(id) as totalNum')
                        ->from('{{product_module}}')
                        ->where('parent_id = :parentId', array(':parentId' => $id))
                        ->queryRow();
        $count = $moduleInfos['totalNum'];
        if(0 < $count)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * edit product module
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   array             $params         module's information
     * @return  array                             module update result
     */
    public static function editProductModule($params)
    {
        $resultInfo = array();
        $parentModule = array();
        $oldFullPathName = '';
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(!empty($params['id']))
        {
            $productModule = ProductModule::model()->findByPk((int) $params['id']);
            $oldRecordAttributs = $productModule->attributes;
            $actionType = BugfreeModel::ACTION_EDIT;
            $productModule = self::loadModel($params['id']);
            $oldFullPathName = $productModule->full_path_name;
        }
        else
        {
            $productModule = new ProductModule();
        }
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try
        {
            $productModule->attributes = $params;
            if(0 != $productModule->parent_id)
            {
                $parentModule = ProductModuleService::loadModel((int)$productModule->parent_id);
                if(!empty($parentModule))
                {
                    $productModule->grade = $parentModule->grade + 1;
                    $productModule->full_path_name = $parentModule->full_path_name . ProductModule::MODULE_SPLITTER . $params['name'];
                }
                else
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'] = array('parent_id' => Yii::t('ProductModule', self::ERROR_PARENT_MODULE_NOTEXIST));
                    return $resultInfo;
                }
            }
            else
            {
                $productModule->parent_id = null;
                $productModule->grade = 1;
                $productModule->full_path_name = $params['name'];
            }
            $productModule->scenario = ProductModule::SCENARIO_EDIT;
            if(!$productModule->save())
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = $productModule->getErrors();
            }
            else
            {
                $newRecord = self::loadModel($productModule->id);
                $addActionResult = AdminActionService::addActionNotes('product_module', $actionType, $newRecord, $oldRecordAttributs);
                if('' != $oldFullPathName && ($oldFullPathName != $newRecord['full_path_name']))
                {
                    $renameResult = self::updateChildModule($oldFullPathName);
                    if(CommonService::$ApiResult['SUCCESS'] == $renameResult['status'])
                    {
                        $transaction->commit();
                        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
                        $resultInfo['detail'] = array('id' => $productModule->id);
                    }
                    else
                    {
                        $resultInfo = $renameResult;
                    }
                }
                else
                {
                    $transaction->commit();
                    $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
                    $resultInfo['detail'] = array('id' => $productModule->id);
                }
            }
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = $e->getMessage();
        }
        return $resultInfo;
    }

    /**
     * get module of grade = 1
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int               $productId      product id
     * @param   string            $productName    product name
     * @return  array                             layer1 module info
     */
    public static function getLayer1Module($productId, $productName)
    {
        $moduleInfos = Yii::app()->db->createCommand()
                        ->select('id,name')
                        ->from('{{product_module}}')
                        ->where('product_id = :productId and grade=1',
                                array(':productId' => $productId))
                        ->order('display_order desc')
                        ->queryAll();
        $selectData = array();
        $selectData[0] = $productName . '/';
        foreach($moduleInfos as $module)
        {
            $selectData[$module['id']] = $productName . '/' . $module['name'];
        }
        return $selectData;
    }

    /**
     * get child module select option
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int               $parentId       parent module id
     * @return  array                             child module information
     */
    public static function getChildModuleSelectOption($parentId)
    {
        if(0 == $parentId)
        {
            return array('0' => '/');
        }
        $parentModuleInfo = ProductModule::model()->findByPk($parentId);
        if($parentModuleInfo == null)
        {
            $moduleInfos = array();
        }
        else
        {
            $parentFullPathName = $parentModuleInfo->full_path_name;
            $productId = $parentModuleInfo->product_id;
            $moduleInfos = Yii::app()->db->createCommand()
                            ->select('id,parent_id,grade,full_path_name')
                            ->from('{{product_module}}')
                            ->where('product_id = :productId and full_path_name like \'' .
                                    $parentFullPathName . '%\'',
                                    array(':productId' => $productId))
                            ->order('grade asc,display_order desc')
                            ->queryAll();
        }


        $moduleArr = TreeService::formOptionTreeData($moduleInfos, $parentId);
        $rootNode = new TreeDataModel();
        $rootNode->id = $parentId;
        $rootNode->name = '/';
        $rootNode->nodes = $moduleArr;
        $returnArr = array();
        $returnArr = self::getModuleNameArr($returnArr, $rootNode);
        return $returnArr;
    }

    /**
     * get tree data's module array
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   array               $returnArr    module array
     * @param   array               $treeData     tree data
     * @return  array                             tree data's name information
     */
    public static function getModuleNameArr($returnArr, $treeData)
    {
        $returnArr[$treeData->id] = $treeData->name;
        if(!empty($treeData->nodes))
        {
            foreach($treeData->nodes as $node)
            {
                $returnArr = self::getModuleNameArr($returnArr, $node);
            }
        }
        return $returnArr;
    }

    /**
     * get module tree for select use
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int               $productId      product id
     * @param   int               $selectedId     module id
     * @return  json                              json encoded data
     */
    public static function getSelectModuleTree($productId, $selectedId)
    {
        $moduleInfos = Yii::app()->db->createCommand()
                        ->select('id,parent_id,grade,name')
                        ->from('{{product_module}}')
                        ->where('product_id = :productId',
                                array(':productId' => $productId))
                        ->order('grade asc,display_order desc,id')
                        ->queryAll();
        $fullIdArr = array();
        $fullIdArr = self::getFullPathId($fullIdArr, $selectedId);
        $moduleArr = TreeService::formSelectedTreeData($moduleInfos, $fullIdArr);
        $rootNode = new TreeDataModel();
        $rootNode->id = 0;
        $productInfo = ProductService::loadModel($productId);
        $rootNode->name = $productInfo['name'];
        $rootNode->open = true;
        $rootNode->nodes = $moduleArr;
        $rootArr = array();
        $rootArr[0] = $rootNode;
        return CJSON::encode($rootArr);
    }

    public static function getFullPathId($fullIdArr, $selectedId)
    {
        $moduleInfo = ProductModule::model()->findByPk($selectedId);
        $fullIdArr[$moduleInfo->grade] = $moduleInfo->id;
        if(!empty($moduleInfo->parent_id))
        {
            $fullIdArr = self::getFullPathId($fullIdArr, $moduleInfo->parent_id);
        }
        return $fullIdArr;
    }

    /**
     * get module tree for ajax
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int               $productId      product id
     * @param   int               $parentId       parent module id
     * @return  json                              json encoded module info
     */
    public static function getAjaxModuleTree($productId, $parentId)
    {
        if(0 == $parentId)
        {
            $moduleInfos = Yii::app()->db->createCommand()
                            ->select('id,parent_id,grade,name')
                            ->from('{{product_module}}')
                            ->where('product_id = :productId and parent_id is null',
                                    array(':productId' => $productId))
                            ->order('display_order desc,id')
                            ->queryAll();
        }
        else
        {
            $moduleInfos = Yii::app()->db->createCommand()
                            ->select('id,parent_id,grade,name')
                            ->from('{{product_module}}')
                            ->where('product_id = :productId and parent_id = :parentId',
                                    array(':productId' => $productId, ':parentId' => $parentId))
                            ->order('display_order desc,id')
                            ->queryAll();
        }
        $moduleIdArr = array();
        foreach($moduleInfos as $moduleInfo)
        {
            $moduleIdArr[] = $moduleInfo['id'];
        }

        $childInfos = Yii::app()->db->createCommand()
                        ->select(array('count(*) as num', 'parent_id'))
                        ->from('{{product_module}}')
                        ->where(array('in', 'parent_id', $moduleIdArr))
                        ->group('parent_id')
                        ->queryAll();
        $moduleArr = TreeService::formAjaxTreeData($moduleInfos, $childInfos);

        if(0 == $parentId)
        {
            $rootNode = new TreeDataModel();
            $rootNode->id = $parentId;
            $productInfo = ProductService::loadModel($productId);
            $rootNode->name = $productInfo['name'];
            $rootNode->open = true;
            $rootArr = array();
            $rootArr[0] = $rootNode;
            $rootArr[0]->nodes = $moduleArr;
            return CJSON::encode($rootArr);
        }
        else
        {
            return CJSON::encode($moduleArr);
        }
    }

    public static function getModuleChildren($fullPathModuleName)
    {
        $moduleInfos = Yii::app()->db->createCommand()
                        ->select('id,parent_id,name')
                        ->from('{{product_module}}')
                        ->where(array('like', 'full_path_name', $fullPathModuleName . '%'))
                        ->order('grade,id')
                        ->queryAll();
        return $moduleInfos;
    }

    /**
     * update child module after module been modified
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string         $oldFullPathName   module's old full path name
     * @return  array                             module update result
     */
    private static function updateChildModule($oldFullPathName)
    {
        $resultInfo = array();
        $childModuleInfos = self::getModuleChildren($oldFullPathName);
        $childMouduleCount = count($childModuleInfos);
        for($i = 0; $i < $childMouduleCount; $i++)
        {
            $moduleInfo = self::loadModel($childModuleInfos[$i]['id']);
            $parentModuleTmp = ProductModule::model()->findByPk((int)$moduleInfo->parent_id);
            $parentFullPathName = '';
            if(!empty($parentModuleTmp))
            {
                $parentFullPathName = $parentModuleTmp->full_path_name . ProductModule::MODULE_SPLITTER;
                $moduleInfo->grade = $parentModuleTmp->grade + 1;
            }
            $moduleInfo->full_path_name = $parentFullPathName . $moduleInfo->name;

            if(!$moduleInfo->save())
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = $moduleInfo->getErrors();
                return $resultInfo;
            }
        }
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    /**
     * Get module list's select html
     *
     * @author                      Yupeng Lee<leeyupeng@gmail.com>
     * @param   int    $ProjectID
     * @param   string $LinkUrl
     * @param   string $ModuleType  Bug or Case
     * @return  string
     */
    public static function getSelectModuleListOption($ProjectID)
    {
        $ModuleList = self::getProductModuleList($ProjectID);
        $idNameArr = array();
        foreach($ModuleList as $ModuleID => $ModuleInfo)
        {
            $idNameArr[$ModuleID] = $ModuleInfo['NamePath'];
        }
        return $idNameArr;
    }

    private static function getModuleList($productId)
    {
        $moduleInfos = Yii::app()->db->createCommand()
                        ->select('id,parent_id,grade,name')
                        ->from('{{product_module}}')
                        ->where('product_id = :productId',
                                array(':productId' => $productId))
                        ->order(array('grade desc', 'display_order asc', 'id desc'))
                        ->queryAll();
        $TreeModuleList = array(0 => array());
        foreach($moduleInfos as $moduleInfo)
        {
            if(!isset($moduleInfo['parent_id']))
            {
                $moduleInfo['parent_id'] = '0';
            }
            $ParentID = $moduleInfo['parent_id'];
            $moduleInfo['ChildIDs'] = $moduleInfo['id'];
            if(!isset($TreeModuleList[$ParentID]))
            {
                $TreeModuleList[$ParentID] = array();
            }
            if(!isset($TreeModuleList[$moduleInfo['id']]))
            {
                $TreeModuleList[$ParentID] = array($moduleInfo['id'] => $moduleInfo) + $TreeModuleList[$ParentID];
            }
            else
            {
                $moduleInfo['ChildIDs'] .= ',' . join(',', array_keys($TreeModuleList[$moduleInfo['id']]));
                $TreeModuleList[$ParentID] = array($moduleInfo['id'] => $moduleInfo) + $TreeModuleList[$moduleInfo['id']] + $TreeModuleList[$ParentID];
                unset($TreeModuleList[$moduleInfo['id']]);
            }
        }
        return $TreeModuleList[0];
    }

    /**
     * Get project module List, including the root module '/'
     *
     * @author                      Yupeng Lee<leeyupeng@gmail.com>
     * @param   int    $ProjectID
     * @param   string $ModuleType  Bug or Case
     * @return  array  $ModuleList
     */
    private static function getProductModuleList($productId)
    {
        $ProjectInfo = ProductService::loadModel($productId);
        $ParentID = '0';
        $LastModuleID = '0';
        $ModuleList = array('0' => array('ModuleID' => 0,
                'ParentID' => '',
                'name' => $ProjectInfo['name'],
                'IDPath' => '0',
                'NamePath' => '/',
                'IsLeaf' => true,
                'IsLastLeaf' => true,
                'grade' => '0',
                'ChildIDs' => '0'));

        $ProductModuleList = self::getModuleList($productId);
        $ModuleList += $ProductModuleList;
        foreach($ModuleList as $ModuleID => $ModuleInfo)
        {
            if($ModuleID == '0')
            {
                continue;
            }
            $ModuleList[$ModuleID]['IsLastLeaf'] = true;

            if($ParentID == $ModuleInfo['parent_id'])
            {
                $ModuleList[$LastModuleID]['IsLastLeaf'] = false;
            }

            $ParentID = $ModuleInfo['parent_id'];

            $ParentIDPath = $ModuleList[$ParentID]['IDPath'];

            $ParentNamePath = $ModuleList[$ParentID]['NamePath'];
            if($ParentIDPath == '')
            {
                $ParentIDPath = '0';
            }
            $ModuleList[$ModuleID]['IDPath'] = $ParentIDPath . ',' . $ModuleID;
            if($ParentNamePath == '/')
            {
                $ParentNamePath = '';
            }
            $ModuleList[$ModuleID]['NamePath'] = $ParentNamePath . '/' . $ModuleInfo['name'];
            $ModuleList[$ModuleID]['IsLeaf'] = true;
            if($ParentID != '')
            {
                $ModuleList[$ParentID]['IsLeaf'] = false;
                $ModuleList[$ParentID]['IsLastLeaf'] = false;
            }
            $ModuleList[$LastModuleID]['NextTreeModuleID'] = $ModuleID;
            $LastModuleID = $ModuleID;
        }
        return $ModuleList;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public static function loadModel($id)
    {
        $model = ProductModule::model()->findByPk((int) $id);
        if($model === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        else
        {
            $model->owner_name = CommonService::getUserRealName($model->owner);
            $model->product_name = $model->product->name;
        }
        return $model;
    }

}

?>
