<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestFile
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * This is the model class for table "test_file".
 *
 * The followings are the available columns in table 'test_file':
 * @property integer $id
 * @property string $file_title
 * @property string $file_location
 * @property string $file_type
 * @property string $file_size
 * @property string $is_dropped
 * @property integer $target_id
 * @property string $target_type
 * @property integer $add_action_id
 * @property integer $delete_action_id
 */
class TestFile extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TestFile the static model class
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
                return '{{test_file}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('file_title, file_location, file_size, is_dropped, target_id, target_type, add_action_id', 'required'),
			array('target_id, add_action_id, delete_action_id', 'numerical', 'integerOnly'=>true),
			array('file_title', 'length', 'max'=>255),
			array('file_type, file_size', 'length', 'max'=>45),
			array('is_dropped', 'length', 'max'=>1),
			array('target_type', 'length', 'max'=>6)
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'file_title' => 'File Title',
			'file_location' => 'File Location',
			'file_type' => 'File Type',
			'file_size' => 'File Size',
			'is_dropped' => 'Is Dropped',
			'target_id' => 'Target',
			'target_type' => 'Target Type',
			'add_action_id' => 'Add Action',
			'delete_action_id' => 'Delete Action',
		);
	}

}