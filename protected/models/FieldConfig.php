<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of FieldConfig
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "field_config".
 *
 * The followings are the available columns in table 'field_config':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $field_name
 * @property string $field_type
 * @property string $field_value
 * @property string $default_value
 * @property string $is_dropped
 * @property string $field_label
 * @property integer $lock_version
 * @property string $type
 * @property string $belong_group
 * @property integer $display_order
 * @property string $editable_action
 * @property string $validate_rule
 * @property string $match_expression
 * @property integer $product_id
 * @property string $edit_in_result
 * @property string $result_group
 * @property string $is_required
 *
 * The followings are the available model relations:
 * @property Product $product
 */
class FieldConfig extends BugfreeModel
{
    const ERROR_RESULT_GROUP = 'result_group cannot be blank.';
    const ERROR_MATCH_EXPRESSION = 'match expression cannot be blank.';

    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_SINGLESELECT = 'single select';
    const FIELD_TYPE_MULTISELECT = 'multi select';
    const FIELD_TYPE_SINGLEUSER = 'single user';
    const FIELD_TYPE_MULTIUSER = 'multi user';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_ACINPUT = 'ac_input';
    const FIELD_TYPE_ACINPUT_MATCH = 'ac_input_match';

    const VALIDATION_RULE_NONEED = 'no';
    const VALIDATION_RULE_UNIQUE = 'unique';
    const VALIDATION_RULE_MATCH = 'match';

    const BUG_FIELDSET_STATUS = 'bug_status';
    const BUG_FIELDSET_OPEN = 'bug_open';
    const BUG_FIELDSET_RESOLVE = 'bug_resolve';
    const BUG_FIELDSET_CLOSE = 'bug_close';
    const BUG_FIELDSET_OTHER = 'bug_other';
    const BUG_FIELDSET_RELATED = 'bug_related';

    const CASE_FIELDSET_STATUS = 'case_status';
    const CASE_FIELDSET_OPEN = 'case_open';
    const CASE_FIELDSET_SCRIPT = 'case_script';
    const CASE_FIELDSET_OTHER = 'case_other';
    const CASE_FIELDSET_RELATED = 'bug_related';

    const RESULT_FIELDSET_STATUS = 'result_status';
    const RESULT_FIELDSET_OPEN = 'result_open';
    const RESULT_FIELDSET_ENVI = 'result_environment';
    const RESULT_FIELDSET_OTHER = 'result_other';
    const RESULT_FIELDSET_RELATED = 'result_related';

    public function getFieldSets($type)
    {
        if('bug' == $type)
        {
            return array(
                self::BUG_FIELDSET_STATUS => Yii::t('FieldConfig', self::BUG_FIELDSET_STATUS),
                self::BUG_FIELDSET_OPEN => Yii::t('FieldConfig', self::BUG_FIELDSET_OPEN),
                self::BUG_FIELDSET_RESOLVE => Yii::t('FieldConfig', self::BUG_FIELDSET_RESOLVE),
                self::BUG_FIELDSET_CLOSE => Yii::t('FieldConfig', self::BUG_FIELDSET_CLOSE),
                self::BUG_FIELDSET_OTHER => Yii::t('FieldConfig', self::BUG_FIELDSET_OTHER),
                self::BUG_FIELDSET_RELATED => Yii::t('FieldConfig', self::BUG_FIELDSET_RELATED)
            );
        }
        else if('case' == $type)
        {
            return array(
                self::CASE_FIELDSET_STATUS => Yii::t('FieldConfig', self::CASE_FIELDSET_STATUS),
                self::CASE_FIELDSET_OPEN => Yii::t('FieldConfig', self::CASE_FIELDSET_OPEN),
                self::CASE_FIELDSET_SCRIPT => Yii::t('FieldConfig', self::CASE_FIELDSET_SCRIPT),
                self::CASE_FIELDSET_OTHER => Yii::t('FieldConfig', self::CASE_FIELDSET_OTHER),
                self::CASE_FIELDSET_RELATED => Yii::t('FieldConfig', self::CASE_FIELDSET_RELATED)
            );
        }
        else if('result' == $type)
        {
            return array(
                self::RESULT_FIELDSET_STATUS => Yii::t('FieldConfig', self::RESULT_FIELDSET_STATUS),
                self::RESULT_FIELDSET_OPEN => Yii::t('FieldConfig', self::RESULT_FIELDSET_OPEN),
                self::RESULT_FIELDSET_ENVI => Yii::t('FieldConfig', self::RESULT_FIELDSET_ENVI),
                self::RESULT_FIELDSET_OTHER => Yii::t('FieldConfig', self::RESULT_FIELDSET_OTHER),
                self::RESULT_FIELDSET_RELATED => Yii::t('FieldConfig', self::RESULT_FIELDSET_RELATED)
            );
        }
    }

