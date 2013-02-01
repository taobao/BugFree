<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of BugInfoView
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class BugInfoView extends BugInfo
{

    public $created_by_name;
    public $updated_by_name;
    public $closed_by_name;
    public $resolved_by_name;

    /**
     * 获取VProduct静态实例
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 定义VProduct映射数据库表名称
     */
    public function tableName()
    {
        return '{{bugview}}';
    }

    /**
     * 定义VProduct映射数据库表主键
     */
    public function primarykey()
    {
        return 'id';
    }

    /**
     * attribute labels
     * 属性标签
     */
    public function attributeLabels()
    {
        return parent::attributeLabels() +
        array(
            'resolved_by_name' => Yii::t('BugInfo', 'resolved_by'),
            'closed_by_name' => Yii::t('BugInfo', 'closed_by')
        );
    }

    public static function getBasicSearchField()
    {        
        return array(
            'id' => Info::$InputType['number'],
            'title' => Info::$InputType['string'],
            'module_name' => Info::$InputType['path'],
            'bug_status' => Info::$InputType['option'],
            'assign_to_name' => Info::$InputType['people'],
            'mail_to' => Info::$InputType['string'],
            'severity'=> Info::$InputType['number'],
            'priority' => Info::$InputType['number'],
            'updated_by_name' => Info::$InputType['people'],
            'updated_at' => Info::$InputType['date'],
            'created_by_name' => Info::$InputType['people'],
            'created_at' => Info::$InputType['date'],
            'resolved_by_name' => Info::$InputType['people'],
            'resolved_at' => Info::$InputType['date'],
            'duplicate_id' => Info::$InputType['string'],
            'closed_by_name' => Info::$InputType['people'],
            'closed_at' => Info::$InputType['date'],
            'related_bug' => Info::$InputType['string'],
            'related_case' => Info::$InputType['string'],
            'related_result' => Info::$InputType['string'],
            'modified_by' => Info::$InputType['multipeople'],
            'reopen_count' => Info::$InputType['number']
        );
    }

    public static function getBasicSearchFieldConfig($productId)
    {
        $searchAbleField = self::getBasicSearchField();
        $returnArr = array();
        foreach($searchAbleField as $key => $value)
        {
            $returnArr[$key] = array('label' => BugInfoView::model()->getAttributeLabel($key),
                'type' => $value, 'isBasic' => true);
            if('bug_status' == $key)
            {
                $returnArr[$key]['value'] = self::getStatusOption();
                array_unshift($returnArr[$key]['value'],'');
            }
            else if('priority' == $key)
            {
                $returnArr[$key]['value'] = ProductService::getBugPriorityOption($productId);
            }
            else if('severity' == $key)
            {
                $returnArr[$key]['value'] = ProductService::getBugSeverityOption($productId);
            }
        }
        return $returnArr;
    }

    public static function getSearchableField($productId)
    {
        $searchAbleArr = self::getBasicSearchFieldConfig($productId);
        if(!empty($productId))
        {
            $searchAbleArr['solution'] = array('label' => BugInfo::model()->getAttributeLabel('solution'),
                'type' => Info::$InputType['option'], 'isBasic' => true,
                'value' => InfoService::getBugSolutionOptions($productId));
            $customSearchAbleFieldArr = ProductService::getSearchableCostomField('bug', $productId);
            $searchAbleArr = array_merge($searchAbleArr, $customSearchAbleFieldArr);
        }
        return $searchAbleArr;
    }

    public static function getDefaultShowFieldArr()
    {
        return array('id', 'severity','priority','title', 'created_by_name', 'assign_to_name',
            'resolved_by_name', 'solution', 'updated_at');
    }

}

?>
