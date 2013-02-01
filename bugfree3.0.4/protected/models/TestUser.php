<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestUser
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * This is the model class for table "test_user".
 *
 * The followings are the available columns in table 'test_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $realname
 * @property string $email
 * @property string $wangwang
 * @property string $email_flag
 * @property string $wangwang_flag
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $is_dropped
 * @property string $authmode
 * @property integer $lock_version
 * @property string $full_pinyin
 * @property string $first_pinyin
 *
 * The followings are the available model relations:
 * @property MapProductUser[] $mapProductUsers
 * @property MapUserGroup[] $mapUserGroups
 */
class TestUser extends BugfreeModel
{

    public $password_repeat;
    public $password_old;
    public $group_name;
    public $change_password;

    const CLOSE_USER_ID = -2;
    const ACTIVE_USER_ID = -1;
    const ACTIVE_CLOSE_USER_ID = -3;
    const CLOSE_USER_NAME = 'Closed';
    const ACTIVE_USER_NAME = 'Active';

    const USER_TYPE_ACTIVE = 'active';
    const USER_TYPE_CLOSE = 'close';
    const USER_TYPE_BOTH = 'both';

    static $Authmode = array(
        'ldap' => 'ldap',
        'internal' => 'internal'
    );

    public static function getSearchUserUrl($type='')
    {
        if(self::USER_TYPE_ACTIVE == $type)
        {
            return "'" . Yii::app()->createUrl('search/userList', array('p' => -1)) . "'";
        }
        else if(self::USER_TYPE_CLOSE == $type)
        {
            return "'" . Yii::app()->createUrl('search/userList', array('p' => -2)) . "'";
        }
        else if(self::USER_TYPE_BOTH == $type)
        {
            return "'" . Yii::app()->createUrl('search/userList', array('p' => -3)) . "'";
        }
        else
        {
            return "'" . Yii::app()->createUrl('search/userList') . "'";
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * @return TestUser the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{test_user}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $basicValidation = array(
            array('password_repeat,password_old,password', 'safe'),
            array('username, realname, email_flag,
                wangwang_flag, created_at, created_by, updated_at,
                updated_by, is_dropped, authmode, lock_version', 'required'),
            array('created_by, updated_by,lock_version', 'numerical', 'integerOnly' => true),
            array('username, password, realname, email, wangwang, full_pinyin, first_pinyin', 'length', 'max' => 45),
            array('username,realname', 'unique'),
            array('email', 'email'),
            array('email_flag, wangwang_flag, is_dropped', 'length', 'max' => 1),
            array('email_flag, wangwang_flag, is_dropped', 'in', 'range' => array('0', '1')),
            array('authmode', 'length', 'max' => 8),
            array('password_old', 'confirmpassword', 'on' => 'password'),
            array('password', 'compare', 'compareAttribute' => 'password_repeat',
                'operator' => '=', 'on' => 'password')
        );
        if($this->isNewRecord)
        {
            array_push($basicValidation, array('password', 'required'));
            array_push($basicValidation, array('email', 'required'));
        }
        return $basicValidation;
    }

    protected function beforeValidate()
    {
        $this->email = trim($this->email);
        $this->wangwang = trim($this->wangwang);
        return parent::beforeValidate();
    }

    protected function afterValidate()
    {
        if(!$this->getErrors())
        {
            if($this->isNewRecord)
            {
                $this->password = md5($this->password);
                $pinyin = PinyinService::pinyin($this->realname);
                $this->full_pinyin = $pinyin[0];
                $this->first_pinyin = $pinyin[1];
            }
            else
            {
                $oldModel = TestUser::model()->findByPk($this->id);
                if('' != $this->password && ($this->password != $oldModel->password))
                {
                    $this->password = md5($this->password);
                }
                else
                {
                    $this->password = $oldModel->password;
                }
            }
        }
    }

    public function confirmpassword($attribute, $params)
    {
        $model = TestUser::model()->findByPk($this->id);
        if(md5($this->password_old) != $model->password)
        {
            $this->addError('password_old', Yii::t('TestUser', 'old password wrong'));
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mapProductUsers' => array(self::HAS_MANY, 'MapProductUser', 'test_user_id'),
            'mapUserGroups' => array(self::HAS_MANY, 'MapUserGroup', 'test_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('Common', 'id'),
            'username' => Yii::t('TestUser', 'username'),
            'password' => Yii::t('TestUser', 'password'),
            'password_old' => Yii::t('TestUser', 'password_old'),
            'password_repeat' => Yii::t('TestUser', 'password_repeat'),
            'realname' => Yii::t('TestUser', 'realname'),
            'email' => Yii::t('TestUser', 'email'),
            'wangwang' => Yii::t('TestUser', 'wangwang'),
            'email_flag' => Yii::t('TestUser', 'email_flag'),
            'wangwang_flag' => Yii::t('TestUser', 'wangwang_flag'),
            'created_at' => Yii::t('AdminCommon', 'created_at'),
            'created_by' => Yii::t('AdminCommon', 'created_by'),
            'updated_at' => Yii::t('AdminCommon', 'updated_at'),
            'updated_by' => Yii::t('AdminCommon', 'updated_by'),
            'is_dropped' => Yii::t('Common', 'is_dropped'),
            'authmode' => Yii::t('TestUser', 'authmode'),
            'full_pinyin' => Yii::t('TestUser', 'full_pinyin'),
            'first_pinyin' => Yii::t('TestUser', 'first_pinyin')
        );
    }

}