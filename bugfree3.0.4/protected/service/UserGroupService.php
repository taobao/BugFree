<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserGroupService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserGroupService
{

    public static function editGroup($params)
    {
        $resultInfo = array();
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(empty($params['id']))
        {
            $group = new UserGroup();
        }
        else
        {
            $group = self::loadModel($params['id']);
            $oldRecordAttributs = $group->attributes;
            if(!empty($group->group_user))
            {
                $oldRecordAttributs['group_user'] = join(',', $group->group_user);
            }
            $oldRecordAttributs['group_manager'] = $group->group_manager;
            $actionType = BugfreeModel::ACTION_EDIT;
        }

        if(!self::isGroupEditable($group->id))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }
        $group->attributes = $params;
        if(!$group->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $group->getErrors();
        }
        else
        {
            Yii::app()->db->createCommand()->delete('{{map_user_group}}',
                    'user_group_id=:groupId', array(':groupId' => $group->id));
            $managerNameArr = CommonService::splitStringToArray(',', $params['group_manager']);
            $managerIdArr = array();
            foreach($managerNameArr as $mangerName)
            {
                $managerInfo = TestUserService::getUserInfoByRealname($mangerName);
                if($managerInfo !== null)
                {
                    $managerIdArr[] = $managerInfo['id'];
                }
            }
            $allUserId = array_unique(array_merge($params['group_user'], $managerIdArr));
            foreach($allUserId as $userId)
            {
                $mapUserGroup = new MapUserGroup();
                $mapUserGroup->test_user_id = $userId;
                $mapUserGroup->user_group_id = $group->id;
                if(in_array($userId, $managerIdArr))
                {
                    $mapUserGroup->is_admin = CommonService::$TrueFalseStatus['TRUE'];
                }
                else
                {
                    $mapUserGroup->is_admin = CommonService::$TrueFalseStatus['FALSE'];
                }
                $mapUserGroup->save();
            }
            $newRecord = self::loadModel($group->id);
            if(!empty($newRecord->group_user))
            {
                $newRecord->group_user = join(',', $newRecord->group_user);
            }
            $addActionResult = AdminActionService::addActionNotes('user_group', $actionType, $newRecord, $oldRecordAttributs);
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            $resultInfo['detail'] = array('id' => $group->id);
        }
        return $resultInfo;
    }

    public static function disableUserGroup($groupId, $isDropped)
    {
        $resultInfo = array();
        if(!self::isGroupEditable($groupId))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }
        $group = self::loadModel($groupId);
        $oldRecordAttributs['is_dropped'] = $group->is_dropped;
        $group->is_dropped = $isDropped;
        if(!$group->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $group->getErrors();
        }
        else
        {
            $addActionResult = AdminActionService::addActionNotes('user_group', BugfreeModel::ACTION_EDIT,
                            array('is_dropped' => $isDropped, 'id' => $groupId), $oldRecordAttributs);
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        }
        return $resultInfo;
    }

    public static function getAllActiveGroup()
    {
        $groupInfos = Yii::app()->db->createCommand()
                        ->select('id,name')
                        ->from('{{user_group}}')
                        ->where('is_dropped = :isDropped',
                                array(':isDropped' => '0'))
                        ->order('id')
                        ->queryAll();
        $groupNameArr = array();
        foreach($groupInfos as $groupInfo)
        {
            $groupNameArr[$groupInfo['id']] = $groupInfo['name'];
        }
        return $groupNameArr;
    }

    public static function getGroupManagerIds($groupId)
    {
        if(empty($groupId))
        {
            return array();
        }
        $groupInfos = Yii::app()->db->createCommand()
                        ->select('test_user_id')
                        ->from('{{map_user_group}}')
                        ->where('is_admin = :isAdmin and user_group_id=:userGroupId',
                                array(':isAdmin' => CommonService::$TrueFalseStatus['TRUE'],
                                    ':userGroupId' => $groupId))
                        ->queryAll();
        $managerIdArr = array();
        foreach($groupInfos as $groupInfo)
        {
            $managerIdArr[] = $groupInfo['test_user_id'];
        }
        return $managerIdArr;
    }

    public static function isGroupEditable($groupId)
    {
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
        {
            return true;
        }
        if(empty($groupId))
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_manager'))
            {
                return true;
            }
        }
        else
        {
            $groupInfo = UserGroup::model()->findByPk($groupId);
            if($groupInfo != null)
            {
                if(Yii::app()->user->id == $groupInfo->created_by)
                {
                    return true;
                }
                $groupMangerIdArr = self::getGroupManagerIds($groupId);
                if(in_array(Yii::app()->user->id, $groupMangerIdArr))
                {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getGroupOperation($groupId, $createdById, $isDropped)
    {
        $returnStr = '';
        if($groupId == Yii::app()->params->allUserGroupId)
        {
            return $returnStr;
        }
        if(self::isGroupEditable($groupId))
        {
            $newIsDropped = 1 - $isDropped;
            $returnStr .= '<a class="with_underline" href="' .
                    Yii::app()->createUrl('userGroup/edit',
                            array('id' => $groupId)) . '">' . Yii::t('Common', 'Edit') . '</a>';
            $returnStr .= '|<a class="with_underline" href="'.Yii::app()->createUrl('userGroup/disable',
                            array('id' => $groupId,'is_dropped'=>$newIsDropped)) .'" onclick="return confirm(\'' .
                    Yii::t('Common', 'Are you sure?') . '\');">';
            if(CommonService::$TrueFalseStatus['TRUE'] == $isDropped)
            {
                $returnStr .= Yii::t('Common', 'Enable') . '</a>';
            }
            else
            {
                $returnStr .= Yii::t('Common', 'Disable') . '</a>';
            }
        }

        return $returnStr;
    }

    public static function getGroupManagerOption($groupId)
    {
        if($groupId == Yii::app()->params->allUserGroupId)
        {
            return '';
        }
        $managerArr = self::getGroupManager($groupId);
        $managerRealNameArr = array();
        foreach($managerArr as $manager)
        {
            $managerRealNameArr[] = $manager['realname'];
        }
        return CHtml::dropDownList('groupManagerList', '', $managerRealNameArr, array('style' => 'width:100%;'));
    }

    public static function getGroupUserOption($groupId)
    {
        if($groupId == Yii::app()->params->allUserGroupId)
        {
            return '';
        }
        $userInfos = self::getGroupUser($groupId);
        $userRealNameArr = array();
        foreach($userInfos as $user)
        {
            $userRealNameArr[] = $user['realname'];
        }
        return CHtml::dropDownList('groupUserList', '', $userRealNameArr, array('style' => 'width:100%;'));
    }

    public static function getGroupManager($groupId)
    {
        $userInfos = self::getGroupUser($groupId);
        $managerArr = array();
        foreach($userInfos as $userInfo)
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == $userInfo['is_admin'])
            {
                $managerArr[] = $userInfo;
            }
        }
        return $managerArr;
    }

    public static function getManagedGroup($userId)
    {
        $groupIdArr = array();
        $managedGroupInfo = MapUserGroup::model()->findAllByAttributes(array('test_user_id' => $userId,
                    'is_admin' => CommonService::$TrueFalseStatus['TRUE']));
        foreach($managedGroupInfo as $groupInfo)
        {
            $groupIdArr[] = $groupInfo['user_group_id'];
        }
        $createdGroups = UserGroup::model()->findAllByAttributes(array('created_by' => $userId));
        foreach($createdGroups as $createdGroupInfo)
        {
            if(!in_array($createdGroupInfo['id'], $groupIdArr))
            {
                $groupIdArr[] = $createdGroupInfo['id'];
            }
        }
        return $groupIdArr;
    }

    public static function getGroupUser($groupId)
    {
        $userMapInfos = MapUserGroup::model()->findAllByAttributes(array('user_group_id' => $groupId));
        $userIdArr = array();
        foreach($userMapInfos as $userMapInfo)
        {
            $userIdArr[$userMapInfo->test_user_id] = $userMapInfo->is_admin;
        }
        $userInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{test_user}}')
                        ->where(array('in', 'id', array_keys($userIdArr)))
                        ->order('username')
                        ->queryAll();
        $userDataArr = array();
        foreach($userInfos as $userInfo)
        {
            $userInfo['is_admin'] = $userIdArr[$userInfo['id']];
            $userDataArr[] = $userInfo;
        }
        return $userDataArr;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public static function loadModel($id)
    {
        $model = UserGroup::model()->findByPk((int) $id);
        if($model === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $groupUsers = self::getGroupUser($id);
        $groupUserNameArr = array();
        foreach($groupUsers as $userInfo)
        {
            $groupUserNameArr[$userInfo['id']] = $userInfo['realname'] . '[' . $userInfo['username'] . ']';
        }
        $model->group_user = $groupUserNameArr;
        $groupManagers = self::getGroupManager($id);
        $managerStr = '';
        foreach($groupManagers as $managerInfo)
        {
            $managerStr .= $managerInfo['realname'] . ',';
        }
        $model->group_manager = $managerStr;
        return $model;
    }

}

?>