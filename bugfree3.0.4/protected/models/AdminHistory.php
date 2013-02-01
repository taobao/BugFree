<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of AdminHistory
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "admin_history".
 *
 * The followings are the available columns in table 'admin_history':
 * @property integer $id
 * @property string $action_field
 * @property string $old_value
 * @property string $new_value
 * @property integer $adminaction_id
 *
 * The followings are the available model relations:
 * @property AdminAction $adminaction
 */
class AdminHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AdminHistory the static model class
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
		return '{{admin_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('adminaction_id', 'required'),
			array('adminaction_id', 'numerical', 'integerOnly'=>true),
			array('action_field', 'length', 'max'=>45),
			array('old_value, new_value', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, action_field, old_value, new_value, adminaction_id', 'safe', 'on'=>'search'),
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
			'adminaction' => array(self::BELONGS_TO, 'AdminAction', 'adminaction_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'action_field' => 'Action Field',
			'old_value' => 'Old Value',
			'new_value' => 'New Value',
			'adminaction_id' => 'Adminaction',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('action_field',$this->action_field,true);
		$criteria->compare('old_value',$this->old_value,true);
		$criteria->compare('new_value',$this->new_value,true);
		$criteria->compare('adminaction_id',$this->adminaction_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}