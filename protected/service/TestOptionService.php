<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestOptionService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TestOptionService
{

    public static function editOption($params)
    {
        $resultInfo = array();
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(empty($params['id']))
        {
            $option = new TestOption();
        }
        else
        {
            $option = TestOption::model()->findByPk((int) $params['id']);
            $oldRecordAttributs = $option->attributes;
            $actionType = BugfreeModel::ACTION_EDIT;
            if($option === null)
            {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }

        $option->attributes = $params;
        if(!$option->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $option->getErrors();
        }
        else
        {
            $newRecord = TestOption::model()->findByPk((int) $params['id']);
            $addActionResult = AdminActionService::addActionNotes('test_option', $actionType, $newRecord, $oldRecordAttributs);
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            $resultInfo['detail'] = array('id' => $option->id);
        }
        return $resultInfo;
    }

    public static function getOptionOperation($optionId)
    {
        $optionModel = TestOption::model()->findByPk($optionId);
        if($optionModel != null && (TestOption::DB_VERSION != $optionModel['option_name']))
        {
            return '<a class="with_underline" href="'.Yii::app()->createUrl('testOption/edit',
                array('id'=>$optionId)).'">' . Yii::t('Common', 'Edit') . '</a>';
        }
        else
        {
            return '';
        }
    }

    public static function getOptionValueByName($optionName)
    {
        $optionInfo = TestOption::model()->findByAttributes(array('option_name' => $optionName));
        if($optionInfo !== null)
        {
            return $optionInfo['option_value'];
        }
        else
        {
            return '';
        }
    }

}

?>
