<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of CaseInfo
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "case_info".
 *
 * The followings are the available columns in table 'case_info':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $case_status
 * @property integer $assign_to
 * @property string $title
 * @property string $mail_to
 * @property string $case_step
 * @property integer $lock_version
 * @property string $related_bug
 * @property string $related_case
 * @property string $related_result
 * @property integer $productmodule_id
 * @property string $modified_by
 * @property string $delete_flag
 * @property integer $product_id
 * @property integer $priority
 *
 * The followings are the available model relations:
 * @property CaseAction[] $caseActions
 * @property ResultInfo[] $resultInfos
 */
class CaseInfo extends Info
{
    const ACTION_RUN = 'run';
    const ACTION_STEP_RUN = 'step run';

    const STATUS_ACTIVE = 'Active';
    const STATUS_BLOCKED = 'Blocked';
    const STATUS_INVESTIGATE = 'Investigating';
    const STATUS_REVIEWED = 'Reviewed';

    /**
     * Returns the static model of the specified AR class.
     * @return CaseInfo the static model class
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
        return '{{case_info}}';
    }

    protected function beforeValidate()
    {
        if($this->isNewRecord)
        {
            $this->delete_flag = CommonService::$TrueFalseStatus['FALSE'];
        }
        return parent::beforeValidate();
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('lock_version,title, product_id,case_status,assign_to_name,delete_flag,priority', 'required'),
            array('created_by,updated_by,assign_to, lock_version, productmodule_id', 'numerical', 'integerOnly' => true),
            array('case_status', 'length', 'max' => 45),
            array('related_bug','relatedBugValidator'),
            array('related_case','relatedCaseValidator'),
            array('title, related_bug, related_case, related_result', 'length', 'max' => 255),
            array('delete_flag', 'length', 'max' => 1),
            array('created_at, updated_at, mail_to, case_step, modified_by', 'safe'),
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
            'caseActions' => array(self::HAS_MANY, 'CaseAction', 'caseinfo_id'),
            'resultInfos' => array(self::HAS_MANY, 'ResultInfo', 'related_case_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'id' => Yii::t('Common', 'id'),
            'case_status' => Yii::t('CaseInfo', 'case_status'),
            'title' => Yii::t('CaseInfo', 'title'),
            'case_step' => Yii::t('CaseInfo', 'case_step'),
            'delete_flag' => Yii::t('CaseInfo', 'delete_flag'),
            'priority' => Yii::t('CaseInfo', 'priority'),
        ));
    }


    public static function getStatusOption()
    {
        return array(
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_BLOCKED => self::STATUS_BLOCKED,
            self::STATUS_INVESTIGATE => self::STATUS_INVESTIGATE,
            self::STATUS_REVIEWED => self::STATUS_REVIEWED
        );
    }

    

}