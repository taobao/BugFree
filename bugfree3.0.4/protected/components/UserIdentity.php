<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserIdentity
 * ldap validation first, if the ladp connect failed, use database to validate instead
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserIdentity extends CUserIdentity
{
    const ERROR_CONNECT = 10001;
    const ERROR_USER_DISABLED = 10002;
    const ERROR_USER_REGIST_FAILED = 10003;
    const ERROR_USER_NOT_FOUND = 10004;
    const ERROR_LDAP_MISS = 10005;
    private $_id;

    /**
     * authenticate
     * if user account is not existed, register it automatically
     * if ladp connect failed, use the database data to validate
     * after each ldap validation, update the user information to database
     *
     */
    public function authenticate()
    {
        $user = TestUser::model()->findByAttributes(array('username' => $this->username));
        if($user == null)
        {
            $this->errorCode = self::ERROR_USER_NOT_FOUND;
        }
        else
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == $user->is_dropped)
            {
                $this->errorCode = self::ERROR_USER_DISABLED;
                return!$this->errorCode;
            }
            if(TestUser::$Authmode['ldap'] == $user->authmode)
            {
                $ldap = new LdapService($this->username, $this->password);
                $userInfoArr = $ldap->search();
                if(LdapService::ERROR_LDAP_MISS == $ldap->errorCode)
                {
                    $this->errorCode = self::ERROR_LDAP_MISS;
                }
                else if((LdapService::ERROR_CONNECT == $ldap->errorCode) ||
                        (LdapService::ERROR_BIND == $ldap->errorCode))
                {
                    if(md5($this->password) !== $user->password)
                    {
                        $this->errorCode = self::ERROR_PASSWORD_INVALID;
                    }
                    else
                    {
                        $this->_id = $user->id;
                        $this->username = $user->username;
                        $this->setState('realname', $user->realname);
                        $this->setState('username', $user->username);
                        $this->errorCode = self::ERROR_NONE;
                    }
                }
                else if(LdapService::ERROR_NONE == $ldap->errorCode)
                {
                    if(empty($userInfoArr))
                    {
                        $this->errorCode = self::ERROR_PASSWORD_INVALID;
                    }
                    else
                    {
                        $userInfo = $userInfoArr;
                        $userInfo['id'] = $user->id;
                        $userInfo['password'] = $this->password;
                        $result = TestUserService::editUser($userInfo, TestUserService::LDAP_UPDATE_USER);
                        if(CommonService::$ApiResult['SUCCESS'] == $result['status'])
                        {
                            $userNew = TestUser::model()->findByPk($user->id);
                            $newRealName = $userNew['realname'];
                            $this->_id = $user->id;
                            $this->errorCode = self::ERROR_NONE;
                            $this->setState('realname', $newRealName);
                            $this->setState('username', $user->username);
                        }
                        else
                        {
                            $this->errorCode = self::ERROR_PASSWORD_INVALID;
                        }
                    }
                }
            }
            else
            {
                if(md5($this->password) !== $user->password)
                {
                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                }
                else
                {
                    $this->_id = $user->id;
                    $this->username = $user->username;
                    $this->setState('realname', $user->realname);
                    $this->setState('username', $user->username);
                    $this->errorCode = self::ERROR_NONE;
                }
            }
        }
        return!$this->errorCode;
    }

    /**
     * api authenticate
     * use the database data to validate
     *
     */
    public function apiAuthenticate()
    {
        $user = TestUser::model()->findByAttributes(array('username' => $this->username));
        if($user === null)
        {
            $this->errorCode = self::ERROR_USER_NOT_FOUND;
        }
        else
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == $user->is_dropped)
            {
                $this->errorCode = self::ERROR_USER_DISABLED;
                return!$this->errorCode;
            }
            if($this->password !== $user->password)
            {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            else
            {
                $this->_id = $user->id;
                $this->username = $user->username;
                $this->setState('realname', $user->realname);
                $this->errorCode = self::ERROR_NONE;
            }
        }
        return!$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

}

?>