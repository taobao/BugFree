<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ProductService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ProductService
{
    const ERROR_GROUP_NOT_FOUND = 'group not found';
    const ERROR_USER_NOT_FOUND = 'user not found';

    /**
     * get product operation
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $productId            product id
     * @param   string      $isDropped            is dropped
     * @return  string                            operation html string
     */
    public static function getProductOperation($productId, $isDropped)
    {
        $returnStr = '';
        $newIsDropped = 1 - $isDropped;
        $returnStr .= '<a class="with_underline" href="' . Yii::app()->createUrl('product/edit',
                        array('id' => $productId)) . '">' . Yii::t('Common', 'Edit') . '</a>';
        //only system_admin can copy,disable and enable product
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
        {
            $returnStr .= '|<a class="with_underline" href="' . Yii::app()->createUrl('product/edit',
                            array('source_id' => $productId)) . '">' . Yii::t('Common', 'Copy') . '</a>';
            if(1 != $isDropped)
            {
                $returnStr .= '|<a class="with_underline" href="javascript:showMergeDialog(' . $productId . ');">' . Yii::t('Common', 'Merge') . '</a>';
            }
//            $returnStr .= '|<a class="with_underline" href="index.php?r=product/disable&id=' . $productId .
//                    '&is_dropped=' . $newIsDropped . '" onclick="return confirm(\'' .
//                    Yii::t('Common', 'Are you sure?') . '\');">';
//            if(CommonService::$TrueFalseStatus['TRUE'] == $isDropped)
//            {
//                $returnStr .= Yii::t('Common', 'Enable') . '</a>';
//            }
//            else
//            {
//                $returnStr .= Yii::t('Common', 'Disable') . '</a>';
//            }
        }

        $returnStr .= '|<a class="with_underline" href="' .
                Yii::app()->createUrl('productModule/index',
                        array('product_id' => $productId)) . '">' . Yii::t('Common', 'Modules') . '</a>';
        return $returnStr;
    }

    public static function mergeProduct($sourceId, $disId)
    {
        $resultInfo = array();
        $checkMergeResult = self::checkMergeable($sourceId, $disId);
        if(CommonService::$ApiResult['FAIL'] == $checkMergeResult['status'])
        {
            return $checkMergeResult;
        }
        $sourceProductInfo = Product::model()->findByPk($sourceId);
        $moduleInfo = array();
        $moduleInfo['name'] = $sourceProductInfo['name'];
        $moduleInfo['display_order'] = 0;
        $moduleInfo['product_id'] = $disId;
        $createModuleResult = ProductModuleService::editProductModule($moduleInfo);
        if(CommonService::$ApiResult['FAIL'] == $createModuleResult['status'])
        {
            return $createModuleResult;
        }
        $newModuleId = $createModuleResult['detail']['id'];

        try
        {
            //update top layer module
            Yii::app()->db->createCommand()->setText('update {{product_module}} set product_id=' .
                    $disId . ',grade=grade+1' .
                    ',full_path_name=CONCAT(' .
                    Yii::app()->db->quoteValue($sourceProductInfo['name'] .
                            ProductModule::MODULE_SPLITTER) .
                    ',full_path_name),parent_id=' . $newModuleId .
                    ' where product_id=' . $sourceId .
                    ' and parent_id is null')->execute();
            //update other layer module
            Yii::app()->db->createCommand()->setText('update {{product_module}} set product_id=' .
                    $disId . ',grade=grade+1' .
                    ',full_path_name=CONCAT(' .
                    Yii::app()->db->quoteValue($sourceProductInfo['name'] .
                            ProductModule::MODULE_SPLITTER) .
                    ',full_path_name) where product_id=' . $sourceId .
                    ' and parent_id is not null')->execute();

            $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
            foreach($infoTypeArr as $infoType)
            {
                Yii::app()->db->createCommand()->setText('update {{' . $infoType . '_info}} set product_id=' .
                        $disId . ',productmodule_id=' . $newModuleId . ' where product_id=' .
                        $sourceId . ' and productmodule_id is null')->execute();
                Yii::app()->db->createCommand()->setText('update {{' . $infoType . '_info}} set product_id=' .
                        $disId . ' where productmodule_id in( select id from {{product_module}} where product_id=' .
                        $disId . ')')->execute();

                $fieldNameStr = FieldConfigService::getActiveCustomFieldName($disId, $infoType);
                Yii::app()->db->createCommand()->setText('insert into {{etton' . $infoType . '_' .
                        $disId . '}} (' . $fieldNameStr . ') select ' . $fieldNameStr . ' from {{etton' . $infoType . '_' .
                        $sourceId . '}}')->execute();

                Yii::app()->db->createCommand()->setText('drop table if exists {{etton' . $infoType . '_' .
                        $sourceId .
                        '}} ')->execute();
            }
            //delete related record
            $relatedTableNameArr = array('field_config', 'map_product_user', 'map_product_group', 'user_query', 'user_template');
            foreach($relatedTableNameArr as $relatedTableName)
            {
                Yii::app()->db->createCommand()->setText('delete from {{' . $relatedTableName . '}} where product_id =' . $sourceId)->execute();
            }
            //delete be merged product
            Yii::app()->db->createCommand()->setText('delete from {{product}} where id =' . $sourceId)->execute();
        }
        catch(Exception $e)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $e->getMessage();
            return $resultInfo;
        }

        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    private static function checkMergeable($sourceId, $disId)
    {
        $resultInfo = array();
        $resultInfo['detail'] = '';
        $sourceProductInfo = Product::model()->findByPk($sourceId);
        $disProductInfo = Product::model()->findByPk($disId);
        $productDiffAttributeArr = array('bug_priority', 'bug_severity', 'case_priority', 'solution_value');
        $productCheckResultStr = '';
        //check if the product's setting is same
        foreach($productDiffAttributeArr as $attr)
        {
            if(0 < count(array_diff(CommonService::splitStringToArray(',', $sourceProductInfo[$attr]),
                                    CommonService::splitStringToArray(',', $disProductInfo[$attr]))))
            {
                $productCheckResultStr .= '[' . Product::model()->getAttributeLabel($attr) . ']' . Yii::t('Common', 'is different') . "\n";
            }
        }
        if('' != $productCheckResultStr)
        {
            $resultInfo['detail'] = "Product:\n" . $productCheckResultStr;
        }

        //check field config setting
        $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
        foreach($infoTypeArr as $infoType)
        {
            $disFieldConfigInfoArr = FieldConfig::model()->findAllByAttributes(array('product_id' => $disId, 'type' => $infoType, 'is_dropped' => '0'));
            $sourceFieldConfigInfoArr = FieldConfig::model()->findAllByAttributes(array('product_id' => $sourceId, 'type' => $infoType, 'is_dropped' => '0'));
            $fieldCheckResultStr = '';
            $fieldNameArr = CommonService::splitStringToArray(',', FieldConfigService::getActiveCustomFieldName($disId, $infoType));
            foreach($sourceFieldConfigInfoArr as $sourceFieldInfo)
            {
                if(!in_array($sourceFieldInfo['field_name'], $fieldNameArr))
                {
                    $fieldCheckResultStr .= "[{$sourceFieldInfo['field_name']}]" . Yii::t('Common', 'is not needed, please disable it first') . "\n";
                }
            }
            foreach($disFieldConfigInfoArr as $fieldInfo)
            {
                $fieldCheckResultStr .= self::checkSingleField($fieldInfo, $sourceFieldConfigInfoArr);
            }
            if('' != $fieldCheckResultStr)
            {
                $resultInfo['detail'] .= ucfirst($infoType) . ":\n" . $fieldCheckResultStr;
            }
        }
        if('' != $resultInfo['detail'])
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        }
        return $resultInfo;
    }

    private static function checkSingleField($fieldInfo, $sourceFieldArr)
    {
        $checkResultStr = '';
        $sourceMatchedInfo = array();
        foreach($sourceFieldArr as $souceFieldInfo)
        {
            if($souceFieldInfo['field_name'] == $fieldInfo['field_name'])
            {
                $sourceMatchedInfo = $souceFieldInfo;
                break;
            }
        }
        if(empty($sourceMatchedInfo))
        {
            $checkResultStr = "[{$fieldInfo['field_name']}]" . Yii::t('Common', 'is not existed') . "\n";
        }
        else
        {
            $compareAttributeArr = array('field_type', 'edit_in_result', 'validate_rule');
            foreach($compareAttributeArr as $attribute)
            {
                if(trim($sourceMatchedInfo[$attribute]) != trim($fieldInfo[$attribute]))
                {
                    $checkResultStr.='[' . $fieldInfo['field_name'] .
                            '][' . FieldConfig::model()->getAttributeLabel($attribute) .
                            '] ' . Yii::t('Common', 'is different') . "\n";
                }
            }

            if(0 < count(array_diff(CommonService::splitStringToArray(',', $sourceMatchedInfo['field_value']),
                                    CommonService::splitStringToArray(',', $fieldInfo['field_value']))))
            {
                $checkResultStr.='[' . $fieldInfo['field_name'] .
                        '][' . FieldConfig::model()->getAttributeLabel('field_value') . '] ' . Yii::t('Common', 'is different') . "\n";
            }

            if(FieldConfig::VALIDATION_RULE_MATCH == $fieldInfo['validate_rule'])
            {
                if($fieldInfo['match_expression'] != $sourceMatchedInfo['match_expression'])
                {
                    $checkResultStr.='[' . $fieldInfo['field_name'] .
                            '][' . FieldConfig::model()->getAttributeLabel('match_expression') . '] ' . Yii::t('Common', 'is different') . "\n";
                }
            }
        }
        return $checkResultStr;
    }

    public static function getActiveProductIdNameArr()
    {
        $idNameArr = array();

        $productInfoArr = Yii::app()->db->createCommand()
                        ->select('id,name')
                        ->from('{{product}}')
                        ->where('is_dropped="0"')
                        ->order('display_order desc')
                        ->queryAll();
        foreach($productInfoArr as $productInfo)
        {
            $idNameArr[$productInfo['id']] = $productInfo['name'];
        }
        return $idNameArr;
    }

    public static function getProductCustomFieldLink($productId)
    {
        $returnStr = '';
        $typeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
        foreach($typeArr as $type)
        {
            $returnStr .= '<a class="with_underline" href="' . Yii::app()->createUrl('fieldConfig/index',
                            array('product_id' => $productId, 'type' => $type)) . '">' . ucfirst($type) . '</a>|';
        }
        $returnStr = rtrim($returnStr, '|');
        return $returnStr;
    }

    public static function isProductEditable($productId)
    {
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
        {
            return true;
        }
        if(!empty($productId))
        {
            $productManagerIdArr = self::getProductManagerIds($productId);
            if(in_array(Yii::app()->user->id, $productManagerIdArr))
            {
                return true;
            }
        }
        return false;
    }

    public static function getProductGroupOption($productId)
    {
        $groupIdArr = self::getProductGroupIdArr($productId);
        $groupNameArr = array();
        $groupInfos = Yii::app()->db->createCommand()
                        ->select('name')
                        ->from('{{user_group}}')
                        ->where(array('in', 'id', $groupIdArr))
                        ->order('id')
                        ->queryAll();

        foreach($groupInfos as $groupInfo)
        {
            $groupNameArr[] = $groupInfo['name'];
        }
        return CHtml::dropDownList('productGroupList', '', $groupNameArr, array('style' => 'width:100%;'));
    }

    public static function getProductManagerOption($productId)
    {
        $managerArr = CommonService::splitStringToArray(',', self::getProductManagers($productId));
        return CHtml::dropDownList('productManagerList', '', $managerArr, array('style' => 'width:100%;'));
    }

    public static function disableProduct($productId, $isDropped)
    {
        $resultInfo = array();
        $product = self::loadModel($productId);
        $oldRecordAttributs['is_dropped'] = $product->is_dropped;
        $product->is_dropped = $isDropped;
        if(!$product->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $product->getErrors();
        }
        else
        {
            $addActionResult = AdminActionService::addActionNotes('product', BugfreeModel::ACTION_EDIT,
                            array('is_dropped' => $isDropped, 'id' => $productId), $oldRecordAttributs);
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        }
        return $resultInfo;
    }

    public static function copyProduct($sourceId, $params)
    {
        $resultInfo = array();
        $resultInfo = self::editProduct($params);
        if(CommonService::$ApiResult['SUCCESS'] == $resultInfo['status'])
        {
            $copyResult = self::copyProductFieldConfig($sourceId, $resultInfo['detail']['id']);
            if(CommonService::$ApiResult['FAIL'] == $copyResult['status'])
            {
                $resultInfo = $copyResult;
            }
        }
        return $resultInfo;
    }

    private static function copyProductFieldConfig($sourceProductId, $newProductId)
    {
        $resultInfo = array();
        try
        {
            $userId = Yii::app()->user->id;
            Yii::app()->db->createCommand()->setText('insert into {{field_config}} ' .
                    '(created_at,created_by,updated_at,updated_by,field_name,field_type,' .
                    'field_value,default_value,is_dropped,field_label,type,belong_group,display_order,' .
                    'editable_action,validate_rule,match_expression,product_id,edit_in_result,result_group,' .
                    'lock_version,is_required) ' .
                    'select now(),' . $userId . ',now(),' . $userId . ',field_name,field_type,' .
                    'field_value,default_value,is_dropped,field_label,type,belong_group,display_order,' .
                    'editable_action,validate_rule,match_expression,' . $newProductId . ',edit_in_result,result_group,' .
                    '1,is_required from {{field_config}} where ' .
                    'product_id =' . $sourceProductId)->execute();
            $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
            foreach($infoTypeArr as $info)
            {
                Yii::app()->db->createCommand()->setText('drop table if exists {{etton' . $info . '_' .
                        $newProductId .
                        '}} ')->execute();
                Yii::app()->db->createCommand()->setText('create table {{etton' . $info . '_' .
                        $newProductId .
                        '}} like {{etton' . $info . '_' . $sourceProductId . '}}')->execute();
            }
        }
        catch(Exception $e)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = $e->getMessage();
            return $resultInfo;
        }
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    public static function editProduct($params)
    {
        $resultInfo = array();
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(isset($params['id']))
        {
            $product = self::loadModel((int) $params['id']);
            $oldRecordAttributs = $product->attributes;
            $oldRecordAttributs['product_manager'] = $product->product_manager;
            if(!empty($product->group_name))
            {
                $oldRecordAttributs['group_name'] = join(',', $product->group_name);
            }
            $actionType = BugfreeModel::ACTION_EDIT;
        }
        else
        {
            $product = new Product();
        }

        if(!ProductService::isProductEditable($product['id']))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }

        try
        {
            $product->attributes = $params;
            if(!$product->save())
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = $product->getErrors();
            }
            else
            {
                Yii::app()->db->createCommand()->delete('{{map_product_group}}',
                        'product_id=:productId', array(':productId' => $product->id));
                if(!empty($params['group_name']))
                {
                    foreach($params['group_name'] as $groupId)
                    {
                        $mapProductGroup = new MapProductGroup();
                        $mapProductGroup->product_id = $product->id;
                        $mapProductGroup->user_group_id = $groupId;
                        $mapProductGroup->save();
                    }
                }

                Yii::app()->db->createCommand()->delete('{{map_product_user}}',
                        'product_id=:productId', array(':productId' => $product->id));
                if('' != trim($params['product_manager']))
                {
                    $managerNameArr = CommonService::splitStringToArray(",", $params['product_manager']);
                    foreach($managerNameArr as $managerName)
                    {
                        $userInfo = TestUser::model()->findByAttributes(array('realname' => $managerName,
                                    'is_dropped' => CommonService::$TrueFalseStatus['FALSE']));
                        if($userInfo !== null)
                        {
                            $mapProductUser = new MapProductUser();
                            $mapProductUser->product_id = $product->id;
                            $mapProductUser->test_user_id = $userInfo->id;
                            $mapProductUser->save();
                        }
                        else
                        {
                            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                            $resultInfo['detail'] = array('product_manager' => '[' . $managerName . ']' . Yii::t('TestUser', self::ERROR_USER_NOT_FOUND));
                            return $resultInfo;
                        }
                    }
                }
                $newRecord = self::loadModel($product->id);
                if(!empty($newRecord->group_name))
                {
                    $newRecord->group_name = join(',', $newRecord->group_name);
                }
                $addActionResult = AdminActionService::addActionNotes('product', $actionType, $newRecord, $oldRecordAttributs);
                if(!isset($params['id']))
                {
                    FieldConfigService::createAddOnTable($product->id);
                }
                $transaction->commit();
                $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
                $resultInfo['detail'] = array('id' => $product->id);
            }
            return $resultInfo;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = $e->getMessage();
        }
        return $resultInfo;
    }

    public static function getProductGroupIdArr($productId)
    {
        $groupIdArr = array();
        $groupInfos = MapProductGroup::model()->findAllByAttributes(array('product_id' => $productId));
        foreach($groupInfos as $groupInfo)
        {
            $groupIdArr[] = $groupInfo['user_group_id'];
        }
        return $groupIdArr;
    }

    public static function getProductManagerIds($productId)
    {
        $managerIds = array();
        $managerInfos = MapProductUser::model()->findAllByAttributes(array('product_id' => $productId));
        foreach($managerInfos as $managerInfo)
        {
            $managerIds[] = $managerInfo['test_user_id'];
        }
        return $managerIds;
    }

    public static function getProductManagers($productId)
    {
        $managerNameStr = '';
        $managerIds = self::getProductManagerIds($productId);
        foreach($managerIds as $managerId)
        {
            $userInfo = TestUser::model()->findByPk($managerId);
            if($userInfo != null)
            {
                $managerNameStr .= $userInfo->realname . ",";
            }
        }
        if('' != $managerNameStr)
        {
            $managerNameStr = substr($managerNameStr, 0, strlen($managerNameStr) - 1);
        }
        return $managerNameStr;
    }

    public static function getProductAllFieldInfo($type, $productId)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{field_config}}')
                        ->where('type = :type and product_id = :productId and is_dropped = :isDropped',
                                array(':type' => $type,
                                    ':productId' => $productId,
                                    ':isDropped' => CommonService::$TrueFalseStatus['FALSE']))
                        ->order('belong_group,display_order desc')
                        ->queryAll();
        return $searchResult;
    }

    public static function getSearchableCostomField($type, $productId)
    {
        $searchCustomFieldArr = array();
        $customFieldArr = self::getProductAllFieldInfo($type, $productId);
        $searchOperatorArr = FieldConfigService::getFieldTypeOperatorMapping();
        foreach($customFieldArr as $fieldInfo)
        {
            $searchCustomFieldArr[$fieldInfo['field_name']] = array('label' => $fieldInfo['field_label'],
                'type' => $searchOperatorArr[$fieldInfo['field_type']], 'isBasic' => false);
            if((FieldConfig::FIELD_TYPE_MULTISELECT == $fieldInfo['field_type'])||
                    (FieldConfig::FIELD_TYPE_SINGLESELECT == $fieldInfo['field_type']))
            {
                $searchCustomFieldArr[$fieldInfo['field_name']]['value'] = FieldConfigService::getSelectOption($fieldInfo['field_value']);
                array_unshift($searchCustomFieldArr[$fieldInfo['field_name']]['value'], '');
            }
        }
        return $searchCustomFieldArr;
    }

    public static function getBugSeverityOption($productId)
    {
        $severityArr = Yii::app()->user->getState('bug_severity_' . $productId);
        if(empty($severityArr))
        {
            $severityArr = self::getPriSevOption($productId, 'bug_severity');
            Yii::app()->user->setState('bug_severity_' . $productId, $severityArr);
        }
        return $severityArr;
    }

    public static function getBugSeverityValueByName($productId, $severityName)
    {
        $severityArr = self::getBugSeverityOption($productId);
        foreach($severityArr as $key => $value)
        {
            if($value == $severityName)
            {
                return $key;
            }
        }
        return 0;
    }

    public static function getBugPriorityOption($productId)
    {
        $bugPriorityArr = Yii::app()->user->getState('bug_priority_' . $productId);
        if(empty($bugPriorityArr))
        {
            $bugPriorityArr = self::getPriSevOption($productId, 'bug_priority');
            Yii::app()->user->setState('bug_priority_' . $productId, $bugPriorityArr);
        }
        return $bugPriorityArr;
    }

    public static function getBugPriorityValueByName($productId, $priorityName)
    {
        $priorityArr = self::getBugPriorityOption($productId);
        foreach($priorityArr as $key => $value)
        {
            if($value == $priorityName)
            {
                return $key;
            }
        }
        return 0;
    }

    public static function getCasePriorityOption($productId)
    {
        $casePriorityArr = Yii::app()->user->getState('case_priority_' . $productId);
        if(empty($casePriorityArr))
        {
            $casePriorityArr = self::getPriSevOption($productId, 'case_priority');
            Yii::app()->user->setState('case_priority_' . $productId, $casePriorityArr);
        }
        return $casePriorityArr;
    }

    public static function getCasePriorityValueByName($productId, $priorityName)
    {
        $priorityArr = self::getCasePriorityOption($productId);
        foreach($priorityArr as $key => $value)
        {
            if($value == $priorityName)
            {
                return $key;
            }
        }
        return 0;
    }

    private static function getPriSevOption($productId, $fieldName)
    {
        $productInfo = self::loadModel($productId);
        $optionStr = $productInfo[$fieldName];
        $optionArr = CommonService::splitStringToArray(',', $optionStr);
        $resultArr = array();
        $resultArr[''] = '';
        $optionCount = count($optionArr);
        for($i = 0; $i < $optionCount; $i++)
        {
            $resultArr[$i + 1] = $optionArr[$i];
        }
        return $resultArr;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public static function loadModel($id)
    {
        $model = Product::model()->findByPk((int) $id);
        if($model === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        else
        {
            $model->group_name = self::getProductGroupIdArr((int) $id);
            $model->product_manager = self::getProductManagers((int) $id);
        }

        return $model;
    }

    public static function getPermissionTable()
    {
        $groupArr = self::getGroupAdminUser();
        $sql = 'select {{product}}.name as product_name,{{user_group}}.name as group_name,
            user_group_id from {{product}},{{user_group}},(select user_group_id,product_id from
            {{map_product_group}})tmp where {{product}}.is_dropped="0" and
            tmp.product_id={{product}}.id and {{user_group}}.id = tmp.user_group_id order by {{product}}.display_order desc,user_group_id';
        $productGroupArr = array();
        $productGroupInfos = Yii::app()->db->createCommand($sql)->queryAll();
        foreach($productGroupInfos as $productGroup)
        {
            $groupAdminUserStr = '';
            if(isset($groupArr[$productGroup['user_group_id']]))
            {
                $groupAdminUserStr = $groupArr[$productGroup['user_group_id']];
            }
            $productGroupArr[$productGroup['product_name']][$productGroup['group_name']] = $groupAdminUserStr;
        }
        $tableStr = '<table style="border-collapse:collapse;" border="1" cellspaceing="0" cellpadding="0" border-collapse="collapse">
            <tr style="background-color:rgb(222,222,222);"><td style="width:160px;">产品</td><td><strong>用户组</strong>:&nbsp;&nbsp;&nbsp;&nbsp;管理员</td></tr>';
        foreach($productGroupArr as $productname=>$groups)
        {
            $tableStr .= '<tr><td>'.$productname.'</td><td>';
            foreach($groups as $groupname=>$admin)
            {
                if(!empty($admin))
                {
                    $tableStr .= '<strong>'.$groupname.'</strong>:&nbsp;&nbsp;&nbsp;&nbsp;'.$admin.'<br/>';
                }
                else
                {
                    $tableStr .= '<strong>'.$groupname.'</strong><br/>';
                }
                
            }
            $tableStr .= '</td></tr>';
        }
        $tableStr .= '</table>';
        return $tableStr;
    }

    private static function getGroupAdminUser()
    {
        $sql = 'select realname,test_user_id,user_group_id from {{test_user}},
            (select test_user_id,user_group_id from {{map_user_group}} where is_admin="1" and
            user_group_id not in (select id from {{user_group}} where is_dropped="1")) tmp where
            {{test_user}}.id=tmp.test_user_id order by user_group_id';
        $userInfos = Yii::app()->db->createCommand($sql)->queryAll();
        $groupArr = array();
        foreach($userInfos as $userInfo)
        {
            if(isset($groupArr[$userInfo['user_group_id']]))
            {
                $groupArr[$userInfo['user_group_id']] .= $userInfo['realname'].'&nbsp;&nbsp;';
            }
            else
            {
                $groupArr[$userInfo['user_group_id']] = $userInfo['realname'].'&nbsp;&nbsp;';
            }
            
        }
        return $groupArr;
    }

}

?>
