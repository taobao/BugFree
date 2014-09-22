<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of SearchController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class SearchController extends Controller
{

    /**
     * access rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'userList',
                    'mark',
                    'deleteTemplateOrQuery',
                    'getProductModule',
                    'getChildModuleSelect',
                    'setExpand',
                    'getAcValue',
                    'setMyQueryDiv',
                    'infoImport',
                    'checkTemplate',
                    'getModuleOwner',
                    'getPreNextId'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
        );
    }

    public function actiongetPreNextId()
    {
        $preId = '';
        $nextId = '';
        $preDisabled = '';
        $nextDisabled = '';
        $needToConfirmStr = '$needToConfirm = false;';
        $id = (int) $_GET['id'];
        $infoType = $_GET['type'];
        $productId = (int) $_GET['product_id'];
        $preNextSql = Yii::app()->user->getState($productId . '_' . $infoType . '_prenextsql');
        if(isset($id) && isset($preNextSql))
        {
            $idSearchResult = Yii::app()->db->createCommand($preNextSql)->queryAll();
            list($preId, $nextId) = InfoService::getPreNextValue($idSearchResult, $id);
        }


        if('' == $preId)
        {
            $preDisabled = 'disabled';
        }
        if('' == $nextId)
        {
            $nextDisabled = 'disabled';
        }
        $listStr = CHtml::button(Yii::t('Common', 'Previous') . '(P)',
                        array('onclick' => $needToConfirmStr . 'location.href="' .
                            Yii::app()->createUrl('info/edit',
                                    array('type' => $infoType,
                                        'id' => $preId)) .
                            '"', 'class' => 'btn', 'disabled' => $preDisabled,
                            'accesskey' => 'P'));
        $listStr .= CHtml::button(Yii::t('Common', 'Next') . '(N)',
                        array('onclick' => $needToConfirmStr . 'location.href="' .
                            Yii::app()->createUrl('info/edit',
                                    array('type' => $infoType,
                                        'id' => $nextId)) .
                            '"', 'class' => 'btn', 'disabled' => $nextDisabled,
                            'accesskey' => 'N'));

        echo $listStr;
    }

    public function actionCheckTemplate()
    {
        $type = $_GET['type'];
        $title = $_GET['title'];
        $productId = $_GET['product_id'];
        $existedTemplate = UserTemplate::model()->findByAttributes(array('title' => $title,
                    'type' => $type, 'product_id' => $productId, 'created_by' => Yii::app()->user->id));
        if($existedTemplate != null)
        {
            echo 'existed';
        }
        else
        {
            echo '';
        }
    }

    public function actionGetModuleOwner()
    {
        $moduleId = $_GET['module_id'];
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        $owner = '';
        if($moduleInfo != null && !empty($moduleInfo->owner))
        {
            $ownerInfo = TestUser::model()->findByPk($moduleInfo->owner);
            if($ownerInfo != null)
            {
                $owner = $ownerInfo->realname;
            }
        }
        echo $owner;
    }

    public function actionGetAcValue()
    {
        $dataStr = $_GET['data'];
        $q = $_GET['q'];
        $limit = $_GET['limit'];
        $valueStr = FieldConfigService::getAutoCompleteValueStr($dataStr, $q, $limit);
        $valueArr = CommonService::splitStringToArray(',', $valueStr);
        $returnStr = '';
        $i = 0;
        foreach($valueArr as $valueTmp)
        {
            if($i < $limit)
            {
                if('' == $q || (false !== strpos(strtolower($valueTmp), strtolower($q))))
                {
                    $returnStr .= $valueTmp . '|' . $valueTmp . "\n";
                    $i++;
                }
            }
            else
            {
                break;
            }
        }
        echo $returnStr;
    }

    public function actionUserList()
    {
        $q = '';
        $type = 'realname';
        if(!empty($_GET['q']))
        {
            $q = strtolower($_GET['q']);
        }
        if(!empty($_GET['type']))
        {
            $type = $_GET['type'];
        }
        $prefixId = 0;
        if(!empty($_GET['p']))
        {
            $prefixId = $_GET['p'];
        }
        $items = TestUserService::getUserList($q, $type);
        $items = TestUserService::handleActiveClose($prefixId, $q, $items);
        foreach($items as $key => $value)
        {
            echo "$key|$value\n";
        }
    }

    public function actionMark()
    {
        $type = $_GET['type'];
        $infoId = $_GET['id'];
        $isAdd = $_GET['isAdd'];
        $markModelName = 'MapUser' . ucfirst($type);
        if('1' == $isAdd)
        {
            $markInfo = new $markModelName();
            $markInfo->test_user_id = Yii::app()->user->id;
            $markInfo->info_id = $infoId;
            if(!$markInfo->save())
            {
                echo CommonService::$ApiResult['FAIL'] . ' error:' . CJSON::encode($markInfo->getErrors());
            }
            else
            {
                echo CommonService::$ApiResult['SUCCESS'];
            }
        }
        else
        {
            if('bug' == $type)
            {
                $markInfo = MapUserBug::model()->findByAttributes(array('info_id' => $infoId, 'test_user_id' => Yii::app()->user->id));
            }
            elseif('case' == $type)
            {
                $markInfo = MapUserCase::model()->findByAttributes(array('info_id' => $infoId, 'test_user_id' => Yii::app()->user->id));
            }
            elseif('result' == $type)
            {
                $markInfo = MapUserResult::model()->findByAttributes(array('info_id' => $infoId, 'test_user_id' => Yii::app()->user->id));
            }
            if($markInfo !== null)
            {
                $markInfo->delete();
            }
            echo CommonService::$ApiResult['SUCCESS'];
        }
    }

    public function actionDeleteTemplateOrQuery()
    {
        $id = $_GET['id'];
        $type = $_GET['type'];
        if('template' == $type)
        {
            $tmpInfo = UserTemplate::model()->findByAttributes(array('id' => $id, 'created_by' => Yii::app()->user->id));
        }
        elseif('query' == $type)
        {
            $tmpInfo = UserQuery::model()->findByAttributes(array('id' => $id, 'created_by' => Yii::app()->user->id));
        }

        if($tmpInfo !== null)
        {
            $tmpInfo->delete();
        }
        echo CommonService::$ApiResult['SUCCESS'];
    }

    public function actionGetProductModule()
    {
        if(isset($_POST['product_id']))
        {
            $productId = $_POST['product_id'];
        }
        else
        {
            return;
        }
        $id = 0;
        if(isset($_POST['id']))
        {
            $id = $_POST['id'];
        }
        if(!empty($_POST['productmodule_id']))
        {
            $selectedModuleId = $_POST['productmodule_id'];
            if(0 == $id)
            {
                echo ProductModuleService::getSelectModuleTree($productId, $selectedModuleId);
                return;
            }
        }
        echo ProductModuleService::getAjaxModuleTree($productId, $id);
    }

    public function actionGetChildModuleSelect()
    {
        $parentModuleId = $_GET['parent_id'];
        $infoType = $_GET['type'];
        $moduleSelectId = ucfirst(strtolower($infoType)) . 'InfoView_productmodule_id';
        $moduleSelectName = ucfirst(strtolower($infoType)) . 'InfoView[productmodule_id]';
        echo CHtml::dropDownList($moduleSelectName, '',
                ProductModuleService::getChildModuleSelectOption($parentModuleId),
                array('style' => 'width:400px;',
                    'class' => 'product_module',
                    'id' => $moduleSelectId, 'onchange' => 'setAssignTo(\'' . $infoType . '\')'));
    }

    public function actionSetExpand()
    {
        if(isset($_GET['expand']))
        {
            Yii::app()->user->setState('expand', $_GET['expand']);
        }
    }

    public function actionSetMyQueryDiv()
    {
        if(isset($_GET['expand']))
        {
            Yii::app()->user->setState('my_query_div', $_GET['expand']);
        }
    }

    private function checkFileBeforeImport()
    {
        $fileElementName = 'casefilename';
        $error = '';
        if(!empty($_FILES[$fileElementName]['error']))
        {
            switch($_FILES[$fileElementName]['error'])
            {
                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;
                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = '';
            }
        }
        elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
        {
            $error = 'No file was uploaded';
        }
        else
        {
            if($_FILES[$fileElementName]['error'] == 0)
            {
                if($_FILES[$fileElementName]['tmp_name'])   // 批量导入
                {
                    $findtype = strtolower(strrchr($_FILES[$fileElementName]['name'], "."));
                    if($findtype != '.xml')
                    {
                        $error = Yii::t('Common', 'Wrong file type, please use xml file');
                    }
                }
            }
        }
        return $error;
    }

    public function actionInfoImport()
    {
        $productId = $_REQUEST['product_id'];
        $infoType = $_REQUEST['type'];
        $productModuleId = $_REQUEST['productmodule_id'];
        $error = "";
        $msg = "";
        $fileElementName = 'casefilename';
        $fileCheckResult = $this->checkFileBeforeImport();
        if('' != $fileCheckResult)
        {
            echo json_encode(array('msg' => $fileCheckResult));
            return;
        }

        $fileName = $_FILES[$fileElementName]['tmp_name'];
        $fileSize = filesize($fileName);
        if(CommonService::getMaxFileSize() < $fileSize)
        {
            echo json_encode(array('msg' => Yii::t('Common', 'Max file size exceeded')));
            return;
        }

        $importService = new ImportService();
        $msg = $importService->import($fileName, $productId, $infoType,$productModuleId);
        echo json_encode(array('error' => '', 'msg' => $msg));
        @unlink($_FILES[$fileElementName]);
    }

}

?>
