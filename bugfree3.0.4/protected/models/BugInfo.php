<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of BugInfo
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * This is the model class for table "bug_info".
 *
 * The followings are the available columns in table 'bug_info':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $bug_status
 * @property integer $assign_to
 * @property string $title
 * @property string $mail_to
 * @property string $repeat_step
 * @property integer $lock_version
 * @property string $resolved_at
 * @property integer $resolved_by
 * @property string $closed_at
 * @property integer $closed_by
 * @property string $related_bug
 * @property string $related_case
 * @property string $related_result
 * @property integer $productmodule_id
 * @property string $modified_by
 * @property integer $duplicate_id
 * @property string $solution
 * @property integer $product_id
 * @property integer $reopen_count
 * @property integer $priority
 * @property integer $severity
 *
 * The followings are the available model relations:
 * @property BugAction[] $bugActions
 */
class BugInfo extends Info
{
    const ACTION_RESOLVE = 'resolved';
    const ACTION_RESOLVE_EDIT = 'resolved_edit';
    const ACTION_CLOSE = 'closed';
    const ACTION_CLOSE_EDIT = 'closed_edit';
    const ACTION_ACTIVATE = 'activated';
    const ACTION_NEW_CASE = 'new_case';

    const STATUS_ACTIVE = 'Active';
    const STATUS_RESOLVED = 'Resolved';
    const STATUS_CLOSED = 'Closed';

    const DUPLICATE_SOLUTION = 'Duplicate';

    const ERROR_DUPLICATE_ID = 'duplicate id is not existed';

    public static function getActions()
    {
        return array(
            self::ACTION_OPEN => Yii::t('BugInfo', self::ACTION_OPEN),
            self::ACTION_RESOLVE => Yii::t('BugInfo', self::ACTION_RESOLVE),
            self::ACTION_CLOSE => Yii::t('BugInfo', self::ACTION_CLOSE)
        );
    }

    public static function getLegalActionByState($status)
    {
        $actionArr = array();
        if(self::STATUS_ACTIVE == $status)
        {
            $actionArr = array(self::ACTION_OPEN, self::ACTION_OPEN_EDIT, self::ACTION_RESOLVE);
        }
        elseif(self::STATUS_CLOSED == $status)
        {
            $actionArr = array(self::ACTION_CLOSE_EDIT, self::ACTION_ACTIVATE);
        }
        elseif(self::STATUS_RESOLVED == $status)
        {
            $actionArr = array(self::ACTION_RESOLVE_EDIT, self::ACTION_ACTIVATE, self::ACTION_CLOSE);
        }
        $actionArr[] = 'view';
        $actionArr[] = Info::ACTION_IMPORT;
        return $actionArr;
    }

    protected function beforeSave()
    {
        if($this->isNewRecord)
        {
            $this->reopen_count = 0;
        }
        else
        {
            if(BugInfo::ACTION_ACTIVATE == $this->scenario)
            {
                $this->reopen_count += 1;
            }
        }
        if((!$this->isNewRecord) && (self::DUPLICATE_SOLUTION == $this->solution))
        {
            $oldRecord = self::model()->findByPk($this->id);
            $oldDuplicatedIdArr = CommonService::splitStringToArray(',', $oldRecord->duplicate_id);
            $duplicatedIdArr = CommonService::splitStringToArray(',', $this->duplicate_id);
            $diffDuplicatedIdArr = array_diff($duplicatedIdArr, $oldDuplicatedIdArr);
            foreach($diffDuplicatedIdArr as $dupId)
            {
                $basicInfo = new BugInfo();
                $basicInfo->id = $dupId;
                $basicInfo->action_note = 'Bug #<a href="Bug.php?BugID=' . $this->id . '" target="_blank">' . $this->id .
                        '</a> is resolved as duplicate to this bug.';
                $addActionResult = InfoService::addActionNotes(Info::TYPE_BUG,
                                Info::ACTION_OPEN_EDIT,
                                $basicInfo, array('basic' => array(), 'custom' => array()));
            }
        }
        return parent::beforeSave();
    }

