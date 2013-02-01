<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ResultAction
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "result_action".
 *
 * The followings are the available columns in table 'result_action':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $action_type
 * @property string $action_note
 * @property integer $resultinfo_id
 *
 * The followings are the available model relations:
 * @property ResultInfo $resultinfo
 * @property ResultHistory[] $resultHistories
 */
class ResultAction extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @return ResultAction the static model class
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
        return '{{result_action}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created_at, created_by, action_type, resultinfo_id', 'required'),
            array('created_by, resultinfo_id', 'numerical', 'integerOnly' => true),
            array('action_type', 'length', 'max' => 255),
            array('action_note', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, created_at, created_by, action_type, action_note, resultinfo_id', 'safe', 'on' => 'search'),
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
            'resultinfo' => array(self::BELONGS_TO, 'ResultInfo', 'resultinfo_id'),
            'resultHistories' => array(self::HAS_MANY, 'ResultHistory', 'resultaction_id'),
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
            'resultinfo_id' => 'Resultinfo',
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('created_by', $this->created_by);
        $criteria->compare('action_type', $this->action_type, true);
        $criteria->compare('action_note', $this->action_note, true);
        $criteria->compare('resultinfo_id', $this->resultinfo_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}