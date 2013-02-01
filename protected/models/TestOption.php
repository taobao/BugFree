<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestOption
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * This is the model class for table "test_option".
 *
 * The followings are the available columns in table 'test_option':
 * @property integer $id
 * @property string $option_name
 * @property string $option_value
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $lock_version
 */
class TestOption extends BugfreeModel
{
    const SYSTEM_ADMIN = 'SYSTEM_ADMIN';
    const DEFAULT_PAGESIZE = 'DEFAULT_PAGESIZE';
    const MAX_FILE_SIZE = 'MAX_FILE_SIZE';
    const QUERY_FIELD_NUMBER = 'QUERY_FIELD_NUMBER';
    const DB_VERSION = 'db_version';
    /**
     * Returns the static model of the specified AR class.
     * @return TestOption the static model class
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
        return '{{test_option}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('option_name, option_value, lock_version', 'required'),
            array('created_at, updated_at', 'safe'),
            array('created_by, updated_by, lock_version', 'numerical', 'integerOnly' => true),
            array('option_name', 'length', 'max' => 45),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'option_name' => Yii::t('AdminCommon','option_name'),
            'option_value' => Yii::t('AdminCommon','option_value'),
            'created_at' => Yii::t('AdminCommon', 'created_at'),
            'created_by' => Yii::t('AdminCommon', 'created_by'),
            'updated_at' => Yii::t('AdminCommon', 'updated_at'),
            'updated_by' => Yii::t('AdminCommon', 'updated_by'),
        );
    }
}