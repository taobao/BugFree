<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ActionHistoryService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ActionHistoryService
{

    /**
     * get file's edit info
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $type                   bug,case or result
     * @param   int     $id                     file id
     * @return  array                           edit result information.
     */
    public static function getFileEditInfos($type, $id)
    {
        $fileInfos = TestFileService::getRelatedFileInfos($type, $id);
        $addFileInfo = array();
        $deleteFileInfo = array();
        foreach($fileInfos as $fileInfo)
        {
            $addFileInfo[$fileInfo['add_action_id']][] = $fileInfo['file_title'];
            if(!empty($fileInfo['delete_action_id']))
            {
                $deleteFileInfo[$fileInfo['delete_action_id']][] = $fileInfo['file_title'];
            }
        }
        return array($addFileInfo, $deleteFileInfo);
    }

    /**
     * get file's edit action string info
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $fileEditInfos          file been edited
     * @param   int     $actionId               related action id
     * @return  string                          file action string.
     */
    private static function getActionFileEditInfoStr($fileEditInfos, $actionId)
    {
        $addFileInfo = $fileEditInfos[0];
        $deleteFileInfo = $fileEditInfos[1];
        $fileActionStr = '';
        $actionAddFileInfo = (empty($addFileInfo[$actionId])) ? array() : $addFileInfo[$actionId];
        $actionDeleteFileInfo = (empty($deleteFileInfo[$actionId])) ? array() : $deleteFileInfo[$actionId];
        $addCount = count($actionAddFileInfo);
        $deleteCount = count($actionDeleteFileInfo);
        for($i = 0; $i < $addCount; $i++)
        {
            $fileActionStr .= '<li>Added file <strong>' . $actionAddFileInfo[$i] . '</strong><li>';
        }
        for($j = 0; $j < $deleteCount; $j++)
        {
            $fileActionStr .= '<li>Delete file <strong>' . $actionDeleteFileInfo[$j] . '</strong><li>';
        }
        return $fileActionStr;
    }

    /**
     * get action log string info
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $actionInfo             action information
     * @param   string  $type                   bug,case or result
     * @param   int     $id                     related info id
     * @return  string                          info's action string
     */
    public static function getActionStr($actionInfo, $type, $id)
    {
        $actionStr = '<dl style="clear:both;text-align:left;margin:6px 3px;padding:0;"><dd style="float:left;margin:5px 0 0 4px;">' .
                self::getActionCleanContent($actionInfo, $type, $id) . '</dd>';
        return $actionStr;
    }

    public static function getActionCleanContent($actionInfo, $type, $id)
    {
        $actionStr = $actionInfo['created_at'] . '&nbsp;';
        if('bug' == $type && BugInfo::ACTION_RESOLVE == $actionInfo['action_type'])
        {
            $mixedInfo = InfoService::loadModel($type, $id);
            $bugInfo = $mixedInfo->getBasicInfo();
            $actionStr .= '<strong>' . Yii::t('BugInfo', $actionInfo['action_type']) .
                    '</strong> as <strong>' . $bugInfo['solution'] .
                    '</strong> by <strong>' . CommonService::getUserRealName($actionInfo['created_by']) . '</strong>';
        }
        else
        {
            $actionStr .= '<strong>' . Yii::t('BugInfo', $actionInfo['action_type']) .
                    '</strong> by <strong>' . CommonService::getUserRealName($actionInfo['created_by']) . '</strong>';
        }
        return $actionStr;
    }

    /**
     * get each field's action information
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $actionInfo             action information
     * @param   string  $type                   bug,case or result
     * @param   array   $fieldLabelArr          related field label array
     * @return  string                          field action string
     */
    private static function getFieldEditStr($actionInfo, $type, $fieldLabelArr, $productId)
    {
        $historyStr = '';
        $historyInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $type . '_history' . '}}')
                        ->where($type . 'action_id = :actionId',
                                array(':actionId' => $actionInfo['id']))
                        ->order('id')
                        ->queryAll();
        foreach($historyInfos as $historyInfo)
        {
            $historyStr .= '<li>' . self::getSingleHistoryStr($fieldLabelArr, $type, $productId, $historyInfo) . '</li>';
        }
        return $historyStr;
    }

    public static function getSingleHistoryStr($fieldLabelArr, $type, $productId, $historyInfo)
    {
        $singleHistoryStr = '';
        if(isset($fieldLabelArr[$historyInfo['action_field']]))
        {
            $fieldName = $fieldLabelArr[$historyInfo['action_field']];
        }
        else
        {
            //search label in Common.php if BugInfo.php not contain related label
            //ex. created_by field
            $fieldName = Yii::t('Common', Yii::t(ucfirst($type) . 'Info', $historyInfo['action_field']));
        }

        if('repeat_step' == $historyInfo['action_field'] ||
                'case_step' == $historyInfo['action_field'] ||
                'result_step' == $historyInfo['action_field'])
        {
            $historyInfo['old_value'] = htmlspecialchars($historyInfo['old_value']);
            $historyInfo['old_value'] = str_replace("\r\n", '', $historyInfo['old_value']);
            $historyInfo['old_value'] = str_replace("\n", '', $historyInfo['old_value']);

            $historyInfo['new_value'] = htmlspecialchars($historyInfo['new_value']);
            $historyInfo['new_value'] = str_replace("\r\n", '', $historyInfo['new_value']);
            $historyInfo['new_value'] = str_replace("\n", '', $historyInfo['new_value']);


            $singleHistoryStr = 'Changed <strong>' . $fieldName .
                    '</strong> from <strong onmouseover="return overlib(\'' . str_replace("'", "\\'", str_replace("\"", "\\\"", $historyInfo['old_value'])) .
                    '\',ABOVE,WIDTH,300,BGCOLOR,\'#75736E\',FGCOLOR,\'#F6F6F6\');" onmouseout="return nd();">' .
                    '"<a href="javascript:void(0);">...</a>"</strong> to <strong onmouseover="return overlib(\'' .
                    str_replace("'", "\\'", str_replace("\"", "\\\"", $historyInfo['new_value'])) . '\',ABOVE,WIDTH,300,BGCOLOR,\'#75736E\',FGCOLOR,\'#F6F6F6\');" onmouseout="return nd();">' .
                    '"<a href="javascript:void(0);" >...</a>"</strong>';
        }
        else
        {
            if('delete_flag' == $historyInfo['action_field'])
            {
                $historyInfo['old_value'] = CommonService::getTrueFalseName($historyInfo['old_value']);
                $historyInfo['new_value'] = CommonService::getTrueFalseName($historyInfo['new_value']);
            }
            else if('severity' == $historyInfo['action_field'] || 'priority' == $historyInfo['action_field'])
            {
                $nameArr = array();
                if('priority' == $historyInfo['action_field'])
                {
                    if(Info::TYPE_BUG == $type)
                    {
                        $nameArr = ProductService::getBugPriorityOption($productId);
                    }
                    else
                    {
                        $nameArr = ProductService::getCasePriorityOption($productId);
                    }
                }
                else
                {
                    $nameArr = ProductService::getBugSeverityOption($productId);
                }
                $historyInfo['old_value'] = CommonService::getNameByValue($nameArr, $historyInfo['old_value']);
                $historyInfo['new_value'] = CommonService::getNameByValue($nameArr, $historyInfo['new_value']);
            }
            $singleHistoryStr = 'Changed <strong>' . $fieldName .
                    '</strong> from <strong>"' . CHtml::encode($historyInfo['old_value']) .
                    '"</strong> to <strong>"' . CHtml::encode($historyInfo['new_value']) . '"</strong>';
        }
        return $singleHistoryStr;
    }

    /**
     * get each field's action information for api use
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $actionInfo             action information
     * @param   string  $type                   bug,case or result
     * @param   array   $fieldLabelArr          related field label array
     * @return  array                           field action array
     */
    private static function getFieldEditInfoForApi($actionInfo, $type, $fieldLabelArr)
    {
        $historyInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $type . '_history' . '}}')
                        ->where($type . 'action_id = :actionId',
                                array(':actionId' => $actionInfo['id']))
                        ->order('id')
                        ->queryAll();
        $historyCount = count($historyInfos);
        for($i = 0; $i < $historyCount; $i++)
        {
            $historyInfos[$i]['old_value'] = CHtml::encode($historyInfos[$i]['old_value']);
            $historyInfos[$i]['new_value'] = CHtml::encode($historyInfos[$i]['new_value']);
            if(isset($fieldLabelArr[$historyInfos[$i]['action_field']]))
            {
                $fieldName = $fieldLabelArr[$historyInfos[$i]['action_field']];
            }
            else
            {
                $fieldName = Yii::t('Common', Yii::t(ucfirst($type) . 'Info', $historyInfos[$i]['action_field']));
            }
            $historyInfos[$i]['filed_name'] = $fieldName;
        }
        return $historyInfos;
    }

    /**
     * get info's single action information
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $fileEditInfos          file edit information
     * @param   array   $actionInfo             action information
     * @param   string  $type                   bug,case or result
     * @param   int     $id                     info obj id
     * @param   array   $fieldLabelArr          related field label array
     * @return  string                          single action string
     */
    public static function getSingleActionHistory($fileEditInfos, $actionInfo, $type, $id, $fieldLabelArr, $productId)
    {
        $actionStr = self::getActionStr($actionInfo, $type, $id);
        $fieldEditStr = self::getFieldEditStr($actionInfo, $type, $fieldLabelArr, $productId);
        $fileActionStr = self::getActionFileEditInfoStr($fileEditInfos, $actionInfo['id']);
        $fieldFileStr = '';
        if('' != $fieldEditStr . $fileActionStr)
        {
            $fieldFileStr = '<ul style="list-style:none;margin:0;padding:0;color:#333333;">' .
                    $fieldEditStr . $fileActionStr . '</ul>';
        }
        $actionNoteStr = '';      
        if(!in_array($actionInfo['action_note'], array('','<br />','<br/>','<br>')))
        {
            $actionNoteStr = '<blockquote style="width:435px;margin:10px 1px 0px 1px;overflow-x:auto;">' . self::handDuplicateIdStr($actionInfo['action_note']) . '</blockquote>';
        }

        if('' != $fieldFileStr . $actionNoteStr)
        {
            $actionStr .= '<dd style="clear:both;width:435px;clear:both;background-color:#E6E6E6;border:1px solid #E3E3E3;margin:0 0 0 4px;padding:5px;">' .
                    $fieldFileStr . $actionNoteStr . '</dd>';
        }
        $actionStr .= '</dl>';
        return $actionStr;
    }

    public static function handDuplicateIdStr($duplicateStr)
    {
        $basicUrlStr = Yii::app()->createAbsoluteUrl('info/edit', array('type' => Info::TYPE_BUG, 'id' => ''));
        $search = array(
            "/^Bug #<a href=\"Bug\.php\?BugID=(\d+)\"/si",
            "/^Bug #<a href=\"index\.php\?r=info\/edit&type=bug&id=(\d+)\"/si",
            "/^Bug #<a href=\"\/index\.php\?r=info\/edit&type=bug&id=(\d+)\"/si",
            "/^Bug #<a href=\"\/[^\"]*?\/bug\/(\d+)\"/si"
        );
        $replace = "Bug #<a href=\"" . $basicUrlStr . "\\1\"";
        $duplicateStr = preg_replace($search, $replace, $duplicateStr);
        return $duplicateStr;
    }

    /**
     * get info's action information
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $type                   bug,case or result
     * @param   int     $id                     info obj id
     * @param   int     $productId              product id
     * @return  string                          info's action string
     */
    public static function getActionHistory($type, $id, $productId)
    {
        $returnStr = '';
        $fieldLabelArr = FieldConfigService::getCustomFieldLabel($type, $productId);
        $actionInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $type . '_action' . '}}')
                        ->where($type . 'info_id = :id',
                                array(':id' => $id))
                        ->order('id desc')
                        ->queryAll();
        $fileEditInfos = self::getFileEditInfos($type, $id);
        foreach($actionInfos as $actionInfo)
        {
            $returnStr .= self::getSingleActionHistory($fileEditInfos, $actionInfo, $type, $id, $fieldLabelArr, $productId);
        }
        return $returnStr;
    }

    public static function getInfoActionForApi($type, $id, $productId)
    {
        $fieldLabelArr = FieldConfigService::getCustomFieldLabel($type, $productId);
        $actionInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $type . '_action' . '}}')
                        ->where($type . 'info_id = :id',
                                array(':id' => $id))
                        ->order('id desc')
                        ->queryAll();
        list($addFileInfos, $deleteFileInfos) = self::getFileEditInfos($type, $id);
        $actionCount = count($actionInfos);
        for($i = 0; $i < $actionCount; $i++)
        {
            $actionInfos[$i]['action_note'] = CHtml::encode($actionInfos[$i]['action_note']);
            $actionInfos[$i]['created_by_name'] = CommonService::getUserRealName($actionInfos[$i]['created_by']);
            $actionInfos[$i]['action_history'] = self::getFieldEditInfoForApi($actionInfos[$i], $type, $fieldLabelArr);
            $actionInfos[$i]['added_file'] = array();
            $actionInfos[$i]['deleted_file'] = array();
            if(isset($addFileInfos[$actionInfos[$i]['id']]))
            {
                $actionInfos[$i]['added_file'] = $addFileInfos[$actionInfos[$i]['id']];
            }
            if(isset($deleteFileInfos[$actionInfos[$i]['id']]))
            {
                $actionInfos[$i]['deleted_file'] = $deleteFileInfos[$actionInfos[$i]['id']];
            }
        }
        return $actionInfos;
    }

}

?>