    public function getFieldTypes()
    {
        return array(
            self::FIELD_TYPE_TEXT => Yii::t('FieldConfig', self::FIELD_TYPE_TEXT),
            self::FIELD_TYPE_TEXTAREA => Yii::t('FieldConfig', self::FIELD_TYPE_TEXTAREA),
            self::FIELD_TYPE_SINGLESELECT => Yii::t('FieldConfig', self::FIELD_TYPE_SINGLESELECT),
            self::FIELD_TYPE_MULTISELECT => Yii::t('FieldConfig', self::FIELD_TYPE_MULTISELECT),
            self::FIELD_TYPE_SINGLEUSER => Yii::t('FieldConfig', self::FIELD_TYPE_SINGLEUSER),
            self::FIELD_TYPE_MULTIUSER => Yii::t('FieldConfig', self::FIELD_TYPE_MULTIUSER),
            self::FIELD_TYPE_DATE => Yii::t('FieldConfig', self::FIELD_TYPE_DATE),
            self::FIELD_TYPE_ACINPUT => Yii::t('FieldConfig', self::FIELD_TYPE_ACINPUT),
            self::FIELD_TYPE_ACINPUT_MATCH => Yii::t('FieldConfig', self::FIELD_TYPE_ACINPUT_MATCH)
        );
    }

    public function getValidationRules()
    {
        return array(
            self::VALIDATION_RULE_NONEED => Yii::t('FieldConfig', self::VALIDATION_RULE_NONEED),
            self::VALIDATION_RULE_UNIQUE => Yii::t('FieldConfig', self::VALIDATION_RULE_UNIQUE),
            self::VALIDATION_RULE_MATCH => Yii::t('FieldConfig', self::VALIDATION_RULE_MATCH),
        );
    }

    public $product_name;
    public $belong_group_name;
    public $editable_action_name;

    /**
     * Returns the static model of the specified AR class.
     * @return FieldConfig the static model class
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
        return '{{field_config}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('field_name, field_type, field_label, belong_group, validate_rule,
                display_order, product_id, is_dropped, lock_version', 'required'),
            array('editable_action_name, edit_in_result', 'required', 'on' => 'bug'),
            array('match_expression','matchExpressionValidator'),
            array('result_group', 'resultGroupValidator', 'on' => 'bug'),
            array('created_by, updated_by, display_order, product_id, lock_version', 'numerical', 'integerOnly' => true),
            array('field_name, field_type, field_label, belong_group, validate_rule', 'length', 'max' => 45),
            array('field_name', 'match', 'pattern' => '/^([a-zA-Z])+([a-zA-Z0-9_])+$/',
                'message' => Yii::t('FieldConfig', 'only charactor, number and underline')),
            array('is_dropped,is_required', 'length', 'max' => 1),
            array('type', 'length', 'max' => 6),
            array('editable_action, match_expression', 'length', 'max' => 255),
            array('created_at, updated_at, field_value, default_value, edit_in_result, result_group', 'safe'),
            array('product_name,belong_group_name,editable_action_name','safe')
        );
    }

    public function matchExpressionValidator($attribute, $params)
    {
        if(self::VALIDATION_RULE_MATCH == $this->validate_rule)
        {
            if((empty($this->match_expression)))
            {
                $this->addError('match_expression', Yii::t('FieldConfig', self::ERROR_MATCH_EXPRESSION));
            }
        }
    }


    public function resultGroupValidator($attribute, $params)
    {
        if(CommonService::$TrueFalseStatus['TRUE'] == $this->edit_in_result)
        {
            if((empty($this->result_group)))
            {
                $this->addError('result_group', Yii::t('FieldConfig', self::ERROR_RESULT_GROUP));
            }
        }
    }
    protected function afterValidate()
    {
        if(!empty($this->editable_action_name))
        {
            $this->editable_action = join(',', $this->editable_action_name);
        }       
        return parent::afterValidate();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('Common', 'id'),
            'created_at' => Yii::t('AdminCommon', 'created_at'),
            'created_by' => Yii::t('AdminCommon', 'created_by'),
            'updated_at' => Yii::t('AdminCommon', 'updated_at'),
            'updated_by' => Yii::t('AdminCommon', 'updated_by'),
            'field_name' => Yii::t('FieldConfig', 'field_name'),
            'field_type' => Yii::t('FieldConfig', 'field_type'),
            'field_value' => Yii::t('FieldConfig', 'field_value'),
            'default_value' => Yii::t('FieldConfig', 'default_value'),
            'is_dropped' => Yii::t('Common', 'is_dropped'),
            'is_required' => Yii::t('FieldConfig', 'is_required'),
            'field_label' => Yii::t('FieldConfig', 'field_label'),
            'type' => Yii::t('FieldConfig', 'type'),
            'belong_group' => Yii::t('FieldConfig', 'belong_group'),
            'belong_group_name' => Yii::t('FieldConfig', 'belong_group'),
            'display_order' => Yii::t('FieldConfig', 'display_order'),
            'editable_action' => Yii::t('FieldConfig', 'editable_action'),
            'editable_action_name' => Yii::t('FieldConfig', 'editable_action'),
            'validate_rule' => Yii::t('FieldConfig', 'validate_rule'),
            'match_expression' => Yii::t('FieldConfig', 'match_expression'),
            'product_id' => Yii::t('FieldConfig', 'product_id'),
            'edit_in_result' => Yii::t('FieldConfig', 'edit_in_result'),
            'result_group' => Yii::t('FieldConfig', 'result_group'),
            'product_name' => Yii::t('FieldConfig', 'product_id'),
        );
    }
}