<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of AdminActionService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class AdminActionService
{

    /**
     * add action notes
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $tableName              target table name
     * @param   string  $action                 edit action
     * @param   array   $newRecordAttributes    new record attributes
     * @param   array   $oldRecordAttributs     old record attributes
     * @return  array                           add action notes result
     */
    public static function addActionNotes($tableName, $action, $newRecordAttributes, $oldRecordAttributs)
    {
        $infoAction = new AdminAction();
        $infoAction->action_type = $action;
        $infoAction->target_table = $tableName;
        $infoAction->target_id = $newRecordAttributes['id'];
        if(!$infoAction->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $infoAction->getErrors();
            return $resultInfo;
        }
        else
        {
            if(!empty($oldRecordAttributs))
            {
                $diffAttributeArr = self::compareRecord($oldRecordAttributs, $newRecordAttributes);
                foreach($diffAttributeArr as $diffInfo)
                {
                    $actionHistory = new AdminHistory();
                    $actionHistory->action_field = $diffInfo[0];
                    $actionHistory->adminaction_id = $infoAction->id;
                    $actionHistory->old_value = $diffInfo[1];
                    $actionHistory->new_value = $diffInfo[2];
                    if(!$actionHistory->save())
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'] = $actionHistory->getErrors();
                        return $resultInfo;
                    }
                }
            }
        }

        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        $resultInfo['detail'] = array('id' => $infoAction->id);
        return $resultInfo;
    }

    /**
     * compare attributes
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $oldRecordAttributs     old record attributes
     * @param   array   $newRecord              new attributes
     * @return  array                           compare result array
     */
    private static function compareRecord($oldRecordAttributs, $newRecord)
    {
        $ignoreAttributeArr = array('lock_version', 'updated_at', 'updated_by');
        $diffResultArr = array();
        foreach($oldRecordAttributs as $key => $value)
        {
            if(!in_array($key, $ignoreAttributeArr) && $value != $newRecord[$key])
            {
                $diffResultArr[] = array($key, $value, $newRecord[$key]);
            }
        }
        return $diffResultArr;
    }

    public static function getDetailLink($actionId, $actionType)
    {
        if(BugfreeModel::ACTION_EDIT == $actionType)
        {
            return '<a class="with_underline" href="javascript:showDetail(' . $actionId . ')">' . Yii::t('Common', 'Detail') . '</a>';
        }
        else
        {
            return '';
        }
    }

    /**
     * get detail content
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int     $actionId               action id
     * @return  string                          get action's detail content
     */
    public static function getDetailContent($actionId)
    {
        $historyArr = AdminHistory::model()->findAllByAttributes(array('adminaction_id' => $actionId));
        $returnStr = '';
        $trClass = 'odd';
        foreach($historyArr as $history)
        {
            $returnStr .= '<tr class="' . $trClass . '">';
            $returnStr .='<td>' . $history['action_field'] . '</td><td>' . $history['old_value'] .
                    '</td><td>' . $history['new_value'] . '</td>';
            $returnStr .= '</tr>';
            $trClass = ('odd' == $trClass) ? 'even' : 'odd';
        }
        return $returnStr;
    }

}

?>
