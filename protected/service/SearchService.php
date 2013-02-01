<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of SearchService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class SearchService
{

    public static function getSearchHtml($type, $searchFieldConfig, $searchRowArr = null)
    {
        $returnHtml = '';
        $rowNum = count($searchRowArr);
        $isShowAddLink = true;
        $isShowRemoveLink = true;
        if(1 == $rowNum)
        {
            $isShowRemoveLink = false;
        }
        if(CommonService::getQueryLimitNumber() <= $rowNum)
        {
            $isShowAddLink = false;
        }
        $returnHtml = self::getFirstSearchConditionRowHtml($searchRowArr[0], $isShowAddLink);
        for($i = 1; $i < $rowNum; $i++)
        {
            $returnHtml .= self::getSearchConditionRowHtml($i,
                            $searchFieldConfig,
                            $searchRowArr[$i], $isShowAddLink, $isShowRemoveLink);
        }
        return $returnHtml;
    }

    public static function getQueryOrder($queryRowOrderStr)
    {
        $arr = CommonService::splitStringToArray(',', $queryRowOrderStr);
        $indexArr = array();
        foreach($arr as $value)
        {
            $indexArr[] = str_replace('SearchConditionRow', '', $value);
        }
        return $indexArr;
    }

    public static function getSearchConditionArr($searchCondition)
    {
        $searchRowOrderArr = self::getQueryOrder($searchCondition['queryRowOrder']);
        $queryFieldNameArr = array('leftParenthesesName', 'field', 'operator', 'value', 'rightParenthesesName', 'andor');
        $searchConditionArr = array();
        foreach($searchRowOrderArr as $index)
        {
            $searchConditionRow = array();
            foreach($queryFieldNameArr as $queryField)
            {
                $searchConditionRow[$queryField] = $searchCondition[$queryField . $index];
            }
            $searchConditionArr[] = $searchConditionRow;
        }
        return $searchConditionArr;
    }

    public static function getSearchConditionRowHtml($index, $searchFieldConfig, $queryRow, $isShowAddLink=true, $isShowRemoveLink=true)
    {
        $returnStr = '';
        $addLinkStyle = '';
        $removeLinkStyle = '';
        if(!$isShowAddLink)
        {
            $addLinkStyle = ' style="display:none" ';
        }
        if(!$isShowRemoveLink)
        {
            $removeLinkStyle = ' style="display:none" ';
        }
        $addRemoveLinkStr = '<a class="add_search_button" ' . $addLinkStyle .
                ' href="javascript:addSearchField(' . $index . ');">' .
                '<img src="' . Yii::app()->theme->baseUrl . '/assets/images/add_search.gif"/></a>' .
                '&nbsp;&nbsp;' .
                '<a class="cancel_search_button" ' . $removeLinkStyle . ' href="javascript:removeSearchField(' . $index .
                ');"><img src="' . Yii::app()->theme->baseUrl . '/assets/images/cancel_search.gif"/></a>';

        $optionValue = array();
        if(!empty($searchFieldConfig[$queryRow['field']]['value']))
        {
            $optionValue = $searchFieldConfig[$queryRow['field']]['value'];
        }
        $returnStr = '<tr class="SearchConditionRow" id="SearchConditionRow' . $index . '">' .
                '<td>' . self::getParentheses($index, 'left', $queryRow['leftParenthesesName']) . '</td>' .
                '<td>' . self::getFieldOptions($index, $searchFieldConfig, $queryRow['field']) . '</td>' .
                '<td>' . self::getSearchOperatorOption($index, $searchFieldConfig[$queryRow['field']]['type'], $queryRow['operator']) . '</td>' .
                '<td>' . self::getFieldValue($index, $searchFieldConfig[$queryRow['field']]['type'], $optionValue, $queryRow['value']) . '</td>' .
                '<td>' . self::getParentheses($index, 'right', $queryRow['rightParenthesesName']) . '</td>' .
                '<td>' . self::getAndOrOption($index, $queryRow['andor']) . '</td>' .
                '<td>' . $addRemoveLinkStr . '</td>' .
                '</tr>';
        return $returnStr;
    }

    /**
     * get first line's search condition html
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $type                   bug,case or result
     * @param   int     $id                     file id
     * @return  array                           edit result information.
     */
    public static function getFirstSearchConditionRowHtml($queryRow, $isShowAddLink)
    {
        $returnStr = '';
        $addLinkStyle = '';
        $removeLinkStyle = '';
        if(!$isShowAddLink)
        {
            $addLinkStyle = ' style="display:none" ';
        }

        $addRemoveLinkStr = '<a class="add_search_button" ' . $addLinkStyle .
                ' href="javascript:addSearchField(0);">' .
                '<img src="' . Yii::app()->theme->baseUrl . '/assets/images/add_search.gif"/></a>';

        $leftStr = CHtml::hiddenField(Info::QUERY_GROUP_NAME . '[leftParenthesesName0]');
        $rightStr = CHtml::hiddenField(Info::QUERY_GROUP_NAME . '[rightParenthesesName0]');
        $fieldOptionStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                        '[field0]',
                        $queryRow['field'], array('module_name' => Yii::t('Common', 'productmodule_id')),
                        array('id' => Info::QUERY_GROUP_NAME . '_field0',
                            'style' => 'color:#878787;'));
        $operatorStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                        '[operator0]',
                        'UNDER', array('UNDER' => Yii::t('FieldOperator', 'UNDER')),
                        array('id' => Info::QUERY_GROUP_NAME .
                            '_operator0',
                            'style' => 'width:100%;color:#878787;'));
        $fieldValueStr = CHtml::textField(Info::QUERY_GROUP_NAME .
                        '[value0]', $queryRow['value'], array('size' => 16,
                    'id' => Info::QUERY_GROUP_NAME . '_value0',
                    'readonly' => 'readonly',
                    'style' => 'color:#878787;'));
        $andStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                        '[andor0]',
                        'And', array('And' => Yii::t('Common', 'And')),
                        array('id' => Info::QUERY_GROUP_NAME .
                            '_andor0', 'style' => 'width:60px;color:#878787;'));

        $returnStr = '<tr class="SearchConditionRow" id="SearchConditionRow0">' .
                '<td>' . $leftStr . '</td>' .
                '<td>' . $fieldOptionStr . '</td>' .
                '<td>' . $operatorStr . '</td>' .
                '<td>' . $fieldValueStr . '</td>' .
                '<td>' . $rightStr . '</td>' .
                '<td>' . $andStr . '</td>' .
                '<td>' . $addRemoveLinkStr . '</td>' .
                '</tr>';
        return $returnStr;
    }

    private static function getParentheses($index, $direction, $value='')
    {
        if('left' == $direction)
        {
            return CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                    '[leftParenthesesName' . $index . ']',
                    $value, array('' => '', '(' => '('),
                    array('id' => Info::QUERY_GROUP_NAME .
                        '_leftParenthesesName' . $index,
                        'onchange' => 'validateParentheses()',
                        'style' => 'width:40px;'));
        }
        else
        {
            return CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                    '[rightParenthesesName' . $index . ']',
                    $value, array('' => '', ')' => ')'),
                    array('id' => Info::QUERY_GROUP_NAME .
                        '_rightParenthesesName' . $index,
                        'onchange' => 'validateParentheses()',
                        'style' => 'width:40px;'));
        }
    }

    public static function getFieldValue($index, $type, $value, $fieldValue='')
    {
        if((Info::$InputType['option'] == $type) || (Info::$InputType['multioption'] == $type))
        {
            $optionValueArr = array();
            foreach($value as $valueTmp)
            {
                $optionValueArr[$valueTmp] = $valueTmp;
            }
            $returnStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                            '[value' . $index . ']',
                            $fieldValue, $optionValueArr,
                            array('id' => Info::QUERY_GROUP_NAME .
                                '_value' . $index, 'style' => 'width:95%'));
        }
        else
        {
            $returnStr = CHtml::textField(Info::QUERY_GROUP_NAME .
                            '[value' . $index . ']', $fieldValue, array('size' => 16,
                        'id' => Info::QUERY_GROUP_NAME . '_value' . $index));
        }
        return $returnStr;
    }

    private static function getAndOrOption($index, $andOrValue)
    {
        return CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                '[andor' . $index . ']',
                $andOrValue, array('And' => Yii::t('Common', 'And'), 'Or' => Yii::t('Common', 'Or')),
                array('id' => Info::QUERY_GROUP_NAME .
                    '_andor' . $index, 'style' => 'width:60px;'));
    }

    public static function getSearchableFields($type, $productId)
    {
        $className = ucfirst(strtolower($type)) . 'InfoView';
        $targetModel = new $className();
        $returnArr = $targetModel->getSearchableField($productId);
        $returnArr[Info::MARK] = array('label' => Yii::t('Common', Info::MARK),
            'type' => Info::$InputType['option'],
            'isBasic' => false,
            'value' => array('', 0, 1));
        return $returnArr;
    }

    public static function getBasicFieldArr($type)
    {
        $className = ucfirst(strtolower($type)) . 'InfoView';
        $targetModel = new $className();
        return array_keys($targetModel->getBasicSearchField());
    }

    public static function isAllBasicField($basicFieldArr, $checkFieldArr)
    {
        $ignoreFieldArr = array(Info::MARK, 'product_id', 'solution');
        foreach($checkFieldArr as $field)
        {
            if(!in_array($field, $basicFieldArr) && !in_array($field, $ignoreFieldArr))
            {
                return false;
            }
        }
        return true;
    }

    public static function getSelectFieldsOption($searchConfig, $showFieldArr)
    {
        $optionArr = array();
        foreach($searchConfig as $key => $value)
        {
            if((Info::MARK != $key) && !in_array($key, $showFieldArr))
            {
                $optionArr[$key] = $value['label'];
            }
        }
        $returnStr = CHtml::dropDownList('fieldsToSelectList',
                        '', $optionArr,
                        array('multiple' => 'multiple',
                            'style' => 'width:95%;height:200px;'));
        return $returnStr;
    }

    public static function getShowFieldsOption($searchConfig, $showFieldArr)
    {
        $showFieldOptionArr = array();
        foreach($showFieldArr as $fieldName)
        {
            $showFieldOptionArr[$fieldName] = $searchConfig[$fieldName]['label'];
        }
        $returnStr = CHtml::dropDownList('fieldsToShowList',
                        '', $showFieldOptionArr,
                        array('multiple' => 'multiple',
                            'style' => 'width:95%;height:200px;'));
        return $returnStr;
    }

    public static function getDefaultShowFieldArr($type)
    {
        $className = ucfirst(strtolower($type)) . 'InfoView';
        $targetModel = new $className();
        $columnArr = $targetModel->getDefaultShowFieldArr();
        return $columnArr;
    }

    public static function getTitleLink($infoType, $columnName, $value)
    {
        $title = CommonService::sysSubStr($value[$columnName], 50, true);
        return '<span class="title"><a href="' . Yii::app()->createUrl('info/edit',
                array('type' => $infoType, 'id' => $value['id'])) . '" target="_blank" title="' .
        CHtml::encode($value[$columnName]) . '">' . CHtml::encode($title) . '</a></span>';
    }

    public static function formatColumnData($infoType, $productId, $fieldType, $columnName, $value)
    {
        $returnValue = $value;
        if(Info::$InputType['people'] == $fieldType)
        {
            $returnValue = CommonService::getUserRealName($value);
        }
        elseif(Info::$InputType['multipeople'] == $fieldType)
        {
            $returnValue = CommonService::getMultiUserRealName($value);
        }
        $returnValue = '<a href="' . Yii::app()->createUrl('info/index',
                        array('type' => $infoType, 'product_id' => $productId,
                            'filter' => $columnName . '|' . $value)) . '">' . $returnValue . '</a>';
        return $returnValue;
    }

    public static function getViewColumnArr($searchFieldConfig, $showFieldArr, $infoType, $productId, $filterColumn)
    {
        $viewColumnArr = array(array('name' => Info::MARK, 'header' => $searchFieldConfig[Info::MARK]['label'],
                'type' => 'raw', 'value' => 'SearchService::getMarkLink($data)',
                'htmlOptions' => array('style' => 'text-align:center;')));

        $needHandleFieldArr = array();
        $needHandleFieldTypeArr = array(Info::$InputType['multipeople']);
        foreach($showFieldArr as $columnName)
        {
            $fieldInfo = $searchFieldConfig[$columnName];
            if((true == $fieldInfo['isBasic']) && in_array($fieldInfo['type'],
                            $needHandleFieldTypeArr) && ('mail_to' != $columnName))
            {
                $needHandleFieldArr[$columnName] = $fieldInfo['type'];
            }
        }

        foreach($showFieldArr as $key)
        {
            $fieldInfo = $searchFieldConfig[$key];
            $fieldType = $fieldInfo['type'];

            $columnArrTmp = array('name' => $key,
                'header' => $fieldInfo['label'],
                'type' => 'raw');
//            if('severity' == $key)
//            {
//                $columnArrTmp['header'] = 'Sev';
//            }
//            elseif('priority' == $key)
//            {
//                $columnArrTmp['header'] = 'Pri';
//            }

            if($filterColumn == $key)
            {
                $columnArrTmp['htmlOptions'] = array('class' => 'filtered');
            }

            if(in_array($key, array_keys($needHandleFieldArr)))
            {
                $columnArrTmp['value'] = 'SearchService::formatColumnData("' . $infoType . '",$data["product_id"],"' . $fieldType . '","' . $key . '",$data["' . $key . '"])';
            }
            elseif('id' == $key)
            {

            }
            elseif(Info::MARK == $key)
            {
                $columnArrTmp['value'] = 'SearchService::getMarkLink($data)';
            }
            elseif('title' == $key)
            {
                $columnArrTmp['htmlOptions'] = array('class' => 'title');
                $columnArrTmp['value'] = 'SearchService::getTitleLink("' . $infoType . '","' . $key . '",$data)';
            }
            else
            {
                $columnArrTmp['value'] = 'SearchService::getFilterLink("' . $infoType . '",$data["product_id"],"' . $key . '",$data["' . $key . '"],"' . $fieldInfo['type'] . '")';
            }
            $viewColumnArr[] = $columnArrTmp;
        }
        return $viewColumnArr;
    }

    public static function getFilterLink($infoType, $productId, $columnName, $value, $fieldType)
    {
        if(Info::$InputType['date'] == $fieldType)
        {
            $value = substr($value, 0, 10);
        }
        if('delete_flag' == $columnName)
        {
            return '<a href="' . Yii::app()->createUrl('info/index',
                    array('type' => $infoType, 'product_id' => $productId,
                        'filter' => $columnName . '|' . $value)) . '">' .
            CHtml::encode(CommonService::getTrueFalseName($value)) . '</a>';
        }
        else if('result_value' == $columnName && Info::TYPE_RESULT == $infoType)
        {
            $statusColorConfig = ResultInfo::getResultValueColorConfig();
            return '<a href="' . Yii::app()->createUrl('info/index',
                    array('type' => $infoType, 'product_id' => $productId,
                        'filter' => $columnName . '|' . $value)) . '" style="color:'.$statusColorConfig[$value].'">' .
            CHtml::encode($value) . '</a>';
        }
        else if('priority' == $columnName || 'severity' == $columnName)
        {
            $nameArr = array();
            if('priority' == $columnName)
            {
                if(Info::TYPE_BUG == $infoType)
                {
                    $nameArr = ProductService::getBugPriorityOption($productId);
                }
                else if(Info::TYPE_CASE == $infoType)
                {
                    $nameArr = ProductService::getCasePriorityOption($productId);
                }
            }
            else
            {
                $nameArr = ProductService::getBugSeverityOption($productId);
            }
            return '<a href="' . Yii::app()->createUrl('info/index',
                    array('type' => $infoType, 'product_id' => $productId,
                        'filter' => $columnName . '|' . $value)) . '">' .
            CHtml::encode(CommonService::getNameByValue($nameArr, $value)) . '</a>';
        }
        else
        {
            return '<a href="' . Yii::app()->createUrl('info/index',
                    array('type' => $infoType, 'product_id' => $productId,
                        'filter' => $columnName . '|' . $value)) . '">' .
            CHtml::encode($value) . '</a>';
        }
    }

    public static function getMarkLink($value)
    {
        $isAdd = '0';
        if(empty($value[Info::MARK]))
        {
            $isAdd = '1';
        }
        return '<a id="marklink' . $value['id'] . '" name="marklink' .
        $value['id'] . '" href="javascript:mark(' . $value['id'] .
        ',' . $isAdd . ');void(0);"><img src="' . Yii::app()->theme->baseUrl . '/assets/images/flag_' .
        $isAdd . '.gif"/></a>';
    }

    public static function getJsValueOption($controller, $searchConfig)
    {
        $templateNumber = Info::TEMPLATE_NUMBER;
        $jsStr = '';
        foreach($searchConfig as $key => $value)
        {
            if('priority' == $key || 'severity' == $key)
            {
                $valueStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                                '[value' . $templateNumber . ']',
                                '', $value['value'],
                                array('id' => Info::QUERY_GROUP_NAME .
                                    '_value' . $templateNumber, 'style' => 'width:100%'));
            }
            else if((Info::$InputType['option'] == $value['type'])||(Info::$InputType['multioption'] == $value['type']))
            {
                $optionValueArr = array();
                if(Info::MARK == $key)
                {
                    $optionValueArr = array(
                        '' => '',
                        '0' => Yii::t('Common', 'not marked'),
                        '1' => Yii::t('Common', 'marked')
                    );
                }
                else
                {
                    foreach($value['value'] as $valueTmp)
                    {
                        $optionValueArr[$valueTmp] = $valueTmp;
                    }
                }

                $valueStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                                '[value' . $templateNumber . ']',
                                '', $optionValueArr,
                                array('id' => Info::QUERY_GROUP_NAME .
                                    '_value' . $templateNumber, 'style' => 'width:100%'));
            }
            else
            {
                $valueStr = '<input type="text"  size="16" value="" id="' .
                        Info::QUERY_GROUP_NAME . '_value' . $templateNumber .
                        '" name="' . Info::QUERY_GROUP_NAME . '[value' . $templateNumber . ']">';
            }
            $valueStr = addslashes($valueStr);
            $valueStr = str_replace(array("\r\n", "\r", "\n"), "", $valueStr);

            $jsStr .= 'var field_' . $key . '_value = \'' . $valueStr . "';\n";
        }
        return $jsStr;
    }

    public static function getJsOperatorOption($searchConfig)
    {
        $fieldTypeOperation = CommonService::getFieldTypeOperation();
        $templateNumber = Info::TEMPLATE_NUMBER;
        $jsStr = '';

        foreach($fieldTypeOperation as $key => $value)
        {
            $operatorArr = $value;
            $selectData = array();
            foreach($operatorArr as $operator)
            {
                $selectData[$operator] = Yii::t('FieldOperator', $operator);
            }

            $operatorStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                            '[operator' . $templateNumber . ']',
                            '', $selectData,
                            array('id' => Info::QUERY_GROUP_NAME .
                                '_operator' . $templateNumber,
                                'style' => 'width:100%',
                                'onchange' => 'updateQueryValue(' .
                                $templateNumber . ',true);'));
            $operatorStr = addslashes($operatorStr);
            $operatorStr = str_replace(array("\r\n", "\r", "\n"), "", $operatorStr);
            $jsStr .= 'var field_' . $key . '_operator = \'' . $operatorStr . "';\n";
        }
        return $jsStr;
    }

    private static function getSearchOperatorOption($index, $fieldType, $operatorSelected)
    {
        $fieldTypeOperation = CommonService::getFieldTypeOperation();
        $fieldOperatorType = $fieldType;
        $selectData = array();
        $operatorArr = $fieldTypeOperation[$fieldOperatorType];
        foreach($operatorArr as $operatorTmp)
        {
            $selectData[$operatorTmp] = Yii::t('FieldOperator', $operatorTmp);
        }
        $returnStr = CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                        '[operator' . $index . ']',
                        $operatorSelected, $selectData,
                        array('id' => Info::QUERY_GROUP_NAME .
                            '_operator' . $index,
                            'style' => 'width:100%',
                            'onchange' => 'updateQueryValue(' .
                            $index . ',true);'));
        return $returnStr;
    }

    private static function getFieldOptions($index, $searchAbleFieldConfig, $fieldName)
    {

        $fieldLabelArr = array();
        foreach($searchAbleFieldConfig as $key => $value)
        {
            $fieldLabelArr[$key] = $value['label'];
        }
        return CHtml::dropDownList(Info::QUERY_GROUP_NAME .
                '[field' . $index . ']',
                $fieldName, $fieldLabelArr,
                array('id' => Info::QUERY_GROUP_NAME .
                    '_field' . $index, 'onchange' => 'updateQueryRow(' .
                    $index . ');'));
    }

}

?>
