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
class CaseInfoView extends CaseInfo
{

    public $created_by_name;
    public $updated_by_name;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{caseview}}';
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
            'case_status' => Info::$InputType['option'],
            'assign_to_name' => Info::$InputType['people'],
            'mail_to' => Info::$InputType['string'],
            'priority' => Info::$InputType['number'],
            'updated_by_name' => Info::$InputType['people'],
            'updated_at' => Info::$InputType['date'],
            'created_by_name' => Info::$InputType['people'],
            'created_at' => Info::$InputType['date'],
            'delete_flag' => Info::$InputType['option'],
            'related_bug' => Info::$InputType['string'],
            'related_case' => Info::$InputType['string'],
            'related_result' => Info::$InputType['string'],
            'modified_by' => Info::$InputType['multipeople']
        );
    }


    public static function getBasicSearchFieldConfig($productId)
    {
        $searchAbleField = self::getBasicSearchField();
        $returnArr = array();
        foreach($searchAbleField as $key => $value)
        {
            $returnArr[$key] = array('label' => CaseInfoView::model()->getAttributeLabel($key),
                'type' => $value, 'isBasic' => true);
            if('case_status' == $key)
            {
                $returnArr[$key]['value'] = self::getStatusOption();
                array_unshift($returnArr[$key]['value'],'');
            }
            else if('priority' == $key)
            {
                $returnArr[$key]['value'] = ProductService::getCasePriorityOption($productId);
            }
            else if('delete_flag' == $key)
            {
                $returnArr[$key]['value'] = CommonService::getTrueFalseOptions();
                array_unshift($returnArr[$key]['value'],'');
            }
        }
        return $returnArr;
    }

    public static function getSearchableField($productId)
    {
        $searchAbleArr = self::getBasicSearchFieldConfig($productId);
        if(!empty($productId))
        {
            $customSearchAbleFieldArr = ProductService::getSearchableCostomField('case', $productId);
            $searchAbleArr = array_merge($searchAbleArr, $customSearchAbleFieldArr);
        }
        return $searchAbleArr;
    }

    public static function getDefaultShowFieldArr()
    {
        return array('id','priority', 'title','case_status', 'created_by_name', 'assign_to_name',
            'updated_at');
    }

}

?>
