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
class ResultInfoView extends ResultInfo
{

    public $created_by_name;
    public $updated_by_name;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{resultview}}';
    }

    public function primarykey()
    {
        return 'id';
    }

    public static function getBasicSearchField()
    {
        return array(
            'id' => Info::$InputType['number'],
            'title' => Info::$InputType['string'],
            'module_name' => Info::$InputType['path'],
            'result_value' => Info::$InputType['option'],
            'result_status' => Info::$InputType['option'],
            'assign_to_name' => Info::$InputType['people'],
            'mail_to' => Info::$InputType['string'],
            'updated_by_name' => Info::$InputType['people'],
            'updated_at' => Info::$InputType['date'],
            'created_by_name' => Info::$InputType['people'],
            'created_at' => Info::$InputType['date'],
            'related_case_id' => Info::$InputType['number'],
            'related_bug' => Info::$InputType['string'],
            'modified_by' => Info::$InputType['multipeople'],
        );
    }


    public static function getBasicSearchFieldConfig()
    {
        $searchAbleField = self::getBasicSearchField();
        $returnArr = array();
        foreach($searchAbleField as $key => $value)
        {
            $returnArr[$key] = array('label' => ResultInfoView::model()->getAttributeLabel($key),
                'type' => $value, 'isBasic' => true);
            if('result_status' == $key)
            {
                $returnArr[$key]['value'] = self::getStatusOption();
                array_unshift($returnArr[$key]['value'],'');
            }
            else if('result_value' == $key)
            {
                $returnArr[$key]['value'] = self::getResultValueOption();
            }
        }
        return $returnArr;
    }

    public static function getSearchableField($productId)
    {
        $searchAbleArr = self::getBasicSearchFieldConfig();
        if(!empty($productId))
        {
            $customSearchAbleFieldArr = ProductService::getSearchableCostomField('result', $productId);
            $searchAbleArr = array_merge($searchAbleArr, $customSearchAbleFieldArr);
        }
        return $searchAbleArr;
    }

    public static function getDefaultShowFieldArr()
    {
        return array('id', 'title', 'created_by_name', 'assign_to_name',
            'related_case_id','result_value', 'updated_at');
    }
}

?>
