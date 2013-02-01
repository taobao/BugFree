<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of LdapService
 * ldap help class
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class LdapService
{
    const ERROR_NONE = 0;
    const ERROR_BIND = 1;
    const ERROR_CONNECT = 2;
    const ERROR_LDAP_MISS = 3;

    private $_host;
    private $_port;
    private $_base;
    private $_username;
    private $_password;
    public $errorCode;

    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;

        $ldapConfig = Yii::app()->params['ldap'];
        $this->_host = $ldapConfig['host'];
        $this->_port = $ldapConfig['port'];
        $this->_base = $ldapConfig['base'];
        $this->errorCode = self::ERROR_NONE;
    }

    /**
     * Search Ldap info
     *
     * for demo only.!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * customer should implement search function according to their's ldap server setting.
     * please make sure that the return value contains username,realname,email information
     *
     * @author                               youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $searchAccountName   the ldap search account name
     * @return  array                        user information been searched out from ldap.
     */
    function search($searchAccountName = '')
    {
        $testUserInfo = array();
        if(function_exists('ldap_connect'))
        {
            // connecting to ldap
            $ldap['conn'] = @ldap_connect($this->_host, $this->_port);
            $bind = @ldap_bind($ldap['conn']);
            // binding to ldap
            if($bind)
            {
                $ldap['bind'] = @ldap_bind($ldap['conn'], $this->_username, $this->_password);
                if($ldap['bind'])
                {
                    if('' == $searchAccountName)
                    {
                        $userInfo = explode("\\", $this->_username);
                    }
                    else
                    {
                        $userInfo = explode("\\", $searchAccountName);
                    }
                    $domainName = $userInfo[0];
                    $domainUserName = $userInfo[1];

                    $ldap['result'] = @ldap_search($ldap['conn'], $this->_base, 'sAMAccountName=' . $domainUserName);
                    if($ldap['result'])
                    {
                        // retrieve all the entries from the search result
                        $ldap['info'] = @ldap_get_entries($ldap['conn'], $ldap['result']);
                        if($ldap['info']['count'] >= '1')
                        {
                            foreach($ldap['info'] as $key => $ldap_info)
                            {
                                $domain = 'your ldap domain name';

                                if($domain == $domainName)
                                {
                                    $userInfoTmp = array();
                                    if('' == $searchAccountName)
                                    {
                                        $userInfoTmp['username'] = $this->_username;
                                    }
                                    else
                                    {
                                        $userInfoTmp['username'] = $searchAccountName;
                                    }

                                    if(!empty($ldap_info['displayname']) && !empty($ldap_info['displayname']['0']))
                                    {
                                        $userInfoTmp['realname'] = iconv('gbk', 'utf-8', $ldap_info['displayname']['0']);
                                    }

                                    if(!empty($ldap_info['mail']) && !empty($ldap_info['mail']['0']))
                                    {
                                        $userInfoTmp['email'] = $ldap_info['mail']['0'];
                                    }
                                    if(!empty($userInfoTmp['email']) && !empty($userInfoTmp['realname']))
                                    {
                                        $testUserInfo = $userInfoTmp;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $this->errorCode = self::ERROR_BIND;
                }
            }
            else
            {
                $this->errorCode = self::ERROR_CONNECT;
            }
            @ldap_close($ldap['conn']);
            return $testUserInfo;
        }
        else
        {
            $this->errorCode = self::ERROR_LDAP_MISS;
        }
    }

}

?>