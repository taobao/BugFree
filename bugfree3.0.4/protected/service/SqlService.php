<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of SqlService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class SqlService
{

    /**
     * get the total number according the search criteria
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   string         $infoType                    bug,case or result
     * @param   int            $productId                   product id
     * @param   string         $whereStr                    search condition string
     * @param   boolean        $isAllBasicField             is the search condition only in basic info table
     * @return  int                                         total number
     *
     */
    public static function getTotalFoundNum($infoType, $productId, $whereStr, $isAllBasicField=false)
    {
        $basicTableName = $infoType . 'view';
        if(empty($productId))
        {
            $accessIdArr = Yii::app()->user->getState('visit_product_id');
            $productFilter = 'product_id in (' . join(',', $accessIdArr) . ')';
        }
        else
        {
            $productFilter = 'product_id=' . $productId;
        }
        $searchSql = $productFilter;
        if('' != $whereStr)
        {
            $searchSql .= ' and ' . $whereStr;
        }

        if('1 = 1' == trim($whereStr))
        {
            $rawData = Yii::app()->db->createCommand()
                            ->select('count(*) as totalNum')
                            ->from('{{' . $infoType . '_info}}')
                            ->where($searchSql)
                            ->queryRow();
        }
        else
        {
            if(empty($productId))
            {
                $rawData = Yii::app()->db->createCommand()
                                ->select('count({{' . $basicTableName . '}}.id) as totalNum')
                                ->from('{{' . $basicTableName . '}}')
                                ->where($searchSql)
                                ->queryRow();
            }
            else
            {
                $productInfo = Product::model()->findByPk($productId);
                if(!empty($productInfo) &&
                        ("(({{bugview}}.module_name LIKE '" . $productInfo['name'] . "/%' or {{bugview}}.module_name = '" . $productInfo['name'] . "'))" == $whereStr))
                {
                    $rawData = Yii::app()->db->createCommand()
                                    ->select('count(*) as totalNum')
                                    ->from('{{' . $infoType . '_info}}')
                                    ->where($productFilter)
                                    ->queryRow();
                }
                else
                {
                    if($isAllBasicField)
                    {
                        $rawData = Yii::app()->db->createCommand()
                                        ->select('count({{' . $basicTableName . '}}.id) as totalNum')
                                        ->from('{{' . $basicTableName . '}}')
                                        ->where($searchSql)
                                        ->queryRow();
                    }
                    else
                    {
                        $addOnTableName = 'etton' . $infoType . '_' . $productId;
                        $rawData = Yii::app()->db->createCommand()
                                        ->select('count({{' . $basicTableName . '}}.id) as totalNum')
                                        ->from('{{' . $basicTableName . '}}')
                                        ->join('{{' . $addOnTableName . '}}',
                                                '{{' . $basicTableName . '}}' . '.id=' .
                                                '{{' . $addOnTableName . '}}' . '.' . $infoType . '_id and ' . $productFilter)
                                        ->where($searchSql)
                                        ->queryRow();
                    }
                }
            }
        }
        return $rawData['totalNum'];
    }

    private static function handleSelectFieldArr($type, $showFieldArr)
    {
        $showFieldNum = count($showFieldArr);
        for($i = 0; $i < $showFieldNum; $i++)
        {
            if('id' == $showFieldArr[$i])
            {
                $showFieldArr[$i] = '{{' . $type . 'view}}.id';
            }
            else if(Info::MARK == $showFieldArr[$i])
            {
                $markTableName = 'map_user_' . $type;
                $showFieldArr[$i] = '{{' . $markTableName . '}}.id as mark';
            }
        }
        return $showFieldArr;
    }

    /**
     * get search sql according to the search condition
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $searchFieldConfig           all the seachable field's information
     * @param   string         $infoType                    bug,case or result
     * @param   int            $productId                   product id
     * @param   array          $showFieldArr                show field array
     * @param   string         $whereStr                    search condition
     * @param   boolean        $isAllBasicField             is the search condition only in basic info table
     * @return  string                                      search sql
     *
     */
    public static function getRawDataSql($searchFieldConfig, $infoType, $productId, $showFieldArr, $whereStr, $isAllBasicField = false)
    {
        $getMarkInfo = false;
        if(in_array(Info::MARK, $showFieldArr))
        {
            $getMarkInfo = true;
        }
        $columnArr = self::handleSelectFieldArr($infoType, $showFieldArr);
        $basicTableName = $infoType . 'view';
        $markTableName = 'map_user_' . $infoType;
        $searchSql = $whereStr;
        if(empty($productId))
        {
            $accessIdArr = Yii::app()->user->getState('visit_product_id');
            $productSql = 'product_id in (' . join(',', $accessIdArr) . ')';
        }
        else
        {
            $productSql = 'product_id=' . $productId;
        }

        if('' == $searchSql)
        {
            $searchSql = $productSql;
        }
        else
        {
            $searchSql = $productSql . ' and ' . $searchSql;
        }

        if(empty($productId))
        {
            if($getMarkInfo)
            {
                $rawDatatxt = Yii::app()->db->createCommand()
                                ->select(join(',', $columnArr))
                                ->from('{{' . $basicTableName . '}}')
                                ->leftJoin('{{' . $markTableName . '}}',
                                        '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                                ->where($searchSql)->text;
            }
            else
            {
                $rawDatatxt = Yii::app()->db->createCommand()
                                ->select(join(',', $columnArr))
                                ->from('{{' . $basicTableName . '}}')
                                ->where($searchSql)->text;
            }
        }
        else
        {
            if(false == $isAllBasicField)
            {
                $addOnTableName = 'etton' . $infoType . '_' . $productId;
                if($getMarkInfo)
                {
                    $rawDatatxt = Yii::app()->db->createCommand()
                                    ->select(join(',', $columnArr))
                                    ->from('{{' . $basicTableName . '}}')
                                    ->leftJoin('{{' . $addOnTableName . '}}',
                                            '{{' . $basicTableName . '}}' . '.id=' . '{{' . $addOnTableName . '}}' .
                                            '.' . $infoType . '_id and product_id=' . $productId)
                                    ->leftJoin('{{' . $markTableName . '}}',
                                            '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                                    ->where($searchSql)->text;
                }
                else
                {
                    $rawDatatxt = Yii::app()->db->createCommand()
                                    ->select(join(',', $columnArr))
                                    ->from('{{' . $basicTableName . '}}')
                                    ->leftJoin('{{' . $addOnTableName . '}}',
                                            '{{' . $basicTableName . '}}' . '.id=' . '{{' . $addOnTableName . '}}' .
                                            '.' . $infoType . '_id and product_id=' . $productId)
                                    ->where($searchSql)->text;
                }
            }
            else
            {
                if($getMarkInfo)
                {
                    $rawDatatxt = Yii::app()->db->createCommand()
                                    ->select(join(',', $columnArr))
                                    ->from('{{' . $basicTableName . '}}')
                                    ->leftJoin('{{' . $markTableName . '}}',
                                            '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                                    ->where($searchSql)->text;
                }
                else
                {
                    $rawDatatxt = Yii::app()->db->createCommand()
                                    ->select(join(',', $columnArr))
                                    ->from('{{' . $basicTableName . '}}')
                                    ->where($searchSql)->text;
                }
            }
        }
        return $rawDatatxt;
    }

    /**
     * get pre and next search sql according to the search condition
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $searchFieldConfig           all the seachable field's information
     * @param   string         $infoType                    bug,case or result
     * @param   int            $productId                   product id
     * @param   string         $whereStr                    search condition
     * @param   array          $sortArr                     sort config
     * @param   boolean        $isAllBasicField             is the search condition only in basic info table
     * @return  string                                      pre next sql
     *
     */
    public static function getPreNextSql($searchFieldConfig, $infoType, $productId, $whereStr, $sortArr, $isAllBasicField = false)
    {
        $basicTableName = $infoType . 'view';
        $markTableName = 'map_user_' . $infoType;
        if(empty($productId))
        {
            $accessIdArr = Yii::app()->user->getState('visit_product_id');
            $searchSql = 'product_id in (' . join(',', $accessIdArr) . ')';
            if('' != $whereStr)
            {
                $searchSql .= ' and ' . $whereStr;
            }
            $preNextSql = Yii::app()->db->createCommand()
                            ->select('{{' . $basicTableName . '}}.id as id,{{' . $markTableName . '}}.id as mark')
                            ->from('{{' . $basicTableName . '}}')
                            ->leftJoin('{{' . $markTableName . '}}',
                                    '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                            ->where($searchSql)
                    ->text;
        }
        else
        {
            $searchSql = 'product_id=' . $productId;
            if('' != $whereStr)
            {
                $searchSql .= ' and ' . $whereStr;
            }
            if($isAllBasicField)
            {
                $preNextSql = Yii::app()->db->createCommand()
                                ->select('{{' . $basicTableName . '}}.id as id,{{' . $markTableName . '}}.id as mark')
                                ->from('{{' . $basicTableName . '}}')
                                ->leftJoin('{{' . $markTableName . '}}',
                                        '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                                ->where($searchSql)
                        ->text;
            }
            else
            {
                $addOnTableName = 'etton' . $infoType . '_' . $productId;
                $preNextSql = Yii::app()->db->createCommand()
                                ->select('{{' . $basicTableName . '}}.id as id,{{' . $markTableName . '}}.id as mark')
                                ->from('{{' . $basicTableName . '}}')
                                ->leftJoin('{{' . $addOnTableName . '}}',
                                        '{{' . $basicTableName . '}}' . '.id=' . '{{' . $addOnTableName . '}}' .
                                        '.' . $infoType . '_id and product_id=' . $productId)
                                ->leftJoin('{{' . $markTableName . '}}',
                                        '{{' . $markTableName . '}}' . '.info_id=' . '{{' . $basicTableName . '}}' . '.' . 'id and test_user_id = ' . yii::app()->user->id)
                                ->where($searchSql)
                        ->text;
            }
        }
        $sortStr = '';

        foreach($sortArr as $field => $isDesc)
        {
            if($isDesc)
            {
                $sortStr .= $field . ' desc,';
            }
            else
            {
                $sortStr .= $field . ',';
            }
        }

        if(strlen($sortStr) > 0)
        {
            $sortStr = substr($sortStr, 0, strlen($sortStr) - 1);
            $preNextSql .= ' order by ' . $sortStr;
        }
        return $preNextSql;
    }

    public static function handleRawData($rawData, $infoType, $searchFieldConfig, $columnArr, $productId)
    {
        $needHandleFieldArr = array();
        $needHandleFieldTypeArr = array(Info::$InputType['multipeople']);
        foreach($columnArr as $columnName)
        {
            if(isset($searchFieldConfig[$columnName]))
            {
                $fieldInfo = $searchFieldConfig[$columnName];
                if((true == $fieldInfo['isBasic']) && in_array($fieldInfo['type'], $needHandleFieldTypeArr) && ('mail_to' != $columnName))
                {
                    $needHandleFieldArr[$columnName] = $fieldInfo['type'];
                }
            }
        }

        $priorityArr = array();
        $severityArr = array();
        if(!empty($productId))
        {
            if(Info::TYPE_BUG == $infoType)
            {
                $priorityArr = ProductService::getBugPriorityOption($productId);
                $severityArr = ProductService::getBugSeverityOption($productId);
            }
            else
            {
                $priorityArr = ProductService::getCasePriorityOption($productId);
            }
        }

        $handleResultArr = array();
        $rawDataNum = count($rawData);
        for($i = 0; $i < $rawDataNum; $i++)
        {
            foreach($needHandleFieldArr as $needHandledKey => $type)
            {
                $rawData[$i][$needHandledKey] = self::parseRawData($rawData[$i][$needHandledKey], $infoType, $needHandledKey, $type);
            }
            if(isset($rawData[$i]['delete_flag']))
            {
                $rawData[$i]['delete_flag'] = CommonService::getTrueFalseName($rawData[$i]['delete_flag']);
            }

            if(isset($rawData[$i]['severity']) && (!empty($severityArr)))
            {
                $rawData[$i]['severity'] = CommonService::getNameByValue($severityArr, $rawData[$i]['severity']);
            }

            if(isset($rawData[$i]['priority']) && (!empty($priorityArr)))
            {
                $rawData[$i]['priority'] = CommonService::getNameByValue($priorityArr, $rawData[$i]['priority']);
            }
        }
        return $rawData;
    }

    private static function parseRawData($data, $infoType, $fieldName, $fieldType)
    {
        $rawData = $data;
        if(Info::$InputType['people'] == $fieldType)
        {
            $data = CommonService::getUserRealName($data);
        }
        elseif(Info::$InputType['multipeople'] == $fieldType)
        {
            $data = CommonService::getMultiUserRealName($data);
        }
        return $data;
    }

    private static function checkDateValidate($searchFieldConfig, $searchRowConditionArr)
    {
        $resultStr = '';
        foreach($searchRowConditionArr as $rowCondtion)
        {
            $fieldName = $rowCondtion['field'];
            $fieldType = $searchFieldConfig[$fieldName]['type'];
            $fieldValue = $rowCondtion['value'];
            $fieldValue = trim($fieldValue);
            if(!empty($fieldValue) && (Info::$InputType['date'] == $fieldType) &&
                    !preg_match('/^-?[1-9]\d*$|^0$|^(19|20)\d{2}-(0?\d|1[012])-(0?\d|[12]\d|3[01])$/', $fieldValue))
            {
                $resultStr = '[' . $fieldValue . ']' . Yii::t('Common', 'Please use valid date format. For example, 2009-10-8 or -7.');
                return $resultStr;
            }
        }
        return $resultStr;
    }

    /**
     * check if the search condition's parenthese is right
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $searchFieldConfig           all the seachable field's information
     * @param   array          $searchRowConditionArr       search condition
     * @return  string                                      search condition string
     *
     */
    private static function checkParentheses($searchRowConditionArr)
    {
        $resultStr = '';
        $parenthesesArr = array();
        foreach($searchRowConditionArr as $rowCondtion)
        {
            $leftParentheses = $rowCondtion['leftParenthesesName'];
            $rightParentheses = $rowCondtion['rightParenthesesName'];
            if(('(' == $leftParentheses) && ('' == $rightParentheses))
            {
                $parenthesesArr[] = $leftParentheses;
            }
            else if(('' == $leftParentheses) && (')' == $rightParentheses))
            {
                if(empty($parenthesesArr))
                {
                    $resultStr = 'error parentheses setting';
                    return $resultStr;
                }
                else
                {
                    array_pop($parenthesesArr);
                }
            }
        }
        if(!empty($parenthesesArr))
        {
            $resultStr = 'error parentheses setting';
        }
        return $resultStr;
    }

    /**
     * get search condition according to the search row array
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $searchFieldConfig           all the seachable field's information
     * @param   string         $type                        bug,case or result
     * @param   array          $searchRowConditionArr       search condition
     * @return  string                                      search condition string
     *
     */
    public static function baseGetGroupQueryStr($searchFieldConfig, $type, $searchRowConditionArr)
    {
        $result = array();
        $checkDateResultStr = self::checkDateValidate($searchFieldConfig, $searchRowConditionArr);
        if('' != $checkDateResultStr)
        {
            $result['status'] = CommonService::$ApiResult['FAIL'];
            $result['detail'] = $checkDateResultStr;
            return $result;
        }
        $checkParenthesesResultStr = self::checkParentheses($searchRowConditionArr);
        if('' != $checkParenthesesResultStr)
        {
            $result['status'] = CommonService::$ApiResult['FAIL'];
            $result['detail'] = $checkParenthesesResultStr;
            return $result;
        }
        $fieldName = 'field';
        $operatorName = 'operator';
        $valueName = 'value';
        $andOrName = 'andor';
        $leftParenthesesName = 'leftParenthesesName';
        $rightParenthesesName = 'rightParenthesesName';

        $queryGroup = array();
        foreach($searchRowConditionArr as $rowCondtion)
        {
            $whereStr = ' ';
            $tempQueryStr = self::baseGetFieldQueryStr($searchFieldConfig, $type,
                            $rowCondtion[$fieldName], $rowCondtion[$operatorName], $rowCondtion[$valueName]);
            if($tempQueryStr == '')
            {
                if($rowCondtion[$leftParenthesesName] == "(" && $rowCondtion[$rightParenthesesName] == ")")
                {

                }
                else if($rowCondtion[$leftParenthesesName] == "(")
                {
                    $whereStr = "(";
                    $queryGroup[] = $whereStr;
                }
                else if($rowCondtion[$rightParenthesesName] == ")")
                {
                    $whereStr = ")";
                    if(ucfirst($rowCondtion[$andOrName]) == 'And')
                    {
                        $whereStr = $whereStr . ' AND ';
                    }
                    elseif(ucfirst($rowCondtion[$andOrName]) == 'Or')
                    {
                        $whereStr = $whereStr . ' OR  ';
                    }

                    $preCondition = $queryGroup[count($queryGroup) - 1];
                    if("(" == $preCondition)
                    {
                        array_pop($queryGroup);
                    }
                    else
                    {
                        $preCondition = substr($preCondition, 0, strlen($preCondition) - strlen(" AND "));
                        $queryGroup[count($queryGroup) - 1] = $preCondition . " " . $whereStr;
                    }
                }
            }
            else
            {
                $whereStr = $tempQueryStr;
                if($rowCondtion[$leftParenthesesName] == "(")
                {
                    $whereStr = "(" . $whereStr;
                }
                if($rowCondtion[$rightParenthesesName] == ")")
                {
                    $whereStr = $whereStr . ")";
                }

                if(ucfirst($rowCondtion[$andOrName]) == 'And')
                {
                    $whereStr = $whereStr . ' AND ';
                }
                elseif(ucfirst($rowCondtion[$andOrName]) == 'Or')
                {
                    $whereStr = $whereStr . ' OR  ';
                }
                $queryGroup[] = $whereStr;
            }
        }
        $sqlStr = '';
        if($queryGroup == null)
        {
            $sqlStr = ' 1 = 1 ';
        }
        else
        {
            $ResultQueryStr = join(' ', $queryGroup);
            $ResultQueryStr = '(' . substr($ResultQueryStr, 0, strlen($ResultQueryStr) - strlen(" AND ")) . ')';
            $sqlStr = $ResultQueryStr;
        }
        $result['status'] = CommonService::$ApiResult['SUCCESS'];
        $result['detail'] = $sqlStr;
        return $result;
    }

    private static function handleEmptyQuery($searchFieldConfig, $basicTableName, $fieldName, $operatorName, $fieldValue)
    {
        if(Info::MARK == $fieldName || 'delete_flag' == $fieldName)
        {
            return '';
        }
        if(true == $searchFieldConfig[$fieldName]['isBasic'])
        {
            $fieldName = $basicTableName . '.' . $fieldName;
        }
        if($operatorName == '!=')
        {
            return '(' . $fieldName . ' is not null and ' . $fieldName . ' <>"")';
        }
        else if($operatorName == '=')
        {
            return '(' . $fieldName . ' is null or ' . $fieldName . ' ="")';
        }
        return '';
    }

    /**
     * Get query string with one field
     *
     * @author                          Yupeng Lee<leeyupeng@gmail.com>
     * @param  string  $fieldName       FieldName
     * @param  string  $operatorName    =,<,>,<= eg.
     * @param  string  $fieldValue      FieldValue
     * @return string                   Query string for SQL
     */
    private static function baseGetFieldQueryStr($searchFieldConfig, $type, $fieldName, $operatorName, $fieldValue)
    {
        $basicTableName = '{{' . $type . 'view}}';
        $fieldValue = trim($fieldValue);
        $fieldValue = addslashes($fieldValue);
        //handle search value %,_
        if($operatorName == 'LIKE' || $operatorName == 'NOT LIKE')
        {
            $fieldValue = str_replace('%', '\%', $fieldValue);
            $fieldValue = str_replace('_', '\_', $fieldValue);
        }

        $queryStr = '';
        if($fieldValue == '')
        {
            return self::handleEmptyQuery($searchFieldConfig, $basicTableName, $fieldName, $operatorName, $fieldValue);
        }
        elseif(Info::MARK == $fieldName)
        {
            $inOrNotIn = ' not in ';
            if('1' == $fieldValue)
            {
                $inOrNotIn = ' in ';
            }
            return $basicTableName . '.id ' . $inOrNotIn .
            ' (select info_id from {{map_user_' .
            $type . '}} where test_user_id=' . Yii::app()->user->id . ')';
        }
        elseif('delete_flag' == $fieldName)
        {
            $fieldValue = CommonService::getTrueFalseValue($fieldValue);
        }
//basic search field's name should be transfer to user id
        $fieldType = $searchFieldConfig[$fieldName]['type'];
        if(Info::$InputType['date'] == $fieldType)
        {//date related search
            if(preg_match('/^-?[1-9]\d*$|^0$/', $fieldValue))
            {//如果输入为整数，则进行日期的换算
                $fieldValue = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $fieldValue, date("Y")));
            }
        }
        elseif((Info::$InputType['multipeople'] == $fieldType) &&
                (true == $searchFieldConfig[$fieldName]['isBasic']))
        {
            $userNameArr = CommonService::splitStringToArray(',', $fieldValue);
            $userIdArr = array();
            foreach($userNameArr as $userName)
            {
                $userInfo = TestUserService::getUserInfoByRealname($userName);
                if(empty($userInfo))
                {
                    $userIdArr[] = '-99999';
                }
                else
                {
                    $userIdArr[] = $userInfo['id'];
                }
            }
            $fieldValue = join(',', $userIdArr);
        }

        if($operatorName == 'LIKE')
        {
            $queryStr = "LIKE '%{$fieldValue}%' ";
        }
        elseif($operatorName == 'NOT LIKE')
        {
            $queryStr = "NOT LIKE '%{$fieldValue}%' ";
        }
        elseif($operatorName == 'UNDER')
        {
            $queryStr = "LIKE '{$fieldValue}%' ";
        }
        elseif($operatorName == '!=')
        {
            if(Info::$InputType['date'] == $fieldType)
            {
                $queryStr = "NOT " . self::sysStrToDateSql($fieldValue);
            }
            else
            {
                $queryStr = "<> '" . $fieldValue . "' ";
            }
        }
        elseif($operatorName == '=')
        {
            if(Info::$InputType['date'] == $fieldType)
            {
                $queryStr = self::sysStrToDateSql($fieldValue);
            }
            elseif(Info::$InputType['multipeople'] == $fieldType)
            {
                $queryStr = "LIKE '%" . $fieldValue . "%' ";
            }
            else
            {
                $queryStr = $operatorName . " '{$fieldValue}' ";
            }
        }
        elseif($operatorName == 'IN')
        {
            $fieldValueArr = CommonService::splitStringToArray(',', $fieldValue);
            $inValueStr = '';
            foreach ($fieldValueArr as $valueTmp)
            {
                if('' == $inValueStr)
                {
                    $inValueStr = '"'.$valueTmp.'"';
                }
                else
                {
                    $inValueStr .= ',"'.$valueTmp.'"';
                }
            }
            $queryStr = "IN ({$inValueStr}) ";
        }
        else
        {
            if(($operatorName == '>' || $operatorName == '<=') && (Info::$InputType['date'] == $fieldType))
            {
                $dateTimeArray = explode(" ", self::sysStrToDateSql($fieldValue));
                $fieldValue = $dateTimeArray[4] . ' ' . $dateTimeArray[5];
                $queryStr = $operatorName . " {$fieldValue} ";
            }
            elseif(($operatorName == '>=' || $operatorName == '<') && (Info::$InputType['date'] == $fieldType))
            {
                $dateTimeArray = explode(" ", self::sysStrToDateSql($fieldValue));
                $fieldValue = $dateTimeArray[1] . ' ' . $dateTimeArray[2];
                $queryStr = $operatorName . " {$fieldValue} ";
            }
            else
            {
                $queryStr = $operatorName . " '{$fieldValue}' ";
            }
        }
        if('' != $queryStr)
        {
            if(true == $searchFieldConfig[$fieldName]['isBasic'])
            {
                $fieldName = $basicTableName . '.' . $fieldName;
            }
            if($operatorName == 'UNDER')
            {
                $likeFieldValue = str_replace('%', '\%', $fieldValue);
                $likeFieldValue = str_replace('_', '\_', $likeFieldValue);
                $queryStr = '(' . $fieldName . " LIKE '{$likeFieldValue}" .
                        ProductModule::MODULE_SPLITTER . "%' or " .
                        $fieldName . " = '{$fieldValue}')";
            }
            else
            {
                $queryStr = $fieldName . ' ' . $queryStr;
            }
        }

        if($operatorName == '!=')
        {
            $queryStr = '(' . $queryStr . ' or ' . $fieldName . ' is null or ' . $fieldName . ' ="")';
        }

        return $queryStr;
    }

    private static function dbCreateIN($itemListStr)
    {
        $itemListArr = CommonService::splitStringToArray(',', $itemListStr);
        $tmpArr = array();
        foreach($itemListArr as $item)
        {
            $tmpArr[] = "'$item'";
        }
        return ' IN (' . join(',', $tmpArr) . ') ';
    }

    /**
     * Create the date SQL
     *
     * @author                     Yupeng Lee <leeyupeng@gmail.com>
     * @param    string $DateStr
     * @return   string            SQL just like: BETWEEN "2008-01-01 00:00:00" AND "2008-01-01 23:59:59"
     */
    private static function sysStrToDateSql($DateStr)
    {
        $pattern = "([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) *([0-9]{0,2}):*([0-9]{0,2}):*([0-9]{0,2})";
        $startDateTime = "";
        $endDateTime = "";
        if(preg_match("/$pattern/", $DateStr, $ereg))
        {
            $ereg[2] = str_pad($ereg[2], 2, '0', STR_PAD_LEFT);
            $ereg[3] = str_pad($ereg[3], 2, '0', STR_PAD_LEFT);
            $startDateTime = $ereg[1] . '-' . $ereg[2] . '-' . $ereg[3];
            $endDateTime = $startDateTime;

            if($ereg[4] != '')
            {
                $ereg[4] = str_pad($ereg[4], 2, '0', STR_PAD_LEFT);
                $startDateTime .= ' ' . $ereg[4];
                $endDateTime .= $startDateTime;
            }
            else
            {
                $startDateTime .= ' 00';
                $endDateTime .= ' 23';
            }
            if($ereg[5] != '')
            {
                $ereg[5] = str_pad($ereg[5], 2, '0', STR_PAD_LEFT);
                $startDateTime .= ':' . $ereg[5];
                $endDateTime .= $startDateTime;
            }
            else
            {
                $startDateTime .= ':00';
                $endDateTime .= ':59';
            }
            if($ereg[6] != '')
            {
                $ereg[6] = str_pad($ereg[6], 2, '0', STR_PAD_LEFT);
                $startDateTime .= ':' . $ereg[6];
                $endDateTime .= $startDateTime;
            }
            else
            {
                $startDateTime .= ':00';
                $endDateTime .= ':59';
            }
        }
        return "BETWEEN '" . $startDateTime . "' AND '" . $endDateTime . "'";
    }

}

?>
