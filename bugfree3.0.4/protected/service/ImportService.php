<?php

/**
 * This is import class
 *
 * @package bugfree.protected.service
 */
class ImportService
{

    /**
     * label convert to field
     *
     * @param string $label
     * @param string $productId
     * @param string $infoType
     * @param array
     */
    private function fieldConv($label, $productId, $infoType)
    {
        $field = false;
        $isBasic = false;
        $clazz = ucfirst($infoType) . 'Info';
//      $basicFields = $clazz::model()->attributeLabels();
        $targetModel = new $clazz();
        $basicFields = $targetModel->attributeLabels();

        $customFields = FieldConfigService::getCustomFieldLabel($infoType, $productId);
        $notAllowFields = array('created_by', 'updated_by', 'created_by_name',
            'updated_by_name', 'created_at', 'updated_at', 'modified_by', 'related_result');

        foreach($notAllowFields as $field)
        {
            if(isset($basicFields[$field]))
            {
                unset($basicFields[$field]);
            }
        }

        $field = array_search($label, $basicFields);
        if($field)
        {
            // hard code for productmodule_id&module_name, assign_to&assign_to_name
            if('assign_to' == $field)
            {
                $field = 'assign_to_name';
            }
            if('module_name' == $field)
            {
                $field = 'productmodule_id';
            }
            $isBasic = true;
        }
        else
        {
            $field = array_search($label, $customFields);
        }

        return array($field, $isBasic);
    }

    /**
     * basic info convert
     *
     * @todo convert $action for bug import
     *
     * @param array $basicInfo
     * @param string $infoType
     * @return string
     */
    private function basicInfoConv($basicInfo, $infoType)
    {
        // hard code for productmodule_id
        if(isset($basicInfo['productmodule_id']))
        {
            $moduleSplitterPos = strpos($basicInfo['productmodule_id'], ProductModule::MODULE_SPLITTER);
            if(false !== $moduleSplitterPos)
            {
                $moduleName = substr($basicInfo['productmodule_id'], $moduleSplitterPos + 1);
                $moduleInfo = ProductModule::model()->findByAttributes(array('product_id' => $basicInfo['product_id'], 'full_path_name' => $moduleName));
                if(!empty($moduleInfo))
                {
                    $basicInfo['productmodule_id'] = $moduleInfo->id;
                }
            }
            else
            {
                $basicInfo['productmodule_id'] = 0;
            }
        }
        // hard code for id
        if(isset($basicInfo['id']) && '' == $basicInfo['id'])
        {
            unset($basicInfo['id']);
        }
        // hard code for delete_flag
        if(isset($basicInfo['delete_flag']))
        {
            $basicInfo['delete_flag'] = CommonService::getTrueFalseValue($basicInfo['delete_flag']);
        }

        if(isset($basicInfo['priority']))
        {
            if(Info::TYPE_CASE == $infoType)
            {
                $basicInfo['priority'] = ProductService::getCasePriorityValueByName($basicInfo['product_id'], $basicInfo['priority']);
            }
            else if(Info::TYPE_BUG == $infoType)
            {
                $basicInfo['priority'] = ProductService::getBugPriorityValueByName($basicInfo['product_id'], $basicInfo['priority']);
            }          
        }

        if(isset($basicInfo['severity']) && (Info::TYPE_BUG == $infoType))
        {
            $basicInfo['severity'] = ProductService::getBugSeverityValueByName($basicInfo['product_id'], $basicInfo['severity']);
        }

        // @TODO convert for bug import
        $bugUserKeyArr = array('resolved_by', 'closed_by');
        foreach($bugUserKeyArr as $bugUserKey)
        {
            if(isset($basicInfo[$bugUserKey]))
            {
                $resolvedByInfo = TestUserService::getUserInfoByRealname($basicInfo[$bugUserKey]);
                if(!empty($resolvedByInfo))
                {
                    $basicInfo[$bugUserKey] = $resolvedByInfo['id'];
                }
                else
                {
                    unset($basicInfo[$bugUserKey]);
                }
            }
        }
        $bugDateKeyArr = array('resolved_at', 'closed_at');
        foreach($bugDateKeyArr as $bugDateKey)
        {
            if(empty($basicInfo[$bugDateKey]))
            {
                unset($basicInfo[$bugDateKey]);
            }
        }

        return $basicInfo;
    }

