<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of InfoService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class InfoService
{
    const ERROR_WRONG_ACTION = 'wrong action';

    /**
     * get blank search row array
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $productName            product name
     * @return  array                           blank search row array.
     */
    public static function getBlankSearchRowArr($productName, $moduleId = 0, $infoType='')
    {
        $fullModulePath = $productName;
        if(0 != $moduleId)
        {
            $moduleInfo = ProductModule::model()->findByPk($moduleId);
            if(!empty($moduleInfo))
            {
                $fullModulePath .= ProductModule::MODULE_SPLITTER . $moduleInfo['full_path_name'];
            }
        }
        $returnArr = array(array('leftParenthesesName' => '',
                'field' => 'module_name',
                'operator' => 'UNDER',
                'value' => $fullModulePath,
                'rightParenthesesName' => '',
                'andor' => 'And'));
        if(Info::TYPE_CASE == $infoType)
        {
            $returnArr[] = array('leftParenthesesName' => '',
                'field' => 'delete_flag',
                'operator' => '=',
                'value' => Yii::t('Common', '0'),
                'rightParenthesesName' => '',
                'andor' => 'And');
        }
        return $returnArr;
    }

    /**
     * get template search row array
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  array                           template search row array.
     */
    public static function getTemplateSearchRowArr()
    {
        return array('leftParenthesesName' => '',
            'field' => 'id',
            'operator' => '=',
            'value' => '',
            'rightParenthesesName' => '',
            'andor' => '');
    }

    /**
     * parse multi select data to string
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array  $customInfo              been handled data
     * @return  array                           handled result array
     */
    private static function parseMultiSelectedData($customInfo)
    {
        $parseResultInfo = $customInfo;
        foreach($parseResultInfo as $key => $value)
        {
            if(is_array($value))
            {
                $parseResultInfo[$key] = join(',', $value);
            }
        }
        return $parseResultInfo;
    }

    private static function getUserEmail($userInfo)
    {
        if($userInfo != null)
        {
            if((CommonService::$TrueFalseStatus['TRUE'] == $userInfo['email_flag']) &&
                    !empty($userInfo['email']) &&
                    (CommonService::$TrueFalseStatus['FALSE'] == $userInfo['is_dropped']))
            {
                return $userInfo['email'];
            }
        }
        return '';
    }

    private static function getWangwang($userInfo)
    {
        if($userInfo != null)
        {
            if((CommonService::$TrueFalseStatus['TRUE'] == $userInfo->wangwang_flag) &&
                    !empty($userInfo->wangwang))
            {
                return $userInfo->wangwang;
            }
        }
        return '';
    }

    /**
     * get mail's cc list
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array  $basicInfo               basic info
     * @return  array                           cclist array
     */
    private static function getCCList($basicInfo, $assignToEmail)
    {
        $ccArr = array();
        $actionUser = TestUser::model()->findByPk(Yii::app()->user->id);
        $actionUserEmail = self::getUserEmail($actionUser);
        $isUserInCclist = false;
        if(!empty($basicInfo->mail_to))
        {
            $mailToArr = CommonService::splitStringToArray(',', $basicInfo->mail_to);
            foreach($mailToArr as $mailToUser)
            {
                if(CommonService::isEmailFormat($mailToUser))
                {
                    $ccArr[] = $mailToUser;
                }
                else
                {
                    $mailToUserInfo = TestUser::model()->findByAttributes(array('realname' => $mailToUser));
                    $userMail = self::getUserEmail($mailToUserInfo);
                    if(!empty($userMail))
                    {
                        $ccArr[] = $userMail;
                    }
                }
            }
            if(in_array($actionUserEmail, $ccArr))
            {
                $isUserInCclist = true;
            }
        }
        $modifiedUserIdArr = CommonService::splitStringToArray(',', $basicInfo->modified_by);
        foreach($modifiedUserIdArr as $modifiedId)
        {
            $modifyUser = TestUser::model()->findByPk($modifiedId);
            $modifyEmail = self::getUserEmail($modifyUser);
            if(!empty($modifyEmail))
            {
                $ccArr[] = $modifyEmail;
            }
        }

        $ccArr = array_unique($ccArr);
        if($isUserInCclist)
        {
            $ccArr = array_diff($ccArr, array($assignToEmail));
        }
        else
        {
            $ccArr = array_diff($ccArr, array($assignToEmail, $actionUserEmail));
        }

        return $ccArr;
    }

    /**
     * get message content
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array  $basicInfo               basic info
     * @param   string $infoType                bug,case or result
     * @param   int    $actionId                action id
     * @param   string $repeatStep              repeat step
     * @param   string $replyNote               reply note
     * @return  array                           message content
     */
    private static function getMessageContent($basicInfo, $infoType, $actionId, $repeatStep, $replyNote)
    {
        $infoId = $basicInfo['id'];
        $absoluteUrl = Yii::app()->createAbsoluteUrl('info/edit', array('type' => $infoType, 'id' => $infoId));
        $linkUrl = '<a target="blank" href="' . $absoluteUrl . '">[' .
                ucfirst($infoType) . ' #' . $infoId . ' => ' . CommonService::getUserRealName($basicInfo['assign_to']) .
                ']</a>';


        $msgContent = $linkUrl;
        $fileEditInfos = ActionHistoryService::getFileEditInfos($infoType, $infoId);
        $actionInfo = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $infoType . '_action' . '}}')
                        ->where('id = :id',
                                array(':id' => $actionId))
                        ->queryRow();
        $fieldLabelArr = FieldConfigService::getCustomFieldLabel($infoType, $basicInfo['product_id']);
        $msgContent .= ActionHistoryService::getSingleActionHistory($fileEditInfos, $actionInfo, $infoType, $infoId, $fieldLabelArr, $basicInfo['product_id']);
        if('' != $repeatStep)
        {
            $msgContent .= "<br/><br/>" . str_repeat("-", 20) . "<br/><br/>" . $repeatStep;
        }


        $wangwangMsg = '';
        if(Info::TYPE_BUG == $infoType)
        {
            $wangwangMsg .= 'Bug #' . $infoId . ':' . CommonService::sysSubStr($basicInfo['title'], 150, true) . "<br/>";
            $wangwangMsg .= $linkUrl;
            if(BugInfo::ACTION_RESOLVE == $actionInfo['action_type'])
            {
                $wangwangMsg .= Yii::t('BugInfo', $actionInfo['action_type']) .
                        ' as ' . $basicInfo['solution'] .
                        ' by ' . CommonService::getUserRealName($actionInfo['created_by']);
            }
            else
            {
                $wangwangMsg .= Yii::t('BugInfo', $actionInfo['action_type']) .
                        ' by ' . CommonService::getUserRealName($actionInfo['created_by']);
            }
            if($replyNote != '')
            {
                $wangwangMsg .= "<br/>" . $replyNote;
            }
        }
        return array($msgContent, $wangwangMsg);
    }

    /**
     * send mail message after action
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int    $infoId                  info id
     * @param   string $infoType                bug,case or result
     * @param   int    $actionId                action id
     * @param   string $repeatStep              repeat step
     * @param   string $replyNote               reply note
     * @return
     */
    private static function sendMessage($infoId, $infoType, $actionId, $repeatStep='', $replyNote='')
    {
        $className = ucfirst(strtolower($infoType)) . 'Info';
        $targetModel = new $className();
        $basicInfo = $targetModel->model()->findByPk($infoId);
        list($mailMsg, $wangwangMsg) = self::getMessageContent($basicInfo, $infoType, $actionId, $repeatStep, $replyNote);

        $assignToEmail = '';
        $wangwang = '';
        if(!empty($basicInfo->assign_to))
        {
            if(Yii::app()->user->id != $basicInfo->assign_to)
            {
                $assignUserInfo = TestUser::model()->findByPk($basicInfo->assign_to);
                $assignToEmail = self::getUserEmail($assignUserInfo);
                $wangwang = self::getWangwang($assignUserInfo);
            }
        }

        $ccArr = self::getCCList($basicInfo, $assignToEmail);
        MailService::sysMail($assignToEmail, $ccArr,
                        ucfirst(strtolower($infoType)) . ' #' . $infoId . ' ' .
                        CommonService::sysSubStr($basicInfo['title'], 150, true), $mailMsg);
    }

    /**
     * get repeat step
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array  $basicInfo               basic info
     * @param   string $infoType                bug,case or result
     * @return  string                          repeat step str
     */
    private static function getRepeatStep($basicInfo, $infoType)
    {
        if(empty($basicInfo['id']))
        {
            $stepName = '';
            if(Info::TYPE_BUG == $infoType)
            {
                $stepName = 'repeat_step';
            }
            else if(Info::TYPE_CASE == $infoType)
            {
                $stepName = 'case_step';
            }
            else if(Info::TYPE_RESULT == $infoType)
            {
                $stepName = 'result_step';
            }

            if(isset($basicInfo[$stepName]))
            {
                return $basicInfo[$stepName];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }
    }

    private static function handleCustomDate($infoType, $productId, $customInfo)
    {
        $dateFields = FieldConfigService::getDateField($infoType, $productId);
        foreach($customInfo as $key => $value)
        {
            if(in_array($key, $dateFields) && ('' == $value))
            {
                $customInfo[$key] = null;
            }
        }
        return $customInfo;
    }

    /**
     * Edit the related bug,case or result info object
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $infoType               bug,case or result
     * @param   string  $action                 edit action
     * @param   array   $mixedInfo              include basic and custom config info
     * @return  array                           edit result information.
     */
    public static function editInfo($infoType, $action, $mixedInfo)
    {
        $className = ucfirst(strtolower($infoType)) . 'Info';
        $targetModel = new $className();
        $basicInfoParams = $mixedInfo['basic'];
        $resultInfo = array();
        $oldRecordAttributs = array();
        $basicInfoId = 0;
        if(isset($basicInfoParams['id']))
        {
            $basicInfoId = $basicInfoParams['id'];
            $className = ucfirst(strtolower($infoType)) . 'Info';
            $targetModel = new $className();
            $basicInfo = $targetModel->model()->findByPk((int) $basicInfoParams['id']);
            $oldRecord = self::loadModel($infoType, $basicInfoParams['id']);
            $basicInfo->attributes = $oldRecord->getBasicInfo()->attributes;
            $oldRecordAttributs = array();
            $oldRecordAttributs['basic'] = $basicInfo->attributes;
            $oldRecordAttributs['custom'] = $oldRecord->getCustomInfo();
            if(Info::TYPE_BUG == $infoType)
            {
                $legalActionArr = $targetModel->model()->getLegalActionByState($basicInfo[$infoType . '_status']);
                if(!in_array($action, $legalActionArr))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail']['id'] = Yii::t('Common', self::ERROR_WRONG_ACTION);
                    return $resultInfo;
                }
            }
        }
        else
        {
            $basicInfo = new $className();
        }
        $basicInfo->attributes = $basicInfoParams;
        $basicInfo->scenario = $action;
        if(Info::ACTION_IMPORT == $action)
        {
            if(BugInfo::STATUS_RESOLVED == $basicInfo[$infoType.'_status'])
            {
                $basicInfo->scenario = BugInfo::ACTION_RESOLVE;
            }
            else if(BugInfo::STATUS_CLOSED == $basicInfo[$infoType.'_status'])
            {
                $basicInfo->scenario = BugInfo::ACTION_CLOSE;
            }
        }
              
        if((Info::TYPE_BUG == $infoType) && (Info::ACTION_IMPORT != $action))
        {
            $basicInfo->setAttribute($infoType . '_status', self::getBugStatusByAction($action));
        }
        $basicModelValid = $basicInfo->validate();
        if(!$basicModelValid)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $basicInfo->getErrors();
            return $resultInfo;
        }
        $repeatStepForMail = self::getRepeatStep($basicInfoParams, $infoType);
        $actionNoteForWangwang = '';
        if(isset($basicInfoParams['action_note']))
        {
            $actionNoteForWangwang = $basicInfoParams['action_note'];
        }

        $customParams = self::parseMultiSelectedData($mixedInfo['custom']);
        $attachmentFile = $mixedInfo['attachment_file'];
        $deletedFileIdStr = '';
        if(!empty($basicInfoParams['deleted_file_id']))
        {
            $deletedFileIdStr = $basicInfoParams['deleted_file_id'];
        }

        $productId = $basicInfo->product_id;
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try
        {
            $customValidateResult = FieldConfigService::validateCustomFieldData($infoType, $productId, $basicInfoId, $customParams, $action);
            if(CommonService::$ApiResult['SUCCESS'] == $customValidateResult['status'])
            {
                if(!$basicInfo->save(false))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'] = $basicInfo->getErrors();
                    if(CommonService::$ApiResult['FAIL'] == $customValidateResult['status'])
                    {
                        $resultInfo['detail'] = array_merge($resultInfo['detail'], $customValidateResult['detail']);
                    }
                }
                else
                {
                    $customTableName = 'etton' . $infoType . '_' . $productId;
                    $customParams[$infoType . '_id'] = $basicInfo->id;
                    $customParams = self::handleCustomDate($infoType, $productId, $customParams);
                    if(isset($basicInfoParams['id']))
                    {
                        $insertResult = Yii::app()->db
                                        ->createCommand()
                                        ->update('{{' . $customTableName . '}}',
                                                $customParams,
                                                $infoType . '_id=:infoId',
                                                array(':infoId' => $basicInfoParams['id']));
                    }
                    else
                    {
                        $insertResult = Yii::app()->db
                                        ->createCommand()
                                        ->insert('{{' . $customTableName . '}}',
                                                $customParams);
                    }
                    $addActionResult = self::addActionNotes($infoType, $action, $basicInfo, $oldRecordAttributs);

                    if(CommonService::$ApiResult['FAIL'] == $addActionResult['status'])
                    {
                        $resultInfo = $addActionResult;
                        return $resultInfo;
                    }
                    else
                    {
                        $deletedFileResult = TestFileService::dropFile($deletedFileIdStr, $addActionResult['detail']['id']);
                        $saveAttachmentResult = TestFileService::saveAttachmentFile($attachmentFile,
                                        $addActionResult['detail']['id'], $basicInfo->id, $infoType, $productId);
                        if(CommonService::$ApiResult['FAIL'] == $saveAttachmentResult['status'])
                        {
                            $resultInfo = $saveAttachmentResult;
                            return $resultInfo;
                        }
                    }
                    $transaction->commit();
                    $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
                    $resultInfo['detail'] = array('id' => $basicInfo->id);
                    if(Info::ACTION_IMPORT != $action)
                    {
                        self::sendMessage($basicInfo->id, $infoType, $addActionResult['detail']['id'], $repeatStepForMail, $actionNoteForWangwang);
                    }                  
                }
            }
            else
            {
                $resultInfo = $customValidateResult;
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

    /**
     * get bug status by action
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string $action                  info's edit action
     * @return  string                          bug's status
     */
    public static function getBugStatusByAction($action)
    {
        $status = 'Active';
        if(BugInfo::ACTION_RESOLVE == $action ||
                BugInfo::ACTION_RESOLVE_EDIT == $action)
        {
            $status = 'Resolved';
        }
        elseif(BugInfo::ACTION_CLOSE == $action ||
                BugInfo::ACTION_CLOSE_EDIT == $action)
        {
            $status = 'Closed';
        }
        return $status;
    }

    /**
     * add action note
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string $infoType                bug,case or result
     * @param   string $action                  info's edit action
     * @param   array  $basicInfo               basic information
     * @param   array  $oldRecordAttributs      old record attributes
     * @return  array                           add action note result
     */
    public static function addActionNotes($infoType, $action, $basicInfo, $oldRecordAttributs)
    {
        $actionClassName = ucfirst(strtolower($infoType)) . 'Action';
        $historyClassName = ucfirst(strtolower($infoType)) . 'History';

        $infoAction = new $actionClassName();
        $infoAction->action_note = $basicInfo->action_note;
        $infoAction->action_type = $action;
        $infoAction->setAttribute($infoType . 'info_id', $basicInfo->id);
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
                $newRecord = self::loadModel($infoType, $basicInfo->id);
                $diffAttributeArr = self::compareRecord($oldRecordAttributs, $newRecord);
                foreach($diffAttributeArr as $diffInfo)
                {
                    $actionHistory = new $historyClassName();
                    $actionHistory->action_field = $diffInfo[0];
                    $actionHistory->setAttribute($infoType . 'action_id', $infoAction->id);
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
     * get module full path name
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int    $moduleId                module id
     * @return  string                          module's full path name
     */
    private static function getModuleFullPathNameForHistory($moduleId)
    {

        if(empty($moduleId))
        {
            return '/';
        }
        else
        {
            $moduleInfo = ProductModule::model()->findByPk($moduleId);
            if($moduleInfo == null)
            {
                return '/';
            }
            else
            {
                return $moduleInfo->full_path_name;
            }
        }
    }

    /**
     * Compare info's attributes
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array    $oldRecordAttributs    old info attributes
     * @param   MixInfo  $newRecord             changed info record
     * @param   array   $mixedInfo              include basic and custom config info
     * @return  array                           compared result,contains key,old value,new value
     */
    private static function compareRecord($oldRecordAttributs, $newRecord)
    {
        $ignoreAttributeArr = array('lock_version', 'updated_at', 'updated_by',
            'modified_by', 'closed_at', 'closed_by', 'resolved_at', 'resolved_by');
        $diffResultArr = array();
        $oldBasicAttributes = $oldRecordAttributs['basic'];
        $newBasicAttributes = $newRecord->getBasicInfo()->attributes;
        $arrPeopleField = array('created_by', 'updated_by', 'resolved_by', 'closed_by', 'assign_to');
        foreach($oldBasicAttributes as $key => $value)
        {
            if(!in_array($key, $ignoreAttributeArr) && $value != $newBasicAttributes[$key])
            {
                if('productmodule_id' == $key)
                {
                    $diffResultArr[] = array($key, self::getModuleFullPathNameForHistory($value),
                        self::getModuleFullPathNameForHistory($newBasicAttributes[$key]));
                }
                elseif(in_array($key, $arrPeopleField))
                {
                    $diffResultArr[] = array($key, CommonService::getUserRealName($value),
                        CommonService::getUserRealName($newBasicAttributes[$key]));
                }
                else
                {
                    $diffResultArr[] = array($key, $value, $newBasicAttributes[$key]);
                }
            }
        }

        $oldCustomAttributes = $oldRecordAttributs['custom'];
        $newCustomAttributes = $newRecord->getCustomInfo();
        foreach($oldCustomAttributes as $key => $value)
        {
            if($value != $newCustomAttributes[$key])
            {
                $diffResultArr[] = array($key, $value, $newCustomAttributes[$key]);
            }
        }
        return $diffResultArr;
    }

    /**
     * get bug's solution options
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int     $productId              product id
     * @return  array                           bug's solution array
     */
    public static function getBugSolutionOptions($productId)
    {
        $productInfo = ProductService::loadModel($productId);
        $solutionValueStr = $productInfo->solution_value;
        $solutionArr = CommonService::splitStringToArray(',', $solutionValueStr);
        $optionArr = array();
        $optionArr[''] = '';
        foreach($solutionArr as $solution)
        {
            $optionArr[$solution] = $solution;
        }
        return $optionArr;
    }

    private static function filterCustomInfoInReactive($customInfo, $productId)
    {
        $filterResult = array();
        $fieldConfigArr = FieldConfigService::getNewBugEditableField($productId);
        foreach($customInfo as $key => $value)
        {
            if(in_array($key, $fieldConfigArr) || in_array($key, array('id', 'bug_id')))
            {
                $filterResult[$key] = $value;
            }
        }
        return $filterResult;
    }

    private static function setBugActiveModel($model)
    {
        $model->resolved_at = null;
        $model->resolved_by = null;
        $model->closed_at = null;
        $model->closed_by = null;
        $model->solution = null;
        $model->duplicate_id = null;
        return $model;
    }

    /**
     * Prepare info page
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   String          $infoType         bug,case or result
     * @param   CController     $controller       page controller
     * @param   String          $actionType       edit action
     * @param   array           $request          page request info
     * @return  array                             page information
     */
    public static function initInfoPage($infoType, $controller, $actionType, $request)
    {
        $basicModelName = ucfirst(strtolower($infoType)) . 'InfoView';
        $infoId = $request->getParam('id');
        $fileDeleteable = true;
        if('view' == $actionType)
        {
            $fileDeleteable = false;
        }
        if(isset($infoId))//update
        {
            $mixedInfo = InfoService::loadModel($infoType, $infoId, $fileDeleteable);
            $model = $mixedInfo->getBasicInfo();
            $customInfo = $mixedInfo->getCustomInfo();

            if(Info::TYPE_BUG == $infoType)
            {
                if(!in_array($actionType, BugInfo::model()->getLegalActionByState($model->getAttribute($infoType . '_status'))))
                {
                    throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
                }
                if('view' != $actionType)
                {
                    $model->setAttribute('bug_status', self::getBugStatusByAction($actionType));
                }

                if(BugInfo::ACTION_OPEN == $actionType)
                {

                }
                elseif(BugInfo::ACTION_RESOLVE == $actionType)
                {
                    $model->resolved_at = date(CommonService::DATE_FORMAT);
                    $model->resolved_by = Yii::app()->user->id;
                    $model->assign_to = $model->created_by;
                    $model->assign_to_name = CommonService::getUserRealName($model->created_by);
                }
                elseif(BugInfo::ACTION_CLOSE == $actionType)
                {
                    $model->closed_at = date(CommonService::DATE_FORMAT);
                    $model->closed_by = Yii::app()->user->id;
                    $model->assign_to = TestUser::CLOSE_USER_ID;
                    $model->assign_to_name = TestUser::CLOSE_USER_NAME;
                }
                elseif(BugInfo::ACTION_ACTIVATE == $actionType)
                {
                    $customInfo = self::filterCustomInfoInReactive($customInfo, $model->product_id);
                    $model->reopen_count += 1;
                    $model->assign_to = $model->resolved_by;
                    $model->assign_to_name = CommonService::getUserRealName($model->resolved_by);
                    $model = self::setBugActiveModel($model);
                }
                $model->scenario = $actionType;
            }
            $productId = $model->product_id;
            $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
        }
        else //new
        {
            $model = new $basicModelName();
            $customInfo = array();

            $sourceId = $request->getParam('source_id');
            $templateId = $request->getParam('template_id');
            $caseId = $request->getParam('case_id');
            $resultId = $request->getParam('result_id');
            $bugId = $request->getParam('bug_id');
            $bactchRunProductId = $request->getParam('batch_product_id');
            if(isset($sourceId)) //clone,only bug and case have this function
            {
                $mixedInfo = InfoService::loadModel($infoType, $sourceId, $fileDeleteable);
                $model = $mixedInfo->getBasicInfo();
                unset($model->id);
                $model->setIsNewRecord(true);
                $productId = $model->product_id;
                if('bug' == $infoType)
                {
                    $customInfo = FieldConfigService::getBugAvailableValueByAction($mixedInfo->getCustomInfo(), $actionType, $productId);
                    $model = self::setBugActiveModel($model);
                }
                else if('case' == $infoType)
                {
                    $customInfo = $mixedInfo->getCustomInfo();
                    unset($customInfo['id']);
                    unset($customInfo['case_id']);
                }
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            elseif(isset($templateId)) //new from template
            {
                $templateInfo = UserTemplate::model()->findByPk($templateId);
                if($templateInfo === null)
                {
                    throw new CHttpException(404, 'The requested page does not exist.');
                }
                $mixedInfo = unserialize($templateInfo['template_content']);
                $model->attributes = $mixedInfo->getBasicInfo();
                $productId = $model->product_id;
                $productInfo = Product::model()->findByPk($productId);
                $model->product_name = $productInfo->name;
                $customInfo = $mixedInfo->getCustomInfo();
                //no need custom id and info_id from template
                if(Info::TYPE_BUG == $infoType)
                {
                    $customInfo = FieldConfigService::getBugAvailableValueByAction($customInfo, $actionType, $productId);
                    $model = self::setBugActiveModel($model);
                }
                else if(Info::TYPE_CASE == $infoType)
                {
                    unset($customInfo['id']);
                    unset($customInfo['case_id']);
                }
                //unset($model->id);
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            elseif(isset($caseId)) //run case
            {
                $caseMixedInfo = InfoService::loadModel('case', $caseId, $fileDeleteable);
                $caseModel = $caseMixedInfo->getBasicInfo();
                $model->related_case_id = $caseId;
                $model->title = $caseModel->title;
                $model->product_id = $caseModel->product_id;
                $model->product_name = $caseModel->product_name;
                $model->module_name = $caseModel->module_name;
                $model->productmodule_id = $caseModel->productmodule_id;
                $model->result_step = $caseModel->case_step;
                $model->assign_to = TestUser::CLOSE_USER_ID;
                $model->assign_to_name = TestUser::CLOSE_USER_NAME;
                $productId = $caseModel->product_id;
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            elseif(isset($bugId)) //new case from bug
            {
                $bugMixedInfo = InfoService::loadModel(Info::TYPE_BUG, $bugId, $fileDeleteable);
                $bugModel = $bugMixedInfo->getBasicInfo();
                $model->related_bug = $bugId;
                $model->title = $bugModel->title;
                $model->product_id = $bugModel->product_id;
                $model->product_name = $bugModel->product_name;
                $model->priority = $bugModel->priority;
                $model->module_name = $bugModel->module_name;
                $model->productmodule_id = $bugModel->productmodule_id;
                $model->case_step = $bugModel->repeat_step;
                $model->assign_to = $bugModel->assign_to;
                $model->assign_to_name = $bugModel->assign_to_name;
                $model->mail_to = $bugModel->mail_to;
                $productId = $bugModel->product_id;
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            elseif(isset($bactchRunProductId)) //batch run case
            {
                $model->title = Yii::t('Common', 'Mutiple items');
                $model->product_id = $bactchRunProductId;
                $model->product_name = Yii::t('Common', 'Mutiple items');
                $model->module_name = Yii::t('Common', 'Mutiple items');
                $model->assign_to = TestUser::CLOSE_USER_ID;
                $model->assign_to_name = TestUser::CLOSE_USER_NAME;
                $productId = $bactchRunProductId;
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            elseif(isset($resultId)) //generate bug from result
            {
                $resultMixedInfo = InfoService::loadModel('result', $resultId, $fileDeleteable);
                $resultModel = $resultMixedInfo->getBasicInfo();
                $resultCustomInfo = $resultMixedInfo->getCustomInfo();
                $model->related_result = $resultId;
                $model->title = $resultModel->title;
                $model->module_name = $resultModel->module_name;
                $model->repeat_step = ResultStepService::removeStepResultForBug($resultModel->result_step);
                $model->product_id = $resultModel->product_id;
                $model->productmodule_id = $resultModel->productmodule_id;
                $model->assign_to = TestUser::ACTIVE_USER_ID;
                $model->assign_to_name = TestUser::ACTIVE_USER_NAME;
                $productId = $resultModel->product_id;
                $copyFieldArr = FieldConfigService::getBugCopyableFields($productId);
                foreach($copyFieldArr as $resultField)
                {
                    $copyFieldName = $resultField['field_name'];
                    if(isset($resultCustomInfo[$copyFieldName]))
                    {
                        $customInfo[$copyFieldName] = $resultCustomInfo[$copyFieldName];
                    }
                }
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
            }
            else
            {
                $productId = $request->getParam('product_id');
                $productInfo = Product::model()->findByPk($productId);
                $model->product_name = $productInfo->name;
                $selectModuleId = Yii::app()->user->getState($productId . '_' . $infoType . '_selectedModule');
                if(!empty($selectModuleId))
                {
                    $model->productmodule_id = $selectModuleId;
                    $selectedModuleInfo = ProductModule::model()->findByPk($selectModuleId);
                    if(!empty($selectedModuleInfo))
                    {
                        $model->assign_to = $selectedModuleInfo['owner'];
                        $model->assign_to_name = CommonService::getUserRealName($selectedModuleInfo['owner']);
                    }
                }
                if(Info::TYPE_BUG == $infoType)
                {
                    $model->repeat_step = $productInfo[$infoType . '_step_template'];
                }
                elseif(Info::TYPE_CASE == $infoType)
                {
                    $model->case_step = $productInfo[$infoType . '_step_template'];
                }
                $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, Info::ACTION_OPEN);
            }
            if(Info::TYPE_RESULT != $infoType)
            {
                $model->setAttribute($infoType . '_status', 'Active');
            }
            else
            {
                $model->setAttribute($infoType . '_status', ResultInfo::STATUS_COMPLETED);
            }
            if(Info::TYPE_BUG == $infoType)
            {
                $model->reopen_count = 0;
            }
            if(Info::TYPE_CASE == $infoType)
            {
                $model->delete_flag = CommonService::$TrueFalseStatus['FALSE'];
            }

            $model->attachment_file = '';
            $model->created_at = date(CommonService::DATE_FORMAT);
            $model->created_by = Yii::app()->user->id;
            $model->updated_at = '';
            $model->updated_by = '';
        }
        $model->product_id = $productId;
        $assignToName = self::parseAssignToName($model->assign_to);
        if('' != $assignToName)
        {
            $model->assign_to_name = $assignToName;
        }
        return array($productId, $model, $customInfo, $customFieldArr);
    }

    /**
     * parse special user name
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int     $assignToId             assign to user id
     * @return  string                          user name
     */
    private static function parseAssignToName($assignToId)
    {
        if(TestUser::ACTIVE_USER_ID == $assignToId)
        {
            return TestUser::ACTIVE_USER_NAME;
        }
        else if(TestUser::CLOSE_USER_ID == $assignToId)
        {
            return TestUser::CLOSE_USER_NAME;
        }
        else
        {
            return '';
        }
    }

    private static function filterCustomInfo($infoType, $dbCustomInfo, $postCustomInfo)
    {
        $diffCustomKey = array_diff_key($dbCustomInfo, $postCustomInfo);
        foreach($diffCustomKey as $key => $value)
        {
            if('id' != $key && $infoType . '_id' != $key)
            {
                $dbCustomInfo[$key] = '';
            }
        }
        $dbCustomInfo = array_merge($dbCustomInfo, $postCustomInfo);
        return $dbCustomInfo;
    }

    /**
     * Save info page
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   String          $infoType         bug,case or result
     * @param   Object          $model            basic info attributes
     * @param   array           $customInfo       custom information
     * @param   array           $attachmentFile   attachment information
     * @param   CController     $controller       save info related controller
     * @param   String          $actionType       edit action type
     * @param   array           $request          user requested info
     * @return  array                             save result info
     */
    public static function saveInfoPage($infoType, $model, $customInfo, $attachmentFile, $controller, $actionType, $request)
    {
        $basicModelName = ucfirst(strtolower($infoType)) . 'InfoView';
        $basicInfo = $request->getParam($basicModelName);
        if(Info::TYPE_RESULT == $infoType)
        {
            $basicInfo['result_step'] = ResultStepService::removeSelectFromResultStep($basicInfo['result_step']);
        }
        $model->attributes = $basicInfo;

        $productId = $basicInfo['product_id'];
        if(!empty($model->id))
        {
            $basicInfo['id'] = $model->id;
        }
        $postedCustomInfo = $request->getParam('Custom');
        if(isset($postedCustomInfo))
        {
            $customInfo = self::filterCustomInfo($infoType, $customInfo, $postedCustomInfo);
        }

        //save to template
        $templateTitleTmp = $request->getParam('templateTitle');
        if(isset($templateTitleTmp) && ('' != trim($templateTitleTmp)))
        {
            $result = InfoService::saveTemplate($basicInfo, $customInfo, $infoType, $templateTitleTmp);
            if(CommonService::$ApiResult['SUCCESS'] == $result['status'])
            {
                CommonService::testRefreshParent();
                Yii::app()->user->setFlash('successMessage', $result['detail']['id']);
            }
            else
            {
                Yii::app()->user->setFlash('failMessage', $result['detail']['id']);
            }
        }
        else
        {
            $requestIdArr = array();
            $resultId = $request->getParam('result_id');
            $caseId = $request->getParam('case_id');
            if(isset($resultId))
            {
                $requestIdArr['result_id'] = $resultId;
            }
            if(isset($caseId))
            {
                $requestIdArr['case_id'] = $caseId;
            }
            if(Info::TYPE_RESULT == $infoType && ResultInfo::ACTION_BATCH_OPEN == $actionType)
            {
                $getBatchCaseIdResult = self::getBatchCaseId($basicInfo['product_id']);
                if(CommonService::$ApiResult['FAIL'] == $getBatchCaseIdResult['status'])
                {
                    $result = $getBatchCaseIdResult;
                }
                else
                {
                    $caseIdArr = $getBatchCaseIdResult['detail'];
                    $result = self::batchCreateResult($caseIdArr, $actionType, $basicInfo, $customInfo, $attachmentFile);
                }
            }
            else
            {
                $result = self::saveInfo($infoType, $actionType, $basicInfo, $customInfo, $attachmentFile, $requestIdArr);
            }
            if(CommonService::$ApiResult['SUCCESS'] == $result['status'])
            {
                CommonService::testRefreshParent();
                CommonService::sysObFlush(CommonService::jsGoto(Yii::app()->createUrl('info/edit', array('type' => $infoType, 'id' => $result['detail']['id'])), 'parent'));
//              $controller->redirect(array('edit', 'type' => $infoType, 'id' => $result['detail']['id']));
            }
        }
        if(CommonService::$ApiResult['FAIL'] == $result['status'])
        {
            $model->addErrors($result['detail']);
            $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, $result['detail'], $customInfo);
        }
        else
        {
            $customFieldArr = FieldConfigService::getCustomFieldConfig($productId, $infoType, $controller, $actionType, array(), $customInfo);
        }
        return array($model, $customFieldArr);
    }

    /**
     * get batch operate id
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int     $productId              product id
     * @return  array                           get related case id
     */
    private static function getBatchCaseId($productId)
    {
        $result = array();
        $idSearchResult = array();
        $preNextSql = Yii::app()->user->getState($productId . '_' . Info::TYPE_CASE . '_prenextsql');
        if(!empty($preNextSql))
        {
            $idSearchResult = Yii::app()->db->createCommand($preNextSql)->queryAll();
        }
        $idNum = count($idSearchResult);
        if(0 == $idNum)
        {
            $result['status'] = CommonService::$ApiResult['FAIL'];
            $result['detail']['id'] = Yii::t('ResultInfo', 'No case selected');
        }
        else if($idNum > 100)
        {
            $result['status'] = CommonService::$ApiResult['FAIL'];
            $result['detail']['id'] = Yii::t('ResultInfo', 'The count of cases can not be more than 100');
        }
        else
        {
            $result['status'] = CommonService::$ApiResult['SUCCESS'];
            $result['detail'] = $idSearchResult;
        }
        return $result;
    }

    /**
     * batch create result
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array   $caseIdArr              case id array
     * @param   string  $actionType             action type
     * @param   array   $basicInfo              basic information
     * @param   array   $customInfo             custom information
     * @param   array   $attachmentFile         attachment file
     * @return  array                           batch create result
     */
    private static function batchCreateResult($caseIdArr, $actionType, $basicInfo, $customInfo, $attachmentFile)
    {
        $result = array();
        foreach($caseIdArr as $idInfo)
        {
            $caseInfo = CaseInfo::model()->findByPk($idInfo['id']);
            $tempBasicInfo = $basicInfo;
            $tempBasicInfo['title'] = $caseInfo->title;
            $tempBasicInfo['related_case_id'] = $caseInfo->id;
            $tempBasicInfo['productmodule_id'] = $caseInfo->productmodule_id;
            $tempBasicInfo['result_step'] = $caseInfo->case_step;
            $result = self::saveInfo(Info::TYPE_RESULT, $actionType,
                            $tempBasicInfo, $customInfo, $attachmentFile,
                            array('case_id' => $caseInfo->id));
            if(CommonService::$ApiResult['FAIL'] == $result['status'])
            {
                break;
            }
        }
        return $result;
    }

    /**
     * Save info page
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   String          $infoType         bug,case or result
     * @param   String          $actionType       edit action type
     * @param   array           $basicInfo        basic info array
     * @param   array           $customInfo       custom information
     * @param   array           $attachmentFile   attachment information
     * @param   array           $requestIdArr     user requested id info
     * @return  array                             save result info
     */
    public static function saveInfo($infoType, $actionType, $basicInfo, $customInfo, $attachmentFile, $requestIdArr = array())
    {
        $mixedInfo = array('basic' => $basicInfo, 'custom' => $customInfo, 'attachment_file' => $attachmentFile);
        $result = self::editInfo($infoType, $actionType, $mixedInfo);
        if(CommonService::$ApiResult['SUCCESS'] == $result['status'])
        {
            if(isset($requestIdArr['result_id'])) //generate bug from result
            {
                $resultModel = ResultInfo::model()->findByPk($requestIdArr['result_id']);
                $resultModel->assign_to_name = CommonService::getUserRealName($resultModel->assign_to);
                if(!empty($resultModel->related_bug))
                {
                    $resultModel->related_bug = $resultModel->related_bug . ',' . $result['detail']['id'];
                }
                else
                {
                    $resultModel->related_bug = $result['detail']['id'];
                }
                $resultModel->save();
            }
            elseif(isset($requestIdArr['case_id']))//generate result from case
            {
                $caseModel = CaseInfo::model()->findByPk($requestIdArr['case_id']);
                $oldRecordAttributs = array();
                $oldRecordAttributs['basic'] = $caseModel->attributes;
                $oldRecordAttributs['custom'] = array();
                $caseModel->assign_to_name = CommonService::getUserRealName($caseModel->assign_to);
                if(!empty($caseModel->related_result))
                {
                    $caseModel->related_result = $caseModel->related_result . ',' . $result['detail']['id'];
                }
                else
                {
                    $caseModel->related_result = $result['detail']['id'];
                }
                $caseModel->save();
                if(isset($_GET['step_run']) && (1 == $_GET['step_run']))
                {
                    $addActionResult = self::addActionNotes(Info::TYPE_CASE, CaseInfo::ACTION_STEP_RUN, $caseModel, $oldRecordAttributs);
                }
                else
                {
                    $addActionResult = self::addActionNotes(Info::TYPE_CASE, CaseInfo::ACTION_RUN, $caseModel, $oldRecordAttributs);
                }
            }
        }
        return $result;
    }

    /**
     * get Previous and next info id
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   array           $valueArr         id array been searched
     * @param   int             $currentValue     id been viewed now
     * @return  array                             pre and next info id
     */
    public static function getPreNextValue($valueArr, $currentValue)
    {
        $valueNum = count($valueArr);
        if($valueNum <= 1)
        {
            $preValue = '';
            $nextValue = '';
        }
        else
        {
            $currentValueIndex = -1;
            for($i = 0; $i < $valueNum; $i++)
            {
                if($valueArr[$i]['id'] == $currentValue)
                {
                    $currentValueIndex = $i;
                    break;
                }
            }
            if(-1 == $currentValueIndex)
            {
                $preValue = '';
                $nextValue = '';
            }
            else
            {
                if($valueNum == $currentValueIndex + 1)
                {
                    $preValue = $valueArr[$currentValueIndex - 1]['id'];
                    $nextValue = '';
                }
                elseif(0 == $currentValueIndex)
                {
                    $preValue = '';
                    $nextValue = $valueArr[$currentValueIndex + 1]['id'];
                }
                else
                {
                    $preValue = $valueArr[$currentValueIndex - 1]['id'];
                    $nextValue = $valueArr[$currentValueIndex + 1]['id'];
                }
            }
        }
        return array($preValue, $nextValue);
    }

    /**
     * get page's action button
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string          $infoType         bug,case or result
     * @param   string          $actionType       page's action now
     * @param   object          $model            basic info attributes
     * @return  string                            button list html str
     */
    public static function getButtonList($infoType, $actionType, $model)
    {
        $listStr = '';
        $infoStatus = $model->getAttribute($infoType . '_status');
        $needToConfirmStr = '$needToConfirm = false;';

        if('view' == $actionType)
        {
            $preId = '';
            $nextId = '';
            $preDisabled = 'disabled';
            $nextDisabled = 'disabled';
            $listStr .= '<span id="preNextSpan">';
            $listStr .= CHtml::button(Yii::t('Common', 'Previous') . '(P)',
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
            $listStr .= '</span>';
            $listStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            if(Info::TYPE_BUG == $infoType)
            {
                if(BugInfo::STATUS_ACTIVE == $infoStatus)
                {
                    $listStr .= CHtml::button(Yii::t('BugInfo', 'opened_edit') . '(E)',
                                    array('onclick' => $needToConfirmStr . 'location.href="' .
                                        Yii::app()->createUrl('info/edit',
                                                array('type' => $infoType,
                                                    'action' => BugInfo::ACTION_OPEN_EDIT,
                                                    'id' => $model->id)) .
                                        '"', 'class' => 'btn',
                                        'accesskey' => 'E'));
                }
                else
                {
                    $listStr .= CHtml::button(Yii::t('BugInfo',
                                            strtolower($infoStatus) . '_edit') . '(E)',
                                    array('onclick' => $needToConfirmStr . 'location.href="' .
                                        Yii::app()->createUrl('info/edit',
                                                array('type' => $infoType,
                                                    'action' => strtolower($infoStatus) . '_edit',
                                                    'id' => $model->id)) .
                                        '"', 'class' => 'btn',
                                        'accesskey' => 'E'));
                }
            }
            else
            {
                $listStr .= CHtml::button(Yii::t('BugInfo', 'opened_edit') . '(E)',
                                array('onclick' => $needToConfirmStr . 'location.href="' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => $infoType,
                                                'action' => BugInfo::ACTION_OPEN_EDIT,
                                                'id' => $model->id)) .
                                    '"', 'class' => 'btn',
                                    'accesskey' => 'E'));
            }
            if(Info::TYPE_RESULT != $infoType)
            {
                $listStr .= CHtml::button(Yii::t('Common', 'Copy') . '(C)',
                                array('onclick' => $needToConfirmStr . 'location.href="' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => $infoType,
                                                'action' => BugInfo::ACTION_OPEN,
                                                'source_id' => $model->id)) .
                                    '"', 'class' => 'btn',
                                    'accesskey' => 'C'));
            }

            if(Info::TYPE_BUG == $infoType)
            {
                $listStr .= CHtml::button(Yii::t('BugInfo', BugInfo::ACTION_NEW_CASE) . '(S)',
                                array('onclick' => $needToConfirmStr . 'openWindow("' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => Info::TYPE_CASE,
                                                'action' => CaseInfo::ACTION_OPEN,
                                                'bug_id' => $model->id)) .
                                    '","_blank")', 'class' => 'btn',
                                    'accesskey' => 'S'));


                $disableResolve = 'disabled';
                if(BugInfo::STATUS_ACTIVE == $infoStatus && 'view' == $actionType)
                {
                    $disableResolve = '';
                }
                $listStr .= CHtml::button(Yii::t('BugInfo', BugInfo::ACTION_RESOLVE) . '(R)',
                                array('onclick' => $needToConfirmStr . 'location.href="' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => $infoType,
                                                'action' => BugInfo::ACTION_RESOLVE,
                                                'id' => $model->id)) .
                                    '"', 'class' => 'btn',
                                    'disabled' => $disableResolve,
                                    'accesskey' => 'R'));

                $disableClose = 'disabled';
                if(BugInfo::STATUS_RESOLVED == $infoStatus && 'view' == $actionType)
                {
                    $disableClose = '';
                }
                $listStr .= CHtml::button(Yii::t('BugInfo', BugInfo::ACTION_CLOSE) . '(L)',
                                array('onclick' => $needToConfirmStr . 'location.href="' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => $infoType,
                                                'action' => BugInfo::ACTION_CLOSE,
                                                'id' => $model->id)) .
                                    '"', 'class' => 'btn',
                                    'disabled' => $disableClose,
                                    'accesskey' => 'L'));

                $disableActive = 'disabled';
                if((BugInfo::STATUS_CLOSED == $infoStatus || BugInfo::STATUS_RESOLVED == $infoStatus) &&
                        'view' == $actionType)
                {
                    $disableActive = '';
                }
                $listStr .= CHtml::button(Yii::t('BugInfo', BugInfo::ACTION_ACTIVATE) . '(A)',
                                array('onclick' => $needToConfirmStr . 'location.href="' .
                                    Yii::app()->createUrl('info/edit',
                                            array('type' => $infoType,
                                                'action' => BugInfo::ACTION_ACTIVATE,
                                                'id' => $model->id)) .
                                    '"', 'class' => 'btn',
                                    'disabled' => $disableActive,
                                    'accesskey' => 'A'));
            }
            elseif(Info::TYPE_CASE == $infoType)
            {
                $listStr .= CHtml::button(Yii::t('CaseInfo', CaseInfo::ACTION_RUN) . '(R)',
                                array('onclick' => $needToConfirmStr . 'openWindow("' .
                                    Yii::app()->createUrl('info/edit', array('type' => Info::TYPE_RESULT,
                                        'action' => ResultInfo::ACTION_OPEN, 'case_id' => $model->id)) .
                                    '","_blank")', 'class' => 'btn',
                                    'accesskey' => 'R'));
                $listStr .= CHtml::button(Yii::t('CaseInfo', CaseInfo::ACTION_STEP_RUN) . '(S)',
                                array('onclick' => $needToConfirmStr . 'openWindow("' .
                                    Yii::app()->createUrl('info/edit', array('type' => Info::TYPE_RESULT,
                                        'action' => ResultInfo::ACTION_OPEN, 'case_id' => $model->id, 'step_run' => 1)) .
                                    '","_blank")', 'class' => 'btn',
                                    'accesskey' => 'S'));
            }
            elseif(Info::TYPE_RESULT == $infoType)
            {
                $listStr .= CHtml::button(Yii::t('ResultInfo', ResultInfo::ACTION_OPEN) . '(B)',
                                array('onclick' => $needToConfirmStr . 'openWindow("' .
                                    Yii::app()->createUrl('info/edit', array('type' => Info::TYPE_BUG,
                                        'action' => BugInfo::ACTION_OPEN, 'result_id' => $model->id)) .
                                    '","_blank")', 'class' => 'btn',
                                    'accesskey' => 'B'));
            }
        }
        else
        {
            $listStr = CHtml::button(Yii::t('Common', 'Save') . '(S)', array('onclick' => $needToConfirmStr . 'submitForm();',
                        'class' => 'btn', 'accesskey' => 'S'));
            if('result' != $infoType)
            {
                $listStr .= '&nbsp;&nbsp;';
                $listStr .= CHtml::button(Yii::t('Common', 'Save as template') . '(T)',
                                array('onclick' => $needToConfirmStr . '$("#template_dialog").dialog("open"); return false;',
                                    'class' => 'btn', 'style' => 'width:120px;',
                                    'accesskey' => 'T'));
            }
        }
        return $listStr;
    }

    /**
     * save info page as template
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   array           $basicInfo        basic info attributes
     * @param   array           $customInfo       custom info attributes
     * @param   string          $infoType         bug,case or result
     * @param   string          $templateTitle    template title
     * @return  array                             save template result
     */
    public static function saveTemplate($basicInfo, $customInfo, $infoType, $templateTitle)
    {
        $productId = $basicInfo['product_id'];
        $mixedInfo = new MixInfo();
        $mixedInfo->setBasicInfo($basicInfo);
        $mixedInfo->setCustomInfo($customInfo);
        $templateInfo = array('template_content' => $mixedInfo,
            'product_id' => $productId,
            'type' => $infoType,
            'title' => $templateTitle);
        return UserTemplateService::editTemplate($templateInfo);
    }

    /**
     * load info object
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string          $infoType         bug,case or result
     * @param   int             $id               info object id
     * @return  array                             load data result
     */
    public static function loadRawData($infoType, $id)
    {
        $resultInfo = array();
        $mixedInfo = new MixInfo();
        $infoType = strtolower($infoType);
        $className = ucfirst($infoType) . 'InfoView';
        $targetModel = new $className();
        $model = $targetModel->model()->findByPk((int) $id);

        if(null === $model)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = Yii::t('Common', 'Requested object does not exist');
            return $resultInfo;
        }
        $modelProductId = $model->product_id;
        if(!Info::isProductAccessable($modelProductId))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = Yii::t('Common', 'No access right');
            return $resultInfo;
        }
        else
        {
            $fileInfo = TestFileService::getRelatedFileInfos($infoType, $id);
            $model->attachment_file = $fileInfo;
            $mixedInfo->setBasicInfo($model);
            $customInfo = FieldConfigService::getCustomFieldValue($model->product_id, $infoType, $id);
            if(false === $customInfo)
            {
                $customInfo = array();
            }
            $mixedInfo->setCustomInfo($customInfo);
        }
        $model->assign_to_name = CommonService::getUserRealName($model->assign_to);
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        $resultInfo['detail'] = $mixedInfo;
        return $resultInfo;
    }

    /**
     * get info object's file information
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string          $infoType         bug,case or result
     * @param   int             $id               info object id
     * @param   boolean         $fileDeleteable   set file delete link or not
     * @return  MixInfo                           load model result
     */
    public static function loadModel($infoType, $id, $fileDeleteable = true)
    {
        $rawDataInfo = self::loadRawData($infoType, $id);
        $mixedInfo = new MixInfo();
        if(CommonService::$ApiResult['SUCCESS'] == $rawDataInfo['status'])
        {
            $mixedInfo = $rawDataInfo['detail'];
        }
        else
        {
            //return $rawDataInfo;
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
        $basicModel = $mixedInfo->getBasicInfo();
        $fileInfoStr = TestFileService::getRelatedFileHtml($basicModel->attachment_file, $fileDeleteable);
        $basicModel->attachment_file = $fileInfoStr;
        $mixedInfo->setBasicInfo($basicModel);
        return $mixedInfo;
    }

    /**
     * get module's html select element for info page use
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int             $productId        product id
     * @param   int             $productModuleId  productmodule's id
     * @param   string          $infoType         bug,case or result
     * @return  string                            module's html select string
     */
    public static function getModuleSelect($productId, $productModuleId, $infoType)
    {
        $productInfo = Product::model()->findByPk($productId);
        $productName = $productInfo['name'];
        $firstLayerModuleArr = ProductModuleService::getLayer1Module($productId, $productName);

        if(!empty($productModuleId))
        {
            $fullIdArr = array();
            $fullIdArr = ProductModuleService::getFullPathId($fullIdArr, $productModuleId);
            $layer1Id = $fullIdArr[1];
        }
        else
        {
            $firstLayerIdArr = array_keys($firstLayerModuleArr);
            $layer1Id = $firstLayerIdArr[0];
        }

        $moduleSelectId = ucfirst(strtolower($infoType)) . 'InfoView_productmodule_id';
        $moduleSelectName = ucfirst(strtolower($infoType)) . 'InfoView[productmodule_id]';

        $returnStr = CHtml::dropDownList('layer1_module', $layer1Id,
                        array($productName => $firstLayerModuleArr),
                        array('style' => 'width:180px;', 'class' => 'required',
                            'onchange' => 'getChildModule($(this).val())'));

        $returnStr .= CHtml::dropDownList($moduleSelectName, $productModuleId,
                        ProductModuleService::getChildModuleSelectOption($layer1Id),
                        array('style' => 'width:400px;', 'class' => 'product_module',
                            'id' => $moduleSelectId, 'onchange' => 'setAssignTo(\'' . $infoType . '\')'));
        return $returnStr;
    }

    /**
     * get product html select element for search page use
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   int             $productId        product id
     * @param   string          $type             bug,case or result
     * @return  string                            product's html select string
     */
    public static function getProductSelect($productId, $type)
    {
        return CHtml::dropDownList('product_name',
                $productId,
                Yii::app()->user->getState('visit_product_list'),
                array('onchange' =>
                    "window.location='" . Yii::app()->createUrl('info/index', array('type' => $type)) .
                    "&product_id='+$(this).val()",
                    'style' => 'font-size:16px;'));
    }

    public static function getTemplateStr($productId, $infoType, $userId)
    {
        $createActionStr = UserTemplateService::getCreateLinkStr($productId, $infoType, $userId);
        if('' == $createActionStr)
        {
            return Yii::t('Common', 'No template can be used');
        }
        else
        {
            return "<ul>" . $createActionStr . "</ul>";
        }
    }

    public static function getRowClassByStatus($infoType, $status)
    {
        if('bug' == $infoType)
        {
            if(BugInfo::STATUS_ACTIVE == $status)
            {
                return 'bugstatus_active';
            }
            else if(BugInfo::STATUS_RESOLVED == $status)
            {
                return 'bugstatus_resolved';
            }
            else if(BugInfo::STATUS_CLOSED == $status)
            {
                return 'bugstatus_closed';
            }
        }
    }

    public static function getRowCssClassExpressionStr($infoType)
    {
        if('bug' == $infoType)
        {
            return 'InfoService::getRowClassByStatus("bug",$data["bug_status"])';
        }
        else if('result' == $infoType)
        {
            return 'InfoService::getRowClassByStatus("result",$data["result_value"])';
        }
        else
        {
            return '';
        }
    }

    /**
     * get info's relate id's html
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   string          $infoType         bug,case or result
     * @param   string          $idStr            related id string
     * @return  string                            relate id's html
     */
    public static function getRelatedIdHtml($infoType, $idStr)
    {
        $returnStr = '';
        $idArr = CommonService::splitStringToArray(',', $idStr);
        $modelName = ucfirst($infoType) . 'Info';
        $targetModel = new $modelName();
        foreach($idArr as $id)
        {
            $infoObj = $targetModel->model()->findByPk($id);
            if($infoObj != null)
            {
                if(!Info::isProductAccessable($infoObj->product_id))
                {
                    $singleLink = '<a title="' . Yii::t('Common', 'No access right') . '" href="' .
                            Yii::app()->createUrl('info/edit', array('type' => $infoType, 'id' => $id)) .
                            '" target="_blank">' . $id . '</a>';
                }
                else
                {
                    $singleLink = '<a title="' . $infoObj->title . '" href="' .
                            Yii::app()->createUrl('info/edit', array('type' => $infoType, 'id' => $id)) .
                            '" target="_blank">' . $id . '</a>';
                }
                if('' == $returnStr)
                {
                    $returnStr = $singleLink;
                }
                else
                {
                    $returnStr .= ',' . $singleLink;
                }
            }
        }
        if('' != $returnStr)
        {
            $returnStr = '<div style="word-break:break-all;word-wrap:break-word;">' . $returnStr . '</div>';
        }
        return $returnStr;
    }

}

?>
