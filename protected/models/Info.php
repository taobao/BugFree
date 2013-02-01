<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of Info
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
abstract class Info extends BugfreeModel
{
    const SHOW_TYPE_GRID = 'grid';
    const SHOW_TYPE_REPORT = 'report';

    const ACTION_OPEN = 'opened';
    const ACTION_OPEN_EDIT = 'opened_edit';

    const TYPE_BUG = 'bug';
    const TYPE_CASE = 'case';
    const TYPE_RESULT = 'result';

    const ACTION_IMPORT = 'import';

    const MARK = 'mark';
    const QUERY_GROUP_NAME = 'BugFreeQuery';
    const TEMPLATE_NUMBER = 999999;

    static $InputType = array(
        'number' => 'number',
        'date' => 'date',
        'string' => 'string',
        'people' => 'people',
        'multipeople' => 'multipeople',
        'option' => 'option',
        'multioption' => 'multioption',
        'path' => 'path'
    );

    public $module_name;
    public $assign_to_name;
    public $action_note;
    public $attachment_file;
    public $deleted_file_id;

    protected function afterValidate()
    {
        //check product permission
        if(!self::isProductAccessable($this->product_id))
        {
            $this->addError('product_id', $this->getAttributeLabel('product_id') .
                    ' ' . Yii::t('Product', 'No access right to this product'));
        }
        //set modified_by
        if($this->isNewRecord)
        {
            $this->modified_by = $this->updated_by;
        }
        else
        {
            $dbBugInfo = $this->model()->findByPk($this->id);
            $oldModifiedBy = $dbBugInfo->modified_by;
            if(!in_array($this->updated_by, CommonService::splitStringToArray(',', $oldModifiedBy)))
            {
                if(empty($this->modified_by))
                {
                    $this->modified_by = $this->updated_by;
                }
                else
                {
                    $this->modified_by .= ',' . $this->updated_by;
                }
            }
        }
        //set productmodule_id to be null if productmodule_id = 0
        if(0 == $this->productmodule_id)
        {
            $this->productmodule_id = null;
        }

        //filter duplicated mail_to
        if(!empty($this->mail_to))
        {
            $this->mail_to = join(',', CommonService::splitStringToArray(',', $this->mail_to));
        }

        //set assign_to
        if(!empty($this->assign_to_name))
        {
            if(TestUser::ACTIVE_USER_NAME == $this->assign_to_name)
            {
                $this->assign_to = TestUser::ACTIVE_USER_ID;
            }
            else if(TestUser::CLOSE_USER_NAME == $this->assign_to_name)
            {
                $this->assign_to = TestUser::CLOSE_USER_ID;
            }
            else
            {
                $assigntoInfo = TestUser::model()->findByAttributes(array('realname' => $this->assign_to_name));
                if($assigntoInfo !== null)
                {
                    $this->assign_to = $assigntoInfo->id;
                }
                else
                {
                    $assigntoNameError = $this->getError('assign_to_name');
                    if(empty($assigntoNameError))
                    {
                        $this->addError('assign_to_name', $this->getAttributeLabel('assign_to_name') . ' ' . Yii::t('TestUser', 'user not found'));
                    }
                }
            }
        }
        else
        {
            $this->assign_to = null;
        }
        return parent::afterValidate();
    }

    public static function isProductAccessable($product)
    {
        $accessProductIds = Yii::app()->user->getState('visit_product_id');
        if(!in_array($product, $accessProductIds))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function handleSpliter($str)
    {
        $str = str_replace(array('，', '，', '；', ';', '；', '。', '。', '。', ' ', ' ', '　',), ',', $str);
        return $str;
    }

    public function relatedBugValidator($attribute, $params)
    {
        if(isset($this->related_bug))
        {
            $this->related_bug = $this->handleSpliter($this->related_bug);
            $relatedBugArr = CommonService::splitStringToArray(',', $this->related_bug);
            foreach($relatedBugArr as $bugIdTmp)
            {
                $infoObj = BugInfo::model()->findByPk($bugIdTmp);
                if($infoObj == null || $infoObj->id != $bugIdTmp)
                {
                    $this->addError('related_bug', 'Bug ID[' . $bugIdTmp . ']' . Yii::t('Common', 'is not existed'));
                }
                elseif(!Info::isProductAccessable($infoObj->product_id))
                {
                    $this->addError('related_bug', 'Bug ID[' . $bugIdTmp . ']' . Yii::t('Common', 'No access right'));
                }
            }
        }
    }

    public function relatedCaseValidator($attribute, $params)
    {
        if(isset($this->related_case))
        {
            $this->related_case = $this->handleSpliter($this->related_case);
            $relatedBugArr = CommonService::splitStringToArray(',', $this->related_case);
            foreach($relatedBugArr as $caseIdTmp)
            {
                $infoObj = CaseInfo::model()->findByPk($caseIdTmp);
                if($infoObj == null || $infoObj->id != $caseIdTmp)
                {
                    $this->addError('related_case', 'Case ID[' . $caseIdTmp . ']' . Yii::t('Common', 'is not existed'));
                }
                elseif(!Info::isProductAccessable($infoObj->product_id))
                {
                    $this->addError('related_case', 'Case ID[' . $caseIdTmp . ']' . Yii::t('Common', 'No access right'));
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
            'created_at' => Yii::t('Common', 'created_at'),
            'created_by' => Yii::t('Common', 'created_by'),
            'updated_at' => Yii::t('Common', 'updated_at'),
            'updated_by' => Yii::t('Common', 'updated_by'),
            'created_by_name' => Yii::t('Common', 'created_by'),
            'updated_by_name' => Yii::t('Common', 'updated_by'),
            'product_id' => Yii::t('Common', 'product_id'),
            'assign_to' => Yii::t('Common', 'assign_to'),
            'modified_by' => Yii::t('Common', 'modified_by'),
            'mail_to' => Yii::t('Common', 'mail_to'),
            'lock_version' => 'Lock Version',
            'related_bug' => Yii::t('Common', 'related_bug'),
            'related_case' => Yii::t('Common', 'related_case'),
            'related_result' => Yii::t('Common', 'related_result'),
            'productmodule_id' => Yii::t('Common', 'productmodule_id'),
            'module_name' => Yii::t('Common', 'productmodule_id'),
            'action_note' => Yii::t('Common', 'action_note'),
            'assign_to_name' => Yii::t('Common', 'assign_to'),
        );
    }

}

?>
