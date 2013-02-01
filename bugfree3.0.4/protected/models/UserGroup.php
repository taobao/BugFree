<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserGroup
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
/**
 * This is the model class for table "user_group".
 *
 * The followings are the available columns in table 'user_group':
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $is_dropped
 * @property integer $lock_version
 */

/**
 * The followings are the available model relations:
 * @property MapProductGroup[] $mapProductGroups
 * @property MapUserGroup[] $mapUserGroups
 */
class UserGroup extends BugfreeModel
{
    public $group_user;
    public $group_manager;
    /**
     * Returns the static model of the specified AR class.
     * @return UserGroup the static model class
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
        return '{{user_group}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, created_at, created_by, updated_at, updated_by, 
                is_dropped, group_user, group_manager, lock_version', 'required'),
            array('name', 'unique'),
            array('created_by, updated_by, lock_version', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('is_dropped', 'length', 'max' => 1)
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mapProductGroups' => array(self::HAS_MANY, 'MapProductGroup', 'user_group_id'),
            'mapUserGroups' => array(self::HAS_MANY, 'MapUserGroup', 'user_group_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('Common', 'id'),
            'name' => Yii::t('AdminCommon', 'group_name'),
            'group_user' => Yii::t('AdminCommon', 'Group User'),
            'group_manager' => Yii::t('AdminCommon', 'Group Manager'),
            'created_at' => Yii::t('AdminCommon', 'created_at'),
            'created_by' => Yii::t('AdminCommon', 'created_by'),
            'updated_at' => Yii::t('AdminCommon', 'updated_at'),
            'updated_by' => Yii::t('AdminCommon', 'updated_by'),
            'is_dropped' => Yii::t('Common', 'is_dropped'),
        );
    }

}