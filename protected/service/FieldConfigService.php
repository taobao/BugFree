<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of FieldConfigService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class FieldConfigService
{
    const ERROR_EMPTY = 'can not be empty';
    const ERROR_MATCH = 'match failed';
    const ERROR_UNIQUE = 'value already existed';
    const ERROR_USER_NOT_FOUND = 'user not found';
    const ERROR_INPUT_INVALID = 'input value is invalid';
    const ERROR_255_LONG = 'Limitted 255 character';
    const ERROR_65535_LONG = 'Limitted 65535 character';

    const ERROR_FIELD_NAME_EXIST = 'field name already existed';
    const ERROR_FIELD_NAME_BASIC_EXIST = 'field name already existed in basic table';
    const ERROR_FIELD_NAME_KEYWORD = 'field name can not be database keyword';

    public static $mySqlKeyWords = array('add', 'all', 'alter', 'analyze',
        'and', 'as', 'asc', 'asensitive', 'before', 'between',
        'bigint', 'binary', 'blob', 'both', 'by', 'call', 'cascade',
        'case', 'change', 'char', 'character', 'check', 'collate', 'column',
        'condition', 'connection', 'constraint', 'continue', 'convert', 'create',
        'cross', 'current_date', 'current_time', 'current_timestamp', 'current_user',
        'cursor', 'database', 'databases', 'day_hour', 'day_microsecond', 'day_minute',
        'day_second', 'dec', 'decimal', 'declare', 'default', 'delayed', 'delete',
        'desc', 'describe', 'deterministic', 'distinct', 'distinctrow', 'div',
        'double', 'drop', 'dual', 'each', 'else', 'elseif', 'enclosed',
        'escaped', 'exists', 'exit', 'explain', 'false', 'fetch', 'float',
        'float4', 'float8', 'for', 'force', 'foreign', 'from', 'fulltext',
        'goto', 'grant', 'group', 'having', 'high_priority', 'hour_microsecond',
        'hour_minute', 'hour_second', 'if', 'ignore', 'in', 'index', 'infile',
        'inner', 'inout', 'insensitive', 'insert', 'int', 'int1', 'int2', 'int3',
        'int4', 'int8', 'integer', 'interval', 'into', 'is', 'iterate', 'join',
        'key', 'keys', 'kill', 'label', 'leading', 'leave', 'left', 'like', 'limit',
        'linear', 'lines', 'load', 'localtime', 'localtimestamp', 'lock', 'long',
        'longblob', 'longtext', 'loop', 'low_priority', 'match', 'mediumblob',
        'mediumint', 'mediumtext', 'middleint', 'minute_microsecond', 'minute_second',
        'mod', 'modifies', 'natural', 'not', 'no_write_to_binlog', 'null',
        'numeric', 'on', 'optimize', 'option', 'optionally', 'or', 'order',
        'out', 'outer', 'outfile', 'precision', 'primary', 'procedure', 'purge',
        'raid0', 'range', 'read', 'reads', 'real', 'references', 'regexp',
        'release', 'rename', 'repeat', 'replace', 'require', 'restrict',
        'return', 'revoke', 'right', 'rlike', 'schema', 'schemas',
        'second_microsecond', 'select', 'sensitive', 'separator',
        'set', 'show', 'smallint', 'spatial', 'specific', 'sql',
        'sqlexception', 'sqlstate', 'sqlwarning', 'sql_big_result',
        'sql_calc_found_rows', 'sql_small_result', 'ssl', 'starting',
        'straight_join', 'table', 'terminated', 'then', 'tinyblob',
        'tinyint', 'tinytext', 'to', 'trailing', 'trigger', 'true',
        'undo', 'union', 'unique', 'unlock', 'unsigned', 'update',
        'usage', 'use', 'using', 'utc_date', 'utc_time', 'utc_timestamp',
        'values', 'varbinary', 'varchar', 'varcharacter', 'varying',
        'when', 'where', 'while', 'with', 'write', 'x509', 'xor', 'year_month', 'zerofill');

    /**
     * get active custom field name
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $productId          product id
     * @param   string      $type               bug,case or result
     * @return  array                           field operaton string
     */
    public static function getActiveCustomFieldName($productId, $type)
    {
        $returnStr = '';
        $fieldNameArr = array($type . '_id');
        $fieldInfoArr = FieldConfig::model()->findAllByAttributes(array('product_id' => $productId,
                    'type' => $type, 'is_dropped' => '0'));
        foreach($fieldInfoArr as $fieldInfo)
        {
            $fieldNameArr[] = $fieldInfo['field_name'];
        }
        return join(',', $fieldNameArr);
    }

    /**
     * get field operation
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $id                 field config object id
     * @param   int         $productId          product id
     * @param   string      $type               bug,case or result
     * @param   string      $isDropped          is dropped
     * @param   string      $editInResult       edit in result
     * @return  array                           field operaton string
     */
    public static function getFieldOperation($id, $productId, $type, $isDropped, $editInResult)
    {
        $returnStr = '';
        $newIsDropped = 1 - $isDropped;
        if((CommonService::$TrueFalseStatus['TRUE'] == $editInResult) &&
                ('result' == $type))
        {
            return $returnStr;
        }
        $returnStr .= '<a class="with_underline" href="' .
                Yii::app()->createUrl('fieldConfig/edit', array('id' => $id,
                    'type' => $type, 'product_id' => $productId)) . '">' . Yii::t('Common', 'Edit') . '</a>|';
        $returnStr .= '<a class="with_underline" href="' .
                Yii::app()->createUrl('fieldConfig/disable',
                        array('id' => $id, 'type' => $type, 'product_id' => $productId,
                            'is_dropped' => $newIsDropped)) . '" onclick="return confirm(\'' .
                Yii::t('Common', 'Are you sure?') . '\');">';
        if(CommonService::$TrueFalseStatus['TRUE'] == $isDropped)
        {
            $returnStr .= Yii::t('Common', 'Enable') . '</a>';
        }
        else
        {
            $returnStr .= Yii::t('Common', 'Disable') . '</a>';
        }
        return $returnStr;
    }

    /**
     * get field operation
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string         $action          action
     * @param   array          $fieldInfo       field config information
     * @return  boolean                         is editable
     */
    private static function getEditableFlag($action, $fieldInfo)
    {
        if(BugInfo::ACTION_ACTIVATE == $action)
        {
            $action = BugInfo::ACTION_OPEN_EDIT;
        }
        if(false !== strpos($action, '_'))
        {
            $actionArr = CommonService::splitStringToArray('_', $action);
            $action = $actionArr[0];
        }
        $editableFlag = false;
        $fieldEditableAtStr = $fieldInfo->editable_action;
        $editableActionArr = CommonService::splitStringToArray(',', $fieldEditableAtStr);
        if(in_array($action, $editableActionArr))
        {
            $editableFlag = true;
        }
        return $editableFlag;
    }

    public static function getBugCopyableFields($productId)
    {
        $bugFieldArr = ProductService::getProductAllFieldInfo('bug', $productId);
        $copyAbleFieldArr = array();
        foreach($bugFieldArr as $bugField)
        {
            if(CommonService::$TrueFalseStatus['TRUE'] == $bugField['edit_in_result'])
            {
                $copyAbleFieldArr[] = $bugField;
            }
        }
        return $copyAbleFieldArr;
    }

    /**
     * get info's custom html element
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   int           $productId              product id
     * @param   string        $infoType               bug,case or result
     * @param   CController   $controller             page's controller
     * @param   string        $action                 page's action
     * @param   array         $errorInfos             page's error info
     * @param   array         $customInfos            custom attributes
     * @return  array                                 custom html element
     *
     */
    public static function getCustomFieldConfig($productId, $infoType, $controller, $action, $errInfos=array(), $customInfos=array())
    {
        $result = array();
        $customFieldInfos = ProductService::getProductAllFieldInfo($infoType, $productId);
        if('bug' == $infoType)
        {
            $copyFieldArr = self::getBugCopyableFields($productId);
            $fieldNum = count($copyFieldArr);
            for($i = 0; $i < $fieldNum; $i++)
            {
                $copyFieldArr[$i]['belong_group'] = $copyFieldArr[$i]['result_group'];
            }
            $customFieldInfos = array_merge($customFieldInfos, $copyFieldArr);
        }
        $errorKeyArr = array();
        foreach($errInfos as $key => $value)
        {
            $errorKeyArr[] = $key;
        }

        foreach($customFieldInfos as $fieldInfo)
        {
            $fieldInfo = (object) $fieldInfo;
            $fieldName = $fieldInfo->field_name;

            //normal code
//            $editableFlag = true;
//            if('bug' == $infoType)
//            {
//                $editableFlag = self::getEditableFlag($action, $fieldInfo);
//            }
//            else
//            {
//                if((Info::ACTION_OPEN != $action) && (Info::ACTION_OPEN_EDIT != $action) && (ResultInfo::ACTION_BATCH_OPEN != $action))
//                {
//                    $editableFlag = false;
//                }
//            }
            //product 154 XProject field readonly
            $editableFlag = true;
            if(154 == $productId && ('XProject' == $fieldName))
            {
                $editableFlag = false;
            }
            else
            {
                if('bug' == $infoType)
                {
                    $editableFlag = self::getEditableFlag($action, $fieldInfo);
                }
                else
                {
                    if((Info::ACTION_OPEN != $action) && (Info::ACTION_OPEN_EDIT != $action) && (ResultInfo::ACTION_BATCH_OPEN != $action))
                    {
                        $editableFlag = false;
                    }
                }
            }
            // end


            $errorClass = "";
            if(in_array($fieldName, $errorKeyArr))
            {
                $errorClass = " error";
            }
            $requiredClass = '';
            if((CommonService::$TrueFalseStatus['TRUE'] == $fieldInfo->is_required) && (true == $editableFlag))
            {
                $requiredClass = 'required';
            }
            $fieldStrTmp = '';

            $fieldStrTmp .= '<div class="row"><label style="font-weight:normal;" class="' . $errorClass . '" for="Custom_' . $fieldName . '">' . $fieldInfo->field_label . '</label>';
            if(isset($customInfos[$fieldName]))
            {
                $fieldStrTmp .= self::getFieldInputStr($controller, $fieldInfo, $editableFlag, $errorClass, $requiredClass, $customInfos[$fieldName]) . '</div>';
            }
            else
            {
                $fieldStrTmp .= self::getFieldInputStr($controller, $fieldInfo, $editableFlag, $errorClass, $requiredClass, null) . '</div>';
            }

            if(empty($result[$fieldInfo->belong_group]))
            {
                $result[$fieldInfo->belong_group] = $fieldStrTmp;
            }
            else
            {
                $result[$fieldInfo->belong_group] .= $fieldStrTmp;
            }
        }
        return $result;
    }

    /**
     * get bug info's custom attributes according to the action
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   array         $targetInfo             been filtered custom attributes
     * @param   string        $action                 bug action
     * @param   int           $productId              product id
     * @return  array                                 filtered custom attributes
     *
     */
    public static function getBugAvailableValueByAction($targetInfo, $action, $productId)
    {
        //open and reactive should edit the same custom field
        if(BugInfo::ACTION_ACTIVATE == $action)
        {
            $action == Info::ACTION_OPEN;
        }
        $availableAttributes = array();
        $returnInfo = array();
        $customFieldInfos = ProductService::getProductAllFieldInfo(Info::TYPE_BUG, $productId);
        foreach($customFieldInfos as $fieldInfo)
        {
            $fieldInfo = (object) $fieldInfo;
            $fieldName = $fieldInfo->field_name;
            $editableActionArr = CommonService::splitStringToArray(',', $fieldInfo->editable_action);
            if(in_array($action, $editableActionArr))
            {
                $availableAttributes[] = $fieldName;
            }
        }
        foreach($targetInfo as $key => $value)
        {
            if(in_array($key, $availableAttributes))
            {
                $returnInfo[$key] = $value;
            }
        }
        return $returnInfo;
    }

    /**
     * get custom field's select option data
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   string        $valueStr               custom field's value string
     * @return  array                                 custom field's select data
     *
     */
    public static function getSelectOption($valueStr)
    {
        if(CommonService::startsWith($valueStr, 'http://'))
        {
            $valueStr = CommonService::curlGetData($valueStr);
        }
        return CommonService::splitStringToArray(',', $valueStr);
    }

    /**
     * get ac field's select option data
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   string        $valueStr               custom field's value string
     * @return  array                                 custom field's select data
     *
     */
    public static function getAutoCompleteValueStr($dataStr, $q, $limit=1)
    {
        if(CommonService::startsWith($dataStr, 'http://'))
        {
            $outApiUrl = '';
            if(false !== strpos($dataStr, '?'))
            {
                $outApiUrl = $dataStr . urlencode($q) . '&limit=' . $limit;
            }
            else
            {
                $outApiUrl = $dataStr . urlencode($q) . '?limit=' . $limit;
            }
            $valueStr = CommonService::curlGetData($outApiUrl);
        }
        else
        {
            $valueStr = $dataStr;
        }
        return $valueStr;
    }

    /**
     * get custom field's html string
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   CController         $controller             been filtered custom attributes
     * @param   object              $fieldInfo              custom field's information
     * @param   boolean             $editableFlag           is custom field's editable
     * @param   string              $errorClass             custom filed's error class
     * @param   string              $requiredClass          custom filed's required class
     * @param   string              $value                  custom filed's value
     * @return  string                                      custom filed's html element string
     *
     */
    public static function getFieldInputStr($controller, $fieldInfo, $editableFlag, $errorClass, $requiredClass, $value=null)
    {
        $fieldType = $fieldInfo->field_type;
        $classStr = $requiredClass;
        if('' != $errorClass)
        {
            $classStr = $errorClass;
        }
        if(FieldConfig::FIELD_TYPE_MULTISELECT != $fieldType)
        {
            $classStr .= ' info_input';
        }
        $resultStr = '';
        $fieldName = $fieldInfo->field_name;

        if(false == $editableFlag)
        {
            $resultStr = '';
            if($value !== null)
            {
                $resultStr = CHtml::textField('Custom[' . $fieldName . ']', $value, array(
                            'readonly' => 'readonly', 'class' => 'info_input readonly_field', 'title' => $value));
            }
            else
            {
                $resultStr = CHtml::textField('Custom[' . $fieldName . ']', '', array(
                            'readonly' => 'readonly', 'class' => 'info_input readonly_field', 'title' => ''));
            }
            return $resultStr;
        }

        $fieldValueStr = $fieldInfo->field_value;
        if($value === null)
        {
            $fieldSettedValue = $fieldInfo->default_value;
        }
        else
        {
            $fieldSettedValue = $value;
        }

        if(FieldConfig::FIELD_TYPE_SINGLESELECT == $fieldType)
        {
            $selectValueStr = '';
            $fieldValueArr = self::getSelectOption($fieldValueStr);
            $optionData = array();
            $optionData[''] = '';
            foreach($fieldValueArr as $fieldValue)
            {
                $optionData[$fieldValue] = $fieldValue;
            }

            $resultStr = CHtml::dropDownList('Custom[' .
                            $fieldName . ']',
                            $fieldSettedValue, $optionData,
                            array('id' => 'Custom_' . $fieldName,
                                'style' => 'width:190px;',
                                'class' => $classStr));
        }
        elseif(FieldConfig::FIELD_TYPE_MULTISELECT == $fieldType)
        {
            $fieldValueArr = self::getSelectOption($fieldValueStr);
            if(is_string($fieldSettedValue))
            {
                $selectValueArr = CommonService::splitStringToArray(',', $fieldSettedValue);
            }
            elseif(is_array($fieldSettedValue))
            {
                $selectValueArr = $fieldSettedValue;
            }
            else
            {
                $selectValueArr = array();
            }

            $optionArr = array();
            foreach($fieldValueArr as $value)
            {
                $optionArr[$value] = $value;
            }
            $resultStr = $controller->widget('application.extensions.multiSelect.MultiSelectWidget', array(
                        'name' => 'Custom[' . $fieldName . '][]',
                        'value' => $selectValueArr,
                        'selectOptionData' => $optionArr,
                        'htmlOptions' => array(
                            'style' => 'width:170px;',
                            'class' => $classStr
                            )), true);
        }
        elseif(FieldConfig::FIELD_TYPE_DATE == $fieldType)
        {
            $resultStr = $controller->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'name' => 'Custom[' . $fieldName . ']',
                        'value' => $fieldSettedValue,
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ),
                        'htmlOptions' => array(
                            //'readonly' => 'readonly',
                            'class' => $classStr
                        ),
                        'language' => Yii::app()->language), true);
        }
        elseif(FieldConfig::FIELD_TYPE_SINGLEUSER == $fieldType || FieldConfig::FIELD_TYPE_MULTIUSER == $fieldType)
        {
            $multipleFlag = 'false';
            if(FieldConfig::FIELD_TYPE_MULTIUSER == $fieldType)
            {
                $multipleFlag = 'true';
            }
            $resultStr = $controller->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                        'name' => 'Custom[' . $fieldName . ']',
                        'value' => $fieldSettedValue,
                        'htmlOptions' => array(
                            'class' => $classStr
                        ),
                        'urlOrData' => TestUser::getSearchUserUrl(),
                        'config' => '{
                    multiple: ' . $multipleFlag . '
                }'
                            ), true);
        }
        elseif(FieldConfig::FIELD_TYPE_ACINPUT == $fieldType ||
                FieldConfig::FIELD_TYPE_ACINPUT_MATCH == $fieldType)
        {
            $resultStr = $controller->widget('application.extensions.autocomplete.AutoCompleteWidget',
                            array(
                                'name' => 'Custom[' . $fieldName . ']',
                                'value' => $fieldSettedValue,
                                'urlOrData' => "'" . Yii::app()->createUrl('search/getAcValue', array('data' => $fieldInfo->field_value)) . "'",
                                'config' => '{cookieId:"custom-ac-' . $fieldName . '-cookie"}',
                                'htmlOptions' => array(
                                    'style' => 'width:190px;',
                                    'class' => $classStr)
                            ), true);
        }
        elseif(FieldConfig::FIELD_TYPE_TEXTAREA == $fieldType)
        {
            $resultStr = '<textarea class="' . $classStr . '" id="Custom_' . $fieldName .
                    '" name="Custom[' . $fieldName . ']"  style="width:190px;" rows="6">' . CHtml::encode($fieldSettedValue) . '</textarea>';
        }
        else
        {
            $resultStr = '<input class="' . $classStr . '" type="text" value="' .
                    CHtml::encode($fieldSettedValue) . '" id="Custom_' . $fieldName . '" name="Custom[' . $fieldName . ']">';
        }
        return $resultStr;
    }

    public static function disableFieldConfig($fieldId, $isDropped)
    {
        $resultInfo = array();
        $fieldInfo = self::loadModel($fieldId);
        if(!ProductService::isProductEditable($fieldInfo['product_id']))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }
        $fieldInfo->is_dropped = $isDropped;
        if(!$fieldInfo->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $fieldInfo->getErrors();
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        }
        return $resultInfo;
    }

    /**
     * edit custom field config
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $fieldConfigInfo             custom config
     * @return  array                                       edit custom field config result
     *
     */
    public static function editFieldConfig($fieldConfigInfo)
    {
        $resultInfo = array();
        if(!ProductService::isProductEditable($fieldConfigInfo['product_id']))
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = Yii::t('Common', 'Required URL not found or permission denied.');
            return $resultInfo;
        }
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try
        {
            $resultInfo = self::editSingleFieldConfig($fieldConfigInfo);
            if(CommonService::$ApiResult['SUCCESS'] == $resultInfo['status'])
            {
                //sync to result
                if((isset($fieldConfigInfo['edit_in_result'])) &&
                        (CommonService::$TrueFalseStatus['TRUE'] == $fieldConfigInfo['edit_in_result']))
                {
                    $originFieldInfo = FieldConfig::model()->findByPk($resultInfo['detail']['id']);
                    $resultFieldConfigInfo = $fieldConfigInfo;
                    $resultFieldConfigInfo['type'] = 'result';
                    $resultFieldConfigInfo['belong_group'] = $originFieldInfo['result_group'];

                    $resultFieldInfo = self::getProductFieldInfoByFieldName('result', $originFieldInfo->product_id, $originFieldInfo['field_name']);
                    if($resultFieldInfo === null)
                    {
                        $resultFieldConfigInfo['id'] = null;
                    }
                    else
                    {
                        $resultFieldConfigInfo['id'] = $resultFieldInfo['id'];
                        $resultFieldConfigInfo['product_id'] = $originFieldInfo->product_id;
                        $resultFieldConfigInfo['field_name'] = $originFieldInfo->field_name;
                    }
                    $resultTmp = self::editSingleFieldConfig($resultFieldConfigInfo);
                    if(CommonService::$ApiResult['FAIL'] == $resultTmp['status'])
                    {
                        $resultInfo = $resultTmp;
                    }
                    else
                    {
                        $transaction->commit();
                    }
                }
                else
                {
                    $transaction->commit();
                }
            }
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = $e->getMessage();
        }
        return $resultInfo;
    }

    /**
     * get field operation
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $fieldConfigInfo         field config information
     * @return  array                                   edit single field config result
     */
    public static function editSingleFieldConfig($fieldConfigInfo)
    {
        $resultInfo = array();
        $actionType = BugfreeModel::ACTION_OPEN;
        $oldRecordAttributs = array();
        if(!empty($fieldConfigInfo['id']))
        {
            $fieldConfig = self::loadModel($fieldConfigInfo['id']);
            $oldRecordAttributs = $fieldConfig->attributes;
            $actionType = BugfreeModel::ACTION_EDIT;
        }
        else
        {
            $fieldConfig = new FieldConfig();
        }
        $fieldConfig->attributes = $fieldConfigInfo;
        $action = 'update';
        if($fieldConfig->isNewRecord)
        {
            $action = 'create';
        }
        $fieldConfig->scenario = $fieldConfigInfo['type'];
        if(!$fieldConfig->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $fieldConfig->getErrors();
        }
        else
        {
            $editAddOnTableResult = self::editAddOnTable($action, $fieldConfig->attributes);
            if('' == $editAddOnTableResult)
            {
                $newRecord = self::loadModel($fieldConfig->id);
                $addActionResult = AdminActionService::addActionNotes('field_config', $actionType, $newRecord, $oldRecordAttributs);
                $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
                $resultInfo['detail'] = array('id' => $fieldConfig->id);
            }
            else
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = array('field_name' => Yii::t('FieldConfig', $editAddOnTableResult));
            }
        }
        return $resultInfo;
    }

    /**
     * check if value existed in database
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   int            $productId               product id
     * @param   string         $type                    bug,case or result
     * @param   string         $fieldName               field name
     * @param   string         $fieldValue              field value
     * @param   int            $basicId                 basic id
     * @return  boolean                                 is value existed in db
     */
    public static function isCustomValueExisted($productId, $type, $fieldName, $fieldValue, $basicId)
    {
        $tableName = 'etton' . $type . '_' . $productId;
        $searchResult = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('{{' . $tableName . '}}')
                        ->where($fieldName . '= :fieldValue and ' . $type . '_id <> :basicId',
                                array(':fieldValue' => $fieldValue,
                                    ':basicId' => $basicId))
                        ->queryAll();
        if(!empty($searchResult))
        {
            return true;
        }
        return false;
    }

    public static function getCustomFieldLabel($type, $productId)
    {
        $labelArr = array();
        $customFieldInfos = ProductService::getProductAllFieldInfo($type, $productId);
        foreach($customFieldInfos as $customFieldInfo)
        {
            $labelArr[$customFieldInfo['field_name']] = $customFieldInfo['field_label'];
        }
        return $labelArr;
    }

    /**
     * get custom field value
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   int            $productId               product id
     * @param   string         $type                    bug,case or result
     * @param   int            $id                      basic id
     * @return  array                                   custom field value
     */
    public static function getCustomFieldValue($productId, $type, $id)
    {
        //only get the active custom field
        $legalCustomFieldInfos = ProductService::getProductAllFieldInfo($type, $productId);
        $legalNameArr = array('id', $type . '_id');
        foreach($legalCustomFieldInfos as $fieldInfo)
        {
            $legalNameArr[] = $fieldInfo['field_name'];
        }
        $tableName = 'etton' . $type . '_' . $productId;
        $searchResult = Yii::app()->db->createCommand()
                        ->select(join(',', $legalNameArr))
                        ->from('{{' . $tableName . '}}')
                        ->where($type . '_id = :id',
                                array(':id' => $id))
                        ->queryRow();
        return $searchResult;
    }

    public static function getCustomFieldInfoByName($productId, $type, $name)
    {
        $tableName = 'etton' . $type . '_' . $productId;
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $tableName . '}}')
                        ->where('field_name = :name',
                                array(':name' => $name))
                        ->queryRow();
        return $searchResult;
    }

    /**
     * get new bug editable field
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   int            $productId               product id
     * @param   string         $type                    bug,case or result
     * @param   int            $basicInfoId             basic info id
     * @return  array                                   custom table info
     */
    public static function getCustomDbInfo($productId, $type, $basicInfoId)
    {
        $tableName = 'etton' . $type . '_' . $productId;
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{' . $tableName . '}}')
                        ->where($type . '_id = :infoId', array(':infoId' => $basicInfoId))
                        ->queryRow();
        return $searchResult;
    }

    /**
     * get new bug editable field
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   int            $productId               product id
     * @return  array                                   editable field
     */
    public static function getNewBugEditableField($productId)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('field_name')
                        ->from('{{field_config}}')
                        ->where("is_dropped = :isDropped and product_id = :productId and type = 'bug' and editable_action like '%" .
                                Info::ACTION_OPEN . "%'",
                                array(':isDropped' => CommonService::$TrueFalseStatus['FALSE'],
                                    ':productId' => $productId))
                        ->queryAll();
        $fieldArr = array();
        foreach($searchResult as $fieldConfig)
        {
            $fieldArr[] = $fieldConfig['field_name'];
        }
        return $fieldArr;
    }

    public static function getDateField($infoType, $productId)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('field_name')
                        ->from('{{field_config}}')
                        ->where("is_dropped = :isDropped and product_id = :productId and type = :type and field_type = :fieldType",
                                array(':isDropped' => CommonService::$TrueFalseStatus['FALSE'],
                                    ':productId' => $productId, ':type' => $infoType,
                                    ':fieldType' => FieldConfig::FIELD_TYPE_DATE))
                        ->queryAll();
        $fieldArr = array();
        foreach($searchResult as $fieldConfig)
        {
            $fieldArr[] = $fieldConfig['field_name'];
        }
        return $fieldArr;
    }

    /**
     * validate custom field input
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   string         $type                        bug,case or result
     * @param   int            $productId                   product id
     * @param   int            $basicInfoId                 basic info id
     * @param   array          $passedCustomValue           been validated custom information
     * @param   string         $action                      edit action
     * @return  array                                       validate result array
     *
     */
    public static function validateCustomFieldData($type, $productId, $basicInfoId, $passedCustomValue, $action)
    {
        $resultInfo = array();
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        $customFields = ProductService::getProductAllFieldInfo($type, $productId);
        $legalFiledNameArr = array('id', $type . '_id');

        $dbCustomValue = array();
        $oldDbCustomValue = array();
        if(!empty($basicInfoId))
        {
            $dbCustomValue = self::getCustomDbInfo($productId, $type, $basicInfoId);
            $oldDbCustomValue = $dbCustomValue;
        }
        foreach($passedCustomValue as $cuskey => $cusValue)
        {
            $dbCustomValue[$cuskey] = $cusValue;
        }
        $customValue = $dbCustomValue;
        foreach($customFields as $fieldInfo)
        {
            $fieldInfo = (object) $fieldInfo;
            $legalFiledNameArr[] = $fieldInfo->field_name;
            $editableActionArr = CommonService::splitStringToArray(',', $fieldInfo->editable_action);
            if(('bug' == $type) && !self::getEditableFlag($action, $fieldInfo))
            {
                continue;
            }
            $key = $fieldInfo->field_name;
            if(CommonService::$TrueFalseStatus['TRUE'] == $fieldInfo->is_required)
            {
                if(!isset($customValue[$key]) || ('' == $customValue[$key]))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_EMPTY);
                    continue;
                }
            }
            $validateRule = $fieldInfo->validate_rule;
            $fieldSetted = false;
            if(isset($customValue[$key]) && ('' != trim($customValue[$key])))
            {
                $fieldSetted = true;
            }
            if($fieldSetted && (FieldConfig::VALIDATION_RULE_MATCH == $validateRule))
            {
                $matchStr = $fieldInfo->match_expression;
                $matchResult = preg_match($matchStr, $customValue[$key]);
                if($matchResult == 0)
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'][$key] = $fieldInfo->field_label . ' [' . $fieldInfo->match_expression . ']' . Yii::t('FieldConfig', self::ERROR_MATCH);
                }
            }
            elseif($fieldSetted && (FieldConfig::VALIDATION_RULE_UNIQUE == $validateRule))
            {
                if(self::isCustomValueExisted($productId, $type, $key, $customValue[$key], $basicInfoId))
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_UNIQUE);
                }
            }
            if(empty($resultInfo['detail'][$key]) && $fieldSetted)
            {
                $fieldType = $fieldInfo->field_type;
                if(FieldConfig::FIELD_TYPE_SINGLEUSER == $fieldType)
                {
                    $errMsg = self::checkUser($fieldInfo, $key, $customValue[$key]);
                    if('' != $errMsg)
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'][$key] = $errMsg;
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_MULTIUSER == $fieldType)
                {
                    $userRealNameArr = CommonService::splitStringToArray(',', $customValue[$key]);
                    foreach($userRealNameArr as $realname)
                    {
                        $errMsg = self::checkUser($fieldInfo, $key, $realname);
                        if('' != $errMsg)
                        {
                            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                            $resultInfo['detail'][$key] = $errMsg;
                            break;
                        }
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_ACINPUT_MATCH == $fieldType)
                {
                    if(!isset($oldDbCustomValue[$key]) || ($customValue[$key] != $oldDbCustomValue[$key]))
                    {
                        $acValueStr = FieldConfigService::getAutoCompleteValueStr($fieldInfo->field_value, $customValue[$key], 1);
                        $acValueArr = CommonService::splitStringToArray(',', $acValueStr);
                        if(!in_array($customValue[$key], $acValueArr))
                        {
                            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                            $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_INPUT_INVALID);
                            continue;
                        }
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_DATE == $fieldType)
                {
                    if(!preg_match('/^(19|20)\d{2}-(0\d|1[012])-(0\d|[12]\d|3[01])$/', $customValue[$key]))
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('Common', 'Wrong date format. should like 2009-01-08.');
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_SINGLESELECT == $fieldType)
                {
                    if(154 == $productId && ('XProject' == $key))
                    {

                    }
                    else
                    {
                        if(!in_array($customValue[$key], FieldConfigService::getSelectOption($fieldInfo->field_value)))
                        {
                            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                            $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_INPUT_INVALID);
                        }
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_MULTISELECT == $fieldType)
                {
                    $inputedValueArr = CommonService::splitStringToArray(',', $customValue[$key]);
                    $legalValueArr = FieldConfigService::getSelectOption($fieldInfo->field_value);
                    foreach($inputedValueArr as $valueTmp)
                    {
                        if(!in_array($valueTmp, $legalValueArr))
                        {
                            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                            $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_INPUT_INVALID);
                            break;
                        }
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_TEXT == $fieldType)
                {
                    if(strlen($customValue[$key]) > 255)
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_255_LONG);
                    }
                }
                elseif(FieldConfig::FIELD_TYPE_TEXTAREA == $fieldType)
                {
                    if(strlen($customValue[$key]) > 65535)
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'][$key] = $fieldInfo->field_label . ' ' . Yii::t('FieldConfig', self::ERROR_65535_LONG);
                    }
                }
            }
        }
        $postedFieldNameArr = array_keys($passedCustomValue);
        $notExistedFieldNameArr = array();
        foreach($postedFieldNameArr as $fieldName)
        {
            if(!in_array($fieldName, $legalFiledNameArr))
            {
                $notExistedFieldNameArr[] = $fieldName;
            }
        }
        if(!empty($notExistedFieldNameArr))
        {
            $keyErrorMsg = 'custom field [' . join(',', $notExistedFieldNameArr) . '] is not valid field name';
            if(CommonService::$ApiResult['SUCCESS'] == $resultInfo['status'])
            {
                unset($resultInfo);
                $resultInfo = array();
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail']['custom_field'] = $keyErrorMsg;
            }
            else
            {
                $resultInfo['detail']['custom_field'] = $keyErrorMsg;
            }
        }
        return $resultInfo;
    }

    private static function checkUser($fieldInfo, $key, $realname)
    {
        $errorMsg = '';
        $userInfo = TestUserService::getUserInfoByRealname($realname);
        if($userInfo === null)
        {
            $errorMsg = $fieldInfo->field_label . ' ' . '[' . $realname . ']' . Yii::t('TestUser', self::ERROR_USER_NOT_FOUND);
        }
        return $errorMsg;
    }

    /**
     * create product's add on table
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   int            $productId               product id
     * @param   array          $fieldConfigInfo         field config information
     * @return
     */
    public static function createAddOnTable($productId)
    {
        $infoTypeArr = array(Info::TYPE_BUG, Info::TYPE_CASE, Info::TYPE_RESULT);
        foreach($infoTypeArr as $type)
        {
            $fieldConfigArr = array();
            $fieldConfigArr['id'] = 'pk';
            $fieldConfigArr[$type . '_id'] = 'integer NOT NULL';
            $createResult = Yii::app()->db->createCommand()
                            ->createTable('{{etton' . $type . '_' . $productId . '}}',
                                    $fieldConfigArr, 'ENGINE=InnoDB DEFAULT CHARSET=utf8');
            Yii::app()->db->createCommand()
                    ->createIndex('{{_idx_' . $type . '_id}}',
                            '{{etton' . $type . '_' . $productId . '}}', $type . '_id');
        }
    }

    /**
     * edit add on table
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   stirng         $action                  edit action
     * @param   array          $fieldConfigInfo         field config information
     * @return  string                                  edit result
     */
    public static function editAddOnTable($action, $fieldConfigInfo)
    {
        $resultInfo = array();
        $type = $fieldConfigInfo['type'];
        $productId = $fieldConfigInfo['product_id'];
        $tableName = 'etton' . $type . '_' . $productId;
        if('create' == $action)
        {
            $checkFieldNameResult = self::checkFieldNameBeforeAddColumn($type, $tableName, $fieldConfigInfo);
            if('' == $checkFieldNameResult)
            {
                self::addColumn($tableName, $fieldConfigInfo);
            }
            else
            {
                return $checkFieldNameResult;
            }
        }
        else
        {
            self::alterColumn($tableName, $fieldConfigInfo);
        }
        return '';
    }

    public static function alterColumn($tableName, $fieldConfigInfo)
    {
        $result = Yii::app()->db->createCommand()
                        ->alterColumn('{{' . $tableName . '}}',
                                $fieldConfigInfo['field_name'],
                                self::getFieldDbType($fieldConfigInfo['field_type']));
    }

    public static function addColumn($tableName, $fieldConfigInfo)
    {
        $result = Yii::app()->db->createCommand()
                        ->addColumn('{{' . $tableName . '}}',
                                $fieldConfigInfo['field_name'],
                                self::getFieldDbType($fieldConfigInfo['field_type']));
    }

    /**
     * check if field is legal
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   stirng         $type                    bug,case or result
     * @param   string         $tableName               table name
     * @param   array          $fieldConfigInfo         field config information
     * @return  string                                  check result
     */
    private static function checkFieldNameBeforeAddColumn($type, $tableName, $fieldConfigInfo)
    {
        if(in_array($fieldConfigInfo['field_name'], self::$mySqlKeyWords))
        {
            return self::ERROR_FIELD_NAME_KEYWORD;
        }
        elseif(($tableName != '') && (self::dbCheckFieldExist($tableName, $fieldConfigInfo['field_name'])))
        {
            return self::ERROR_FIELD_NAME_EXIST;
        }
        elseif(self::dbCheckFieldExist($type . '_info', $fieldConfigInfo['field_name']))
        {
            return self::ERROR_FIELD_NAME_BASIC_EXIST;
        }
        else
        {
            return '';
        }
    }

    public static function dbCheckTableExists($tableName)
    {
        $tableInfos = Yii::app()->db->createCommand('show tables')->queryAll();
        foreach($tableInfos as $tableInfo)
        {
            foreach($tableInfo as $key => $value)
            {
                if(CommonService::endsWith($value, $tableName))
                {
                    return true;
                }
            }
        }
        return false;
    }

    private static function dbCheckFieldExist($tableName, $fieldName)
    {
        $fieldInfo = Yii::app()->db->createCommand('describe {{' . $tableName . '}} ' . $fieldName)->queryAll();
        if(empty($fieldInfo))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * get product field information by field name
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   stirng         $type                    bug,case or result
     * @param   int            $productId               product id
     * @param   string         $fieldName               field name
     * @return  array                                   field config information
     */
    public static function getProductFieldInfoByFieldName($type, $productId, $fieldName)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{field_config}}')
                        ->where('type = :type and product_id = :productId and is_dropped = :isDropped and field_name = :fieldName',
                                array(':type' => $type,
                                    ':productId' => $productId,
                                    ':fieldName' => $fieldName,
                                    ':isDropped' => CommonService::$TrueFalseStatus['FALSE']))
                        ->order('display_order,id')
                        ->queryRow();
        return $searchResult;
    }

    /**
     * check if value existed in database
     *
     * @author                                          youzhao.zxw<swustnjtu@gmail.com>
     * @param   string         $fieldTypeInfo           field type information
     * @return  string                                  field db type
     */
    private static function getFieldDbType($fieldTypeInfo)
    {
        if(FieldConfig::FIELD_TYPE_TEXT == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_SINGLESELECT == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_SINGLEUSER == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_ACINPUT == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_ACINPUT_MATCH == $fieldTypeInfo)
        {
            return 'string';
        }
        else if(FieldConfig::FIELD_TYPE_TEXTAREA == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_MULTISELECT == $fieldTypeInfo ||
                FieldConfig::FIELD_TYPE_MULTIUSER == $fieldTypeInfo)
        {
            return 'text';
        }
        else if(FieldConfig::FIELD_TYPE_DATE == $fieldTypeInfo)
        {
            return 'date';
        }
    }

    public static function handleFieldValueStr($fieldValueStr)
    {
        $resultStr = CommonService::sysSubStr($fieldValueStr, 20, true);
        return '<span title="' . $fieldValueStr . '">' . $resultStr . '</span>';
    }

    public static function getFieldTypeOperatorMapping()
    {
        return array(
            FieldConfig::FIELD_TYPE_TEXT => Info::$InputType['string'],
            FieldConfig::FIELD_TYPE_SINGLESELECT => Info::$InputType['option'],
            FieldConfig::FIELD_TYPE_SINGLEUSER => Info::$InputType['people'],
            FieldConfig::FIELD_TYPE_TEXTAREA => Info::$InputType['string'],
            FieldConfig::FIELD_TYPE_MULTISELECT => Info::$InputType['multioption'],
            FieldConfig::FIELD_TYPE_MULTIUSER => Info::$InputType['multipeople'],
            FieldConfig::FIELD_TYPE_DATE => Info::$InputType['date'],
            FieldConfig::FIELD_TYPE_ACINPUT => Info::$InputType['string'],
            FieldConfig::FIELD_TYPE_ACINPUT_MATCH => Info::$InputType['string']
        );
    }

    public static function loadModel($id)
    {
        $model = FieldConfig::model()->findByPk((int) $id);
        if($model === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        else
        {
            $model->product_name = $model->product->name;
            $model->belong_group_name = CommonService::getMessageName('FieldConfig', $model->belong_group);
            $model->editable_action_name = CommonService::splitStringToArray(',', $model->editable_action);
        }
        return $model;
    }

}

?>
