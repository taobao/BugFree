<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of AdminAction
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "admin_action".
 *
 * The followings are the available columns in table 'admin_action':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $action_type
 * @property string $target_table
 * @property integer $target_id
 *
 * The followings are the available model relations:
 * @property AdminHistory[] $adminHistories
 */
class AdminAction extends CActiveRecord
{
    const ACTION_NEW = 'new';
    const ACTION_EDIT = 'edit';
    

    /**
     * Returns the static model of the specified AR class.
     * @return AdminAction the static model class
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
        return '{{admin_action}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created_by, target_id', 'required'),
            array('created_by, target_id', 'numerical', 'integerOnly' => true),
            array('action_type', 'length', 'max' => 255),
            array('target_table', 'length', 'max' => 45),
            array('created_at', 'safe')
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
            'adminHistories' => array(self::HAS_MANY, 'AdminHistory', 'adminaction_id'),
        );
    }

    protected function beforeValidate()
    {
        if($this->isNewRecord)
        {
            $this->created_at = date(CommonService::DATE_FORMAT);
            $this->created_by = Yii::app()->user->id;
        }
        return parent::beforeValidate();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'created_at' => Yii::t('AdminCommon','created_at'),
            'created_by' => Yii::t('Common','created_by'),
            'action_type' => Yii::t('AdminCommon','action_type'),
            'target_table' => Yii::t('AdminCommon','target_table'),
            'target_id' => Yii::t('AdminCommon','target_id'),
        );
    }

}