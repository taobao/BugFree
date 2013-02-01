<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of LoginForm
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    const LANGUAGE_ZH_CN = 'zh_cn';
    const LANGUAGE_EN = 'en';

    public $username;
    public $password;
    public $isapi = 0;
    public $rememberMe;
    public $language;
    const DURATION = 2592000; //3600*24*30

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('username, password, language', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
            array('isapi,language', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe' => Yii::t('LoginForm', 'Remember me'),
            'username' => Yii::t('LoginForm', 'Username'),
            'password' => Yii::t('LoginForm', 'Password'),
            'language' => Yii::t('LoginForm', 'Language')
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params)
    {
        if(!$this->hasErrors())
        {
            $identity = new UserIdentity($this->username, $this->password);
            if(0 == $this->isapi)
            {
                $identity->authenticate();
            }
            else
            {
                $identity->apiAuthenticate();
            }
            switch($identity->errorCode)
            {
                case UserIdentity::ERROR_NONE:
                    {
                        $accessableProducts = TestUserService::getAccessableProduct($identity->getId());
                        if(empty($accessableProducts))
                        {
                            $this->addError('username', Yii::t('LoginForm', 'no accessable product'));
                        }
                        else
                        {
                            $duration = 0;
                            if($this->rememberMe)
                            {
                                // keep login state duration
                                $duration = LoginForm::DURATION;
                            }
                            Yii::app()->user->login($identity, $duration);
                            UserLogService::createUserLog(array('created_by'=>Yii::app()->user->id,
                                'created_at'=>date(CommonService::DATE_FORMAT),
                                'ip'=>$_SERVER['REMOTE_ADDR']));
                            LoginService::setLanguageCookie($this->language);                           
                        }
                        break;
                    }
                case UserIdentity::ERROR_USERNAME_INVALID:
                    {
                        $this->addError('username', Yii::t('LoginForm', 'username is incorrect'));
                        break;
                    }
                case UserIdentity::ERROR_CONNECT:
                    {
                        $this->addError('username', Yii::t('LoginForm', 'ldap connect failed'));
                        break;
                    }
                case UserIdentity::ERROR_USER_DISABLED:
                    {
                        $this->addError('username', Yii::t('LoginForm', 'user disabled'));
                        break;
                    }
                case UserIdentity::ERROR_LDAP_MISS:
                    {
                        $this->addError('username', Yii::t('LoginForm', 'ldap module disabled'));
                        break;
                    }
                case UserIdentity::ERROR_USER_NOT_FOUND:
                    {
                        $this->addError('username', Yii::t('LoginForm', 
                                'user not found').'&nbsp;<a href="'.
                                Yii::app()->createUrl('site/permission').'">'.
                                Yii::t('LoginForm','permission tips').'</a>');
                        break;
                    }

                default: // UserIdentity::ERROR_PASSWORD_INVALID {
                    $this->addError('password', Yii::t('LoginForm', 'password is incorrect'));
                    break;
            }
        }
    }

}
