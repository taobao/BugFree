<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of BugfreeBaseModel
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */

/**
 * This is the model class for table "map_user_group".
 *
 * The followings are the available columns in table 'map_user_group':
 * @property integer $id
 * @property integer $test_user_id
 * @property integer $user_group_id
 * @property string $is_admin
 *
 * The followings are the available model relations:
 * @property TestUser $testUser
 * @property UserGroup $userGroup
 */
class MapUserGroup extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @return MapUserGroup the static model class
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
        return '{{map_user_group}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('test_user_id, user_group_id, is_admin', 'required'),
            array('test_user_id, user_group_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, test_user_id, user_group_id', 'safe', 'on' => 'search'),
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
            'userGroup' => array(self::BELONGS_TO, 'UserGroup', 'user_group_id'),
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
            'user_group_id' => 'User Group',
            'is_admin' => 'is_admin'
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('test_user_id', $this->test_user_id);
        $criteria->compare('user_group_id', $this->user_group_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}