<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ProductModule
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "product_module".
 *
 * The followings are the available columns in table 'product_module':
 * @property integer $id
 * @property string $name
 * @property integer $grade
 * @property integer $owner
 * @property integer $display_order
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $full_path_name
 * @property integer $product_id
 * @property integer $parent_id
 * @property integer $lock_version
 *
 * The followings are the available model relations:
 * @property BugInfo[] $bugInfos
 * @property CaseInfo[] $caseInfos
 * @property Product $product
 * @property ProductModule $parent
 * @property ProductModule[] $productModules
 * @property ResultInfo[] $resultInfos
 */
class ProductModule extends BugfreeModel
{
    const MODULE_SPLITTER = '/';

    const ERROR_NAME_FORMAT = 'error_name_format';
    const ERROR_NAME_EXISTED = 'error_name_existed';
    const ERROR_PARENT_MODULE = 'Parent module cannot be the child of oneself';
    const SCENARIO_EDIT = 'EDIT';

    public $owner_name;
    public $product_name;
    public $add_owner_name;

    /**
     * Returns the static model of the specified AR class.
     * @return ProductModule the static model class
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
        return '{{product_module}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, grade, display_order, 
                full_path_name,
                product_id, lock_version', 'required'),
            array('grade, owner, display_order, created_by, updated_by,
                product_id, parent_id, lock_version', 'numerical', 'integerOnly' => true),
            array('name', 'nameValidator'),
            array('parent_id', 'parentNameValidator'),
            array('name', 'length', 'max' => 45),
            array('owner_name,product_name,add_owner_name', 'safe'),
            array('owner_name', 'length', 'max' => 45),
        );
    }

    public function nameValidator($attribute, $params)
    {
        if(false !== strpos($this->name, ProductModule::MODULE_SPLITTER))
        {
            $this->addError('name', Yii::t('ProductModule', self::ERROR_NAME_FORMAT));
        }
        $id = 0;
        if(!$this->isNewRecord)
        {
            $id = $this->id;
        }
        if(empty($this->parent_id))
        {
            $isModuleExisted = ProductModule::model()->exists('id<> :id and product_id=:product_id and parent_id is null and name=:name',
                        array(':id' => $id, ':name' => $this->name, ':product_id' => $this->product_id));
        }
        else
        {
            $isModuleExisted = ProductModule::model()->exists('id<> :id and product_id=:product_id and parent_id=:parent_id and name=:name',
                        array(':id' => $id, ':parent_id' => $this->parent_id,
                            ':name' => $this->name, ':product_id' => $this->product_id));
        }
        if($isModuleExisted)
        {
            $this->addError('name', Yii::t('ProductModule', self::ERROR_NAME_EXISTED));
        }
    }

    public function parentNameValidator($attribute, $params)
    {
        if(!$this->isNewRecord)
        {
            $oldObj = $this->model()->findByPk($this->id);
            $fullParentName = '';
            if(0 != $this->parent_id)
            {
                $parentModule = $this->model()->findByPk($this->parent_id);
                $fullParentName = $parentModule['full_path_name'];
            }
            if(false !== strpos($fullParentName, $oldObj->full_path_name))
            {
                $this->addError('parent_id', Yii::t('ProductModule', self::ERROR_PARENT_MODULE));
            }
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
            'bugInfos' => array(self::HAS_MANY, 'BugInfo', 'productmodule_id'),
            'caseInfos' => array(self::HAS_MANY, 'CaseInfo', 'productmodule_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'parent' => array(self::BELONGS_TO, 'ProductModule', 'parent_id'),
            'productModules' => array(self::HAS_MANY, 'ProductModule', 'parent_id'),
            'resultInfos' => array(self::HAS_MANY, 'ResultInfo', 'productmodule_id'),
        );
    }

    protected function afterValidate()
    {
        if(!$this->getErrors())
        {
            if(self::SCENARIO_EDIT == $this->scenario)
            {
                if((isset($this->owner_name)) && ('' != $this->owner_name))
                {
                    $userInfo = TestUser::model()->findByAttributes(array('realname' => $this->owner_name,
                                'is_dropped' => CommonService::$TrueFalseStatus['FALSE']));
                    if($userInfo !== null)
                    {
                        $this->owner = $userInfo->id;
                    }
                    else
                    {
                        $this->addError('owner_name', Yii::t('TestUser', 'user not found'));
                    }
                }
                else
                {
                    $this->owner = null;
                }
            }
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('Common', 'id'),
            'name' => Yii::t('ProductModule', 'name'),
            'grade' => Yii::t('ProductModule', 'grade'),
            'owner' => Yii::t('ProductModule', 'owner'),
            'owner_name' => Yii::t('ProductModule', 'owner'),
            'add_owner_name' => Yii::t('ProductModule', 'owner'),
            'display_order' => Yii::t('ProductModule', 'display_order'),
            'created_at' => Yii::t('Common', 'created_at'),
            'created_by' => Yii::t('Common', 'created_by'),
            'updated_at' => Yii::t('Common', 'updated_at'),
            'updated_by' => Yii::t('Common', 'updated_by'),
            'full_path_name' => Yii::t('ProductModule', 'full_path_name'),
            'product_id' => Yii::t('ProductModule', 'product_id'),
            'product_name' => Yii::t('ProductModule', 'product_id'),
            'parent_id' => Yii::t('ProductModule', 'parent_id')
        );
    }

}