    protected function beforeValidate()
    {
        if(!$this->isNewRecord)//import need provide related field value
        {
            if(BugInfo::ACTION_RESOLVE == $this->scenario)
            {
                $this->resolved_at = date(CommonService::DATE_FORMAT);
                $this->resolved_by = Yii::app()->user->id;
            }
            elseif(BugInfo::ACTION_CLOSE == $this->scenario)
            {
                $this->closed_at = date(CommonService::DATE_FORMAT);
                $this->closed_by = Yii::app()->user->id;
            }
            elseif(BugInfo::ACTION_ACTIVATE == $this->scenario)
            {
                $this->resolved_at = null;
                $this->resolved_by = null;
                $this->closed_at = null;
                $this->closed_by = null;
                $this->solution = null;
                $this->duplicate_id = null;
            }
        }
        return parent::beforeValidate();
    }

    public static function getStatusOption()
    {
        return array(
            self::STATUS_ACTIVE,
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @return BugInfo the static model class
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
        return '{{bug_info}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('lock_version, title, product_id, assign_to_name, severity, bug_status', 'required'),
            array('closed_at, closed_by', 'required', 'on' => array(self::ACTION_CLOSE, self::ACTION_CLOSE_EDIT)),
            array('solution, resolved_at, resolved_by', 'required', 'on' => array(self::ACTION_RESOLVE, self::ACTION_RESOLVE_EDIT, self::ACTION_CLOSE, self::ACTION_CLOSE_EDIT)),
            array('related_bug', 'relatedBugValidator'),
            array('related_case', 'relatedCaseValidator'),
            array('created_by, updated_by, assign_to, lock_version, resolved_by, closed_by, productmodule_id,reopen_count',
                'numerical', 'integerOnly' => true),
            array('duplicate_id', 'duplicateIdValidator', 'on' => array(self::ACTION_RESOLVE, self::ACTION_RESOLVE_EDIT, self::ACTION_CLOSE, self::ACTION_CLOSE_EDIT)),
            array('bug_status', 'length', 'max' => 45),
            array('title, related_bug, related_case, related_result', 'length', 'max' => 255),
            array('created_at, updated_at,duplicate_id,solution, mail_to, repeat_step, resolved_at, closed_at, modified_by,priority', 'safe'),
            array('module_name,action_note,product_id,attachment_file,deleted_file_id,assign_to_name', 'safe'),
        );
    }

    public function duplicateIdValidator($attribute, $params)
    {
        if(self::DUPLICATE_SOLUTION == $this->solution)
        {
            $duplicatedIdError = $this->getError('duplicate_id');
            if(empty($duplicatedIdError))
            {
                if((empty($this->duplicate_id)))
                {
                    $this->addError('duplicate_id', Yii::t('BugInfo', self::ERROR_DUPLICATE_ID));
                }
                else
                {
                    $this->duplicate_id = $this->handleSpliter($this->duplicate_id);
                    $duplicatedIdArr = CommonService::splitStringToArray(',', $this->duplicate_id);
                    foreach($duplicatedIdArr as $dupId)
                    {
                        if($dupId != ceil($dupId))
                        {
                            $this->addError('duplicate_id', 'Duplicate ID[' . $dupId . ']' . Yii::t('BugInfo', self::ERROR_DUPLICATE_ID));
                        }
                        else
                        {
                            $infoObj = BugInfo::model()->findByPk($dupId);
                            if($infoObj == null || $infoObj->id != $dupId)
                            {
                                $this->addError('duplicate_id', 'Duplicate ID[' . $dupId . ']' . Yii::t('BugInfo', self::ERROR_DUPLICATE_ID));
                            }
                            elseif(!Info::isProductAccessable($infoObj->product_id))
                            {
                                $this->addError('duplicate_id', 'Duplicate ID[' . $dupId . ']' . Yii::t('Common', 'No access right'));
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $this->duplicate_id = null;
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
            'bugActions' => array(self::HAS_MANY, 'BugAction', 'buginfo_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'id' => Yii::t('Common', 'id'),
            'bug_status' => Yii::t('BugInfo', 'bug_status'),
            'title' => Yii::t('BugInfo', 'title'),
            'repeat_step' => Yii::t('BugInfo', 'repeat_step'),
            'resolved_at' => Yii::t('BugInfo', 'resolved_at'),
            'resolved_by' => Yii::t('BugInfo', 'resolved_by'),
            'closed_at' => Yii::t('BugInfo', 'closed_at'),
            'closed_by' => Yii::t('BugInfo', 'closed_by'),
            'duplicate_id' => Yii::t('BugInfo', 'duplicate_id'),
            'solution' => Yii::t('BugInfo', 'solution'),
            'reopen_count' => Yii::t('BugInfo', 'reopen_count'),
            'priority' => Yii::t('BugInfo', 'priority'),
            'severity' => Yii::t('BugInfo', 'severity')
        ));
    }

}