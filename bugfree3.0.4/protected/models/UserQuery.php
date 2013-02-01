<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserQuery
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "user_query".
 *
 * The followings are the available columns in table 'user_query':
 * @property integer $id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property string $query_type
 * @property string $query_string
 * @property string $andorlist
 * @property string $fieldlist
 * @property string $operatorlist
 * @property string $left_parentheses
 * @property string $right_parentheses
 * @property integer $product_id
 * @property string $title
 * @property string $valuelist
 *
 * The followings are the available model relations:
 * @property Product $product
 */
class UserQuery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserQuery the static model class
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
                return '{{user_query}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_at, created_by, updated_at, query_type, product_id, title', 'required'),
			array('created_by, product_id', 'numerical', 'integerOnly'=>true),
			array('query_type', 'length', 'max'=>6),
			array('title', 'length', 'max'=>100),
			array('query_string, andorlist, fieldlist, operatorlist, valuelist, left_parentheses, right_parentheses', 'safe')
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
			'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
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
			'updated_at' => 'Updated At',
			'query_type' => 'Query Type',
			'query_string' => 'Query String',
			'andorlist' => 'Andorlist',
			'fieldlist' => 'Fieldlist',
			'operatorlist' => 'Operatorlist',
			'left_parentheses' => 'Left Parentheses',
			'right_parentheses' => 'Right Parentheses',
			'product_id' => 'Product',
			'title' => 'Title',
			'valuelist' => 'Valuelist',
		);
	}

}