<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of MapUserResult
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/**
 * This is the model class for table "map_user_result".
 *
 * The followings are the available columns in table 'map_user_result':
 * @property integer $id
 * @property integer $test_user_id
 * @property integer $info_id
 *
 * The followings are the available model relations:
 * @property TestUser $testUser
 * @property ResultInfo $info
 */
class MapUserResult extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MapUserResult the static model class
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
                return '{{map_user_result}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('test_user_id, info_id', 'required'),
			array('test_user_id, info_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, test_user_id, info_id', 'safe', 'on'=>'search'),
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
			'testUser' => array(self::BELONGS_TO, 'TestUser', 'test_user_id'),
			'info' => array(self::BELONGS_TO, 'ResultInfo', 'info_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'test_user_id' => 'Test User',
			'info_id' => 'Info',
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
		$criteria->compare('test_user_id',$this->test_user_id);
		$criteria->compare('info_id',$this->info_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}