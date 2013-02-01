<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ResultInfo
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "result_info".
 *
 * The followings are the available columns in table 'result_info':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $result_status
 * @property integer $assign_to
 * @property string $result_value
 * @property string $mail_to
 * @property string $result_step
 * @property integer $lock_version
 * @property string $related_bug
 * @property integer $productmodule_id
 * @property string $modified_by
 * @property string $title
 * @property integer $related_case_id
 * @property integer $product_id
 *
 * The followings are the available model relations:
 * @property ResultAction[] $resultActions
 * @property CaseInfo $relatedCase
 */
class ResultInfo extends Info
{
    const STATUS_COMPLETED = 'Completed';
    const STATUS_INVESTIGATE = 'Investigating';
    const STATUS_RESOLVED = 'Resolved';

    const RESULT_PASSED = 'Passed';
    const RESULT_FAILED = 'Failed';
    const RESULT_BLOCKED = 'Blocked';
    const RESULT_NA = 'N/A';

    const RESULT_STEP_SELECT_CLASS = 'result_step_select';

    const ACTION_BATCH_OPEN = 'batch_opened';

    /**
     * Returns the static model of the specified AR class.
     * @return ResultInfo the static model class
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
        return '{{result_info}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('related_case_id,lock_version, title,product_id,
                result_value,assign_to_name', 'required'),
            array('created_by, updated_by, assign_to, lock_version, productmodule_id,
                related_case_id', 'numerical', 'integerOnly' => true),
            array('result_status', 'length', 'max' => 45),
            array('result_value', 'length', 'max' => 45),
            array('related_bug, title', 'length', 'max' => 255),
            array('created_at, updated_at, mail_to, result_step, modified_by', 'safe'),
            array('module_name,action_note,product_id,attachment_file,deleted_file_id,assign_to_name', 'safe'),
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
            'resultActions' => array(self::HAS_MANY, 'ResultAction', 'resultinfo_id'),
            'relatedCase' => array(self::BELONGS_TO, 'CaseInfo', 'related_case_id'),
        );
    }

    protected function beforeValidate()
    {
        if($this->isNewRecord)
        {
            if(empty($this->related_case_id))
            {
                $this->addError('related_case_id', Yii::t('ResultInfo', 'related_case_id can not be blank'));
            }
            else
            {
                $caseModel = CaseInfo::model()->findByPk($this->related_case_id);
                if($caseModel == null)
                {
                    $this->addError('related_case_id', Yii::t('Common', 'Requested object does not exist'));
                }
                else
                {
                    $this->title = $caseModel->title;
                    $this->product_id = $caseModel->product_id;
                    $this->productmodule_id = $caseModel->productmodule_id;
                }
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'id' => Yii::t('Common', 'id'),
            'result_status' => Yii::t('ResultInfo', 'result_status'),
            'result_value' => Yii::t('ResultInfo', 'result_value'),
            'result_step' => Yii::t('ResultInfo', 'result_step'),
            'title' => Yii::t('ResultInfo', 'title'),
            'related_case_id' => Yii::t('ResultInfo', 'related_case_id')
        ));
    }

    public static function getStatusOption()
    {
        return array(
            self::STATUS_COMPLETED => self::STATUS_COMPLETED,
            self::STATUS_INVESTIGATE => self::STATUS_INVESTIGATE,
            self::STATUS_RESOLVED => self::STATUS_RESOLVED
        );
    }

    public static function getResultValueOption()
    {
        return array(
            '' => '',
            self::RESULT_PASSED => self::RESULT_PASSED,
            self::RESULT_FAILED => self::RESULT_FAILED,
            self::RESULT_BLOCKED => self::RESULT_BLOCKED,
            self::RESULT_NA => self::RESULT_NA
        );
    }

    public static function getResultValueColorConfig()
    {
        return array(
            self::RESULT_PASSED => 'green',
            self::RESULT_FAILED => 'red',
            self::RESULT_BLOCKED => 'purple',
            self::RESULT_NA => 'gray'
        );
    }

}