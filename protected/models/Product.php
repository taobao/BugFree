<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of Product
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $is_dropped
 * @property integer $lock_version
 * @property string $bug_customfield_text
 * @property string $case_customfield_text
 * @property string $result_customfield_text
 * @property string $solution_value
 * @property string $bug_step_template
 * @property string $case_step_template
 * @property string $bug_severity
 * @property string $bug_priority
 * @property string $case_priority
 * @property integer $display_order
 *
 * The followings are the available model relations:
 * @property FieldConfig[] $fieldConfigs
 * @property MapProductGroup[] $mapProductGroups
 * @property MapProductUser[] $mapProductUsers
 * @property ProductModule[] $productModules
 * @property UserQuery[] $userQueries
 */
class Product extends BugfreeModel
{
    public $group_name;
    public $product_manager;

    /**
     * Returns the static model of the specified AR class.
     * @return Product the static model class
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
        return '{{product}}';
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
                is_dropped, display_order, lock_version, solution_value,
                bug_severity, bug_priority, case_priority', 'required'),
            array('created_by, updated_by, display_order, lock_version',
                'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('name', 'unique'),
            array('solution_value,bug_step_template,case_step_template','safe'),
            array('is_dropped', 'length', 'max' => 1),
            array('group_name,product_manager', 'safe'),
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
            'fieldConfigs' => array(self::HAS_MANY, 'FieldConfig', 'product_id'),
            'mapProductGroups' => array(self::HAS_MANY, 'MapProductGroup', 'product_id'),
            'mapProductUsers' => array(self::HAS_MANY, 'MapProductUser', 'product_id'),
            'productModules' => array(self::HAS_MANY, 'ProductModule', 'product_id'),
            'userQueries' => array(self::HAS_MANY, 'UserQuery', 'product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => Yii::t('Product','name'),
            'created_at' => Yii::t('AdminCommon', 'created_at'),
            'created_by' => Yii::t('AdminCommon', 'created_by'),
            'updated_at' => Yii::t('AdminCommon', 'updated_at'),
            'updated_by' => Yii::t('AdminCommon', 'updated_by'),
            'is_dropped' => Yii::t('Common','is_dropped'),
            'solution_value' => Yii::t('Product','solution_value'),
            'bug_severity' => Yii::t('Product','bug_severity'),
            'bug_priority' => Yii::t('Product','bug_priority'),
            'case_priority' => Yii::t('Product','case_priority'),
            'group_name' => Yii::t('Product','group_name'),
            'product_manager' => Yii::t('Product','product_manager'),
            'display_order' => Yii::t('Product', 'display_order'),
            'bug_step_template' => Yii::t('Product','bug_step_template'),
            'case_step_template' => Yii::t('Product','case_step_template')
        );
    }

}