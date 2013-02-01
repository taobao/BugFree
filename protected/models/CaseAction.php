<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of CaseAction
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "case_action".
 *
 * The followings are the available columns in table 'case_action':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $action_type
 * @property string $action_note
 * @property integer $caseinfo_id
 *
 * The followings are the available model relations:
 * @property CaseInfo $caseinfo
 * @property CaseHistory[] $caseHistories
 */
class CaseAction extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @return CaseAction the static model class
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
        return '{{case_action}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created_at, created_by, action_type, caseinfo_id', 'required'),
            array('created_by, caseinfo_id', 'numerical', 'integerOnly' => true),
            array('action_type', 'length', 'max' => 255),
            array('action_note', 'safe')
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
            'caseinfo' => array(self::BELONGS_TO, 'CaseInfo', 'caseinfo_id'),
            'caseHistories' => array(self::HAS_MANY, 'CaseHistory', 'caseaction_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'action_type' => 'Action Type',
            'action_note' => 'Action Note',
            'caseinfo_id' => 'Caseinfo',
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
}