    /**
     * get result msg
     *
     * @param array $results
     * @param string $infoType
     * @return string
     */
    private function getResultMsg($results)
    {
        $msgGroup = 'Common';
        $msg = '';
        $rowCount = $failedCount = 0;

        // sheet index
        foreach($results as $sidx => $sheetResults)
        {
            foreach($sheetResults as $ridx => $result)
            {
                $rowCount++;
                if(CommonService::$ApiResult['FAIL'] == $result['status'])
                {
                    $failedCount++;
                    $msg .= Yii::t($msgGroup, 'Sheet {sidx} row {ridx} import failed',
                                    array('{sidx}' => $sidx + 1, '{ridx}' => $ridx + 2));
                    $msg .= '(';
                    $infos = array();
                    foreach($result['detail'] as $info)
                    {
                        if(is_array($info))
                        {
                            $infos[] = join(' ', $info);
                        }
                        else
                        {
                            $infos[] = $info;
                        }
                    }
                    $msg .= join(', ', $infos) . ")\n";
                }
            }
        }

        if(empty($result))
        {
            $msg = Yii::t($msgGroup, 'Parse sheet file error');
        }
        else
        {
            $msg = Yii::t($msgGroup,
                            'Import finished! Total: {param0}, success: {param1}, fail: {param2}',
                            array('{param0}' => $rowCount, '{param1}' => $rowCount - $failedCount, '{param2}' => $failedCount))
                    . "\n\n" . $msg;
        }

        return $msg;
    }

    /**
     * import sheet data to bugfree
     *
     * @param mixed $sheet file or string
     * @param string $infoType
     */
    public function import($sheet, $productId, $infoType)
    {
        $sheetConv = new SheetConv();
        $data = $sheetConv->xml2array($sheet);
        $results = array();

        // sheet index
        foreach($data as $sidx => $items)
        {
            $sheetResult = array();
            // item index
            foreach($items as $iidx => $item)
            {
                // hard code for product_id
                $basicInfo = array('product_id' => $productId);
                $customInfo = array();
                $isEmpty = true;
                foreach($item as $key => $val)
                {
                    unset($item[$key]);
                    list($field, $isBasic) = $this->fieldConv($key, $productId, $infoType);
                    if($field)
                    {
                        if($isBasic)
                        {
                            $basicInfo[$field] = $val;
                        }
                        else
                        {
                            $customInfo[$field] = $val;
                        }
                    }
                    if('' != $val)
                    {
                        $isEmpty = false;
                    }
                }
                if(!$isEmpty)
                {
                    $action = Info::ACTION_IMPORT;
                    if(isset($basicInfo['id']) && ('' != trim($basicInfo['id'])))
                    {
                        if(Info::TYPE_BUG == $infoType)//bug not allow update
                        {
                            $sheetResult[] = array('status' => CommonService::$ApiResult['FAIL'],
                                'detail' => array(Yii::t('Common', 'can not update bug by import')));
                            continue;
                        }
                        else
                        {
                            $existedCaseInfo = CaseInfo::model()->findByPk((int) $basicInfo['id']);
                            $resultInfo = array();
                            if(empty($existedCaseInfo))
                            {
                                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                                $resultInfo['detail']['id'] = '[id:' . $basicInfo['id'] . ']' . Yii::t('Common', 'Requested object does not exist');
                                $sheetResult[] = $resultInfo;
                                continue;
                            }
                            else
                            {
                                $basicInfo['action_note'] = ''; //import action_note only when doing new action
                                if($productId != $existedCaseInfo['product_id'])
                                {
                                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                                    $resultInfo['detail']['id'] = Yii::t('Common', 'product is different');
                                    $sheetResult[] = $resultInfo;
                                    continue;
                                }
                            }
                        }
                    }

                    $basicInfo = $this->basicInfoConv($basicInfo, $infoType);
                    $sheetResult[] = InfoService::saveInfo($infoType, $action, $basicInfo, $customInfo, array(), array());
                }
            }
            $results[$sidx] = $sheetResult;
        }

        return $this->getResultMsg($results);
    }

}

?>