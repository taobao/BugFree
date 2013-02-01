<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestUserService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TestUserService
{
    const ADMIN_EDIT_USER = 'admin';
    const LDAP_UPDATE_USER = 'ldap';
    const SELF_EDIT_USER = 'self';

    /**
     * Create or update user information
     * @param array() $userInfoArr the user related information
     * @return array() create result. return detail information
     */
    public static function editUser($userInfoArr, $pageActionType)
    {
        $resultInfo = array();
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(!empty($userInfoArr['id']))
        {
            $user = self::loadModel($userInfoArr['id']);
            if(isset($userInfoArr['realname']) && self::isRealnameExisted($userInfoArr['id'], $userInfoArr['realname']))
            {
                $userInfoArr['realname'] = $userInfoArr['realname'] . '[' . $userInfoArr['username'] . ']';
            }
            $oldRecordAttributs = $user->attributes;
            $actionType = BugfreeModel::ACTION_EDIT;
            $user->attributes = $userInfoArr;
            if(!empty($userInfoArr['change_password']) && (CommonService::$TrueFalseStatus['TRUE'] == $userInfoArr['change_password']))
            {
                $user->scenario = 'password';
            }
        }
        else
        {
            $user = new TestUser();
            $user->attributes = $userInfoArr;
            if(TestUser::$Authmode['ldap'] == $_POST['TestUser']['authmode'])
            {
                $ldap = new LdapService(Yii::app()->params->ldap['user'], Yii::app()->params->ldap['pass']);
                if(empty($userInfoArr['username']))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail']['id'] = Yii::t('TestUser', 'username can not be blank');
                    return $resultInfo;
                }
                $ldapUserInfo = $ldap->search($userInfoArr['username']);
                if(empty($ldapUserInfo))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail']['id'] = Yii::t('TestUser', 'Domain Account not found');
                    return $resultInfo;
                }
                if(self::isRealnameExisted(0, $ldapUserInfo['realname']))
                {
                    $ldapUserInfo['realname'] = $ldapUserInfo['realname'] . '[' . $ldapUserInfo['username'] . ']';
                }
                $user->attributes = $ldapUserInfo;
                $user->password = time();
            }
            $user->is_dropped = CommonService::$TrueFalseStatus['FALSE'];
            $user->email_flag = CommonService::$TrueFalseStatus['TRUE'];
            $user->wangwang_flag = CommonService::$TrueFalseStatus['FALSE'];
        }

        if(!self::isUserEditable($user->id, $pageActionType))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }

        if($user->save())
        {
            $newRecord = self::loadModel($user->id);
            $addActionResult = AdminActionService::addActionNotes('test_user', $actionType, $newRecord, $oldRecordAttributs);
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            $resultInfo['detail'] = array('id' => $user->id);
            return $resultInfo;
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $user->getErrors();
        }
        return $resultInfo;
    }

    private static function isRealnameExisted($userId, $realname)
    {
        $userInfos = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('{{test_user}}')
                        ->where('realname=:realName and id<>:userId',
                                array(':realName' => $realname, ':userId' => $userId))
                        ->queryAll();
        if(count($userInfos) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getAuthModeOptions()
    {
        return array(
            TestUser::$Authmode['ldap'] => Yii::t('TestUser', 'Domain Account'),
            TestUser::$Authmode['internal'] => Yii::t('TestUser', 'Internal Account')
        );
    }

    public static function getModeMessage($mode)
    {
        if(TestUser::$Authmode['ldap'] == $mode)
        {
            return Yii::t('TestUser', 'Domain Account');
        }
        else
        {
            return Yii::t('TestUser', 'Internal Account');
        }
    }

    public static function getUserOperation($userId, $createdById, $authmode, $isDropped)
    {
        $returnStr = '';
        if(self::isAdminEditable($userId, $createdById))
        {
            if(TestUser::$Authmode['internal'] == $authmode)
            {
                $returnStr .= '<a class="with_underline" href="' .
                        Yii::app()->createUrl('testUser/adminedit',
                                array('id' => $userId)) . '">' . Yii::t('Common', 'Edit') . '</a>';
            }
            if($userId != Yii::app()->user->id)
            {
                if('' != $returnStr)
                {
                    $returnStr .= '|';
                }
                $returnStr .= '<a class="with_underline disable_user" href="javascript:void(0)" user_id=' . $userId . ' drop_status="' . $isDropped . '">';
                if(CommonService::$TrueFalseStatus['TRUE'] == $isDropped)
                {
                    $returnStr .= Yii::t('Common', 'Enable') . '</a>';
                }
                else
                {
                    $returnStr .= Yii::t('Common', 'Disable') . '</a>';
                }
            }
        }
        return $returnStr;
    }

    private static function isAdminEditable($userId, $createdBy)
    {
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin') ||
                (Yii::app()->user->id == $userId) ||
                (Yii::app()->user->id == $createdBy))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function isUserEditable($userId, $actionType = 'admin')
    {
        if(empty($userId))
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin') ||
                    CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_manager'))
            {
                return true;
            }
        }
        else
        {
            $userInfo = TestUser::model()->findByPk($userId);
            if($userInfo != null)
            {
                if(self::ADMIN_EDIT_USER == $actionType)
                {
                    if(self::isAdminEditable($userId, $userInfo->created_by))
                    {
                        return true;
                    }
                }
                else if(self::LDAP_UPDATE_USER == $actionType)
                {
                    return true;
                }
                else
                {
                    if($userId == Yii::app()->user->id)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getUserGroupNameArr($userId)
    {
        $groupNameArr = '';
        $groupIds = MapUserGroup::model()->findAllByAttributes(array('test_user_id' => $userId));
        for($i = 0; $i < count($groupIds); $i++)
        {
            $groupInfo = UserGroup::model()->findByPk($groupIds[$i]->user_group_id);
            $groupNameArr[] = $groupInfo->name;
        }
        return $groupNameArr;
    }

    public static function loadModel($id)
    {
        $model = TestUser::model()->findByPk((int) $id);
        if($model === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        else
        {
            $model->password = '';
            $model->group_name = self::getUserGroupNameArr($model->id);
        }
        return $model;
    }

    public static function isManager($userId)
    {
        $managedProductIdArr = self::getManagedProduct($userId);
        if(!empty($managedProductIdArr))
        {
            return CommonService::$TrueFalseStatus['TRUE'];
        }
        $managedGroupIdArr = UserGroupService::getManagedGroup($userId);
        if(!empty($managedGroupIdArr))
        {
            return CommonService::$TrueFalseStatus['TRUE'];
        }
        return CommonService::$TrueFalseStatus['FALSE'];
    }

    public static function getManagedProduct($userId)
    {
        $mapInfos = MapProductUser::model()->findAllByAttributes(array('test_user_id' => $userId));
        $productIdArr = array();
        foreach($mapInfos as $info)
        {
            $productIdArr[] = $info['product_id'];
        }
        sort($productIdArr);
        return $productIdArr;
    }

    public static function isSystemAdmin($userId)
    {
        $userInfo = TestUser::model()->findByPk($userId);
        $systemAdminStr = TestOption::model()->findByAttributes(array('option_name' => TestOption::SYSTEM_ADMIN));
        $systemAdminArr = array();
        if($systemAdminStr !== null && (!empty($systemAdminStr['option_value'])))
        {
            $systemAdminArr = CommonService::splitStringToArray(",", $systemAdminStr['option_value']);
        }
        if(in_array($userInfo['username'], $systemAdminArr))
        {
            return CommonService::$TrueFalseStatus['TRUE'];
        }
        else
        {
            return CommonService::$TrueFalseStatus['FALSE'];
        }
    }

    public static function getAccessableProduct($userId)
    {
        $productInfos = array();
        if(self::isSystemAdmin($userId))
        {
            $productInfos = Yii::app()->db->createCommand()
                            ->select('id,name')
                            ->from('{{product}}')
                            ->where('is_dropped = "0"')
                            ->order('display_order desc')
                            ->queryAll();
        }
        else
        {
            $productIdArr = array();
            $userGroups = self::getUserBelongGroups($userId);
            $userGroupIdArr = array();
            foreach($userGroups as $userGroupInfo)
            {
                $userGroupIdArr[] = (int) $userGroupInfo['user_group_id'];
            }

            //add allusergroup to the search condition
            $userGroupIdArr[] = Yii::app()->params->allUserGroupId;

            $productIds = Yii::app()->db->createCommand()
                            ->selectDistinct('product_id')
                            ->from('{{map_product_group}}')
                            ->where(array('in', 'user_group_id', $userGroupIdArr))
                            ->order('product_id')
                            ->queryAll();
            foreach($productIds as $productTmp)
            {
                $productIdArr[] = $productTmp['product_id'];
            }
            $accessAbleProductIdArr = array_merge($productIdArr, self::getManagedProduct($userId));
            $accessAbleProductIdArr = array_unique($accessAbleProductIdArr);

            $productInfos = Yii::app()->db->createCommand()
                            ->select('id,name')
                            ->from('{{product}}')
                            ->where(array('and', 'is_dropped = "0"', array('in', 'id', $accessAbleProductIdArr)))
                            ->order('display_order desc')
                            ->queryAll();
        }

        return $productInfos;
    }

    public static function getUserGroupOption($userId)
    {
        $userModel = self::loadModel($userId);
        $mapUserGroups = $userModel->mapUserGroups;
        $userGroupNameArr = array();
        foreach($mapUserGroups as $mapInfo)
        {
            $userGroupNameArr[] = $mapInfo->userGroup->name;
        }
        return CHtml::dropDownList('groupList', '', $userGroupNameArr, array('style' => 'width:150px;'));
    }

    public static function getUserBelongGroups($userId)
    {
        $userGroups = MapUserGroup::model()->findAllByAttributes(array('test_user_id' => $userId));
        return $userGroups;
    }

    public static function getUserList($keyWord, $type='realname')
    {
        $keyWord = '%' . $keyWord . '%';
        $users = Yii::app()->db->createCommand()
                        ->select('id,realname,email,username')
                        ->from('{{test_user}}')
                        //->where('is_dropped = \'0\' and (username like :keyWord or realname like :keyWord or email like :keyWord)', array(':keyWord' => $keyWord))
                        ->where("id>0 and is_dropped = '0' and (first_pinyin like :keyWord or full_pinyin like :keyWord or username like :keyWord or realname like :keyWord)", array(':keyWord' => $keyWord))
                        ->order('username')
                        ->queryAll();
        $userArr = array();
        if('realname' == $type)
        {
            foreach($users as $user)
            {
                $userArr[$user['realname']] = $user['realname'] . '[' . $user['username'] . ']';
            }
        }
        else
        {
            foreach($users as $user)
            {
                $userArr[$user['id']] = $user['realname'] . '[' . $user['username'] . ']';
            }
        }

        return $userArr;
    }

    public static function handleActiveClose($prefixId, $queryStr, $items)
    {
        $resultItems = array();
        if(TestUser::ACTIVE_USER_ID == $prefixId)
        {
            if(empty($items))
            {
                $resultItems[TestUser::ACTIVE_USER_NAME] = TestUser::ACTIVE_USER_NAME;
            }
            elseif(preg_match('/' . $queryStr . '/i', TestUser::ACTIVE_USER_NAME))
            {
                $resultItems[TestUser::ACTIVE_USER_NAME] = TestUser::ACTIVE_USER_NAME;
            }
        }
        else if(TestUser::CLOSE_USER_ID == $prefixId)
        {
            if(empty($items))
            {
                $resultItems[TestUser::CLOSE_USER_NAME] = TestUser::CLOSE_USER_NAME;
            }
            elseif(preg_match('/' . $queryStr . '/i', TestUser::CLOSE_USER_NAME))
            {
                $resultItems[TestUser::CLOSE_USER_NAME] = TestUser::CLOSE_USER_NAME;
            }
        }
        else if(TestUser::ACTIVE_CLOSE_USER_ID == $prefixId)
        {
            if(empty($items))
            {
                $resultItems[TestUser::ACTIVE_USER_NAME] = TestUser::ACTIVE_USER_NAME;
                $resultItems[TestUser::CLOSE_USER_NAME] = TestUser::CLOSE_USER_NAME;
            }
            else
            {
                if(preg_match('/' . $queryStr . '/i', TestUser::ACTIVE_USER_NAME))
                {
                    $resultItems[TestUser::ACTIVE_USER_NAME] = TestUser::ACTIVE_USER_NAME;
                }
                if(preg_match('/' . $queryStr . '/i', TestUser::CLOSE_USER_NAME))
                {
                    $resultItems[TestUser::CLOSE_USER_NAME] = TestUser::CLOSE_USER_NAME;
                }
            }
        }

        foreach($items as $key => $value)
        {
            $resultItems[$key] = $value;
        }

        return $resultItems;
    }

    public static function getUserInfoByRealname($realname)
    {
        $userInfo = TestUser::model()->findByAttributes(array('realname' => $realname, 'is_dropped' => '0'));
        return $userInfo;
    }

    public static function updateUserProductCookie($productId)
    {
        $productCookieKey = Yii::app()->user->id . "_product";
        $productInfo = Product::model()->findByPk($productId);
        if($productInfo === null || CommonService::$TrueFalseStatus['TRUE'] == $productInfo->is_dropped)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        else
        {
            $cookies = Yii::app()->request->getCookies();
            if(!empty($cookies[$productCookieKey]) && ($productId != $cookies[$productCookieKey]->value))
            {
                $cookie = new CHttpCookie($productCookieKey, $productId);
                $cookie->expire = time() + 60 * 60 * 24 * 30;  //30 days
                Yii::app()->request->cookies[$productCookieKey] = $cookie;
                Yii::app()->user->setState('product', $cookies[$productCookieKey]->value);
            }
        }
    }

}

?>
