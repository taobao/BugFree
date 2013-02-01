<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ExportService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ExportService
{

    /**
     * split string to array
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array       $exportList         export list
     * @param   array       $exportColumnArray  export column array
     * @param   array       $FieldsArray        field array
     * @return  string                          exported xml string
     */
    public static function exportXML($exportList, $exportColumnArray, $FieldsArray)
    {
        $rowCount = count($exportList) + 1;
        $columnCount = count($exportColumnArray);

        $Content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                 <?mso-application progid=\"Excel.Sheet\"?>
                 <Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
                 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
                 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
                 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
                 xmlns:html=\"http://www.w3.org/TR/REC-html40\">
                 <DocumentProperties xmlns=\"urn:schemas-microsoft-com:office:office\">
                  <Created>1996-12-17T01:32:42Z</Created>
                  <LastSaved>2009-11-21T14:55:15Z</LastSaved>
                  <Version>11.9999</Version>
                 </DocumentProperties>
                 <OfficeDocumentSettings xmlns=\"urn:schemas-microsoft-com:office:office\">
                  <RemovePersonalInformation/>
                 </OfficeDocumentSettings>
                 <ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\">
                  <WindowHeight>4530</WindowHeight>
                  <WindowWidth>8505</WindowWidth>
                  <WindowTopX>480</WindowTopX>
                  <WindowTopY>120</WindowTopY>
                  <AcceptLabelsInFormulas/>
                  <ProtectStructure>False</ProtectStructure>
                  <ProtectWindows>False</ProtectWindows>
                 </ExcelWorkbook>
                 <Styles>
                  <Style ss:ID=\"Default\" ss:Name=\"Normal\">
                   <Alignment ss:Vertical=\"Bottom\"/>
                   <Borders/>
                   <Font ss:FontName=\"\" x:CharSet=\"134\" ss:Size=\"12\"/>
                   <Interior/>
                   <NumberFormat/>
                   <Protection/>
                  </Style>
                  <Style ss:ID=\"s21\">
                   <Alignment ss:Vertical=\"Bottom\" ss:WrapText=\"1\"/>
                  </Style>
                 </Styles>
                 <Worksheet ss:Name=\"Sheet1\">
                  <Table ss:ExpandedColumnCount=\"" . $columnCount . "\" ss:ExpandedRowCount=\"" . $rowCount . "\" x:FullColumns=\"1\"
                   x:FullRows=\"1\" ss:DefaultColumnWidth=\"54\" ss:DefaultRowHeight=\"14.25\">";

        $TempStr = "\n<Row>";
        foreach($exportColumnArray as $ExportItem)
        {
            $TempStr .=" \n<Cell><Data ss:Type=\"String\">" . $FieldsArray[$ExportItem]['label'] . "</Data></Cell>\n";
        }
        $TempStr .= "</Row>\n";
        foreach($exportList as $CaseItem)
        {
            $TempStr .= "\n<Row>";
            foreach($exportColumnArray as $Column)
            {
                $TempStr .=" \n<Cell><Data ss:Type=\"String\"><![CDATA[" . $CaseItem[$Column] . "]]></Data></Cell> \n";
            }
            $TempStr .= "</Row>\n";
        }


        $Content .= $TempStr;
        $Content .= "</Table>
                  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">
                   <Selected/>
                   <Panes>
                    <Pane>
                     <Number>3</Number>
                     <ActiveRow>1</ActiveRow>
                     <ActiveCol>1</ActiveCol>
                    </Pane>
                   </Panes>
                   <ProtectObjects>False</ProtectObjects>
                   <ProtectScenarios>False</ProtectScenarios>
                  </WorksheetOptions>
                 </Worksheet>
		 </Workbook>";
        return $Content;
    }

    /**
     * check if search row field is legal
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array       $searchFieldConfig  searchable field config
     * @param   array       $searchRowArr       search row array
     * @return  string                          check result string
     */
    private static function checkSearchRowField($searchFieldConfig, $searchRowArr)
    {
        $legalFieldNameArr = array_keys($searchFieldConfig);
        foreach($searchRowArr as $rowInfo)
        {
            if(!in_array($rowInfo['field'], $legalFieldNameArr))
            {
                return 'field [' . $rowInfo['field'] . '] can not be used as search condition';
            }
        }
        return '';
    }

    /**
     * check if show field is legal
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array       $searchFieldConfig  field config
     * @param   array       $showFieldArr       show field array
     * @return  string                          check result string
     */
    private static function checkShowField($searchFieldConfig, $showFieldArr)
    {
        $legalFieldNameArr = array_keys($searchFieldConfig);
        $specialFieldArr = array('repeat_step', 'case_step', 'result_step', '*', 'count(*)');
        foreach($showFieldArr as $showField)
        {
            if(!in_array($showField, $specialFieldArr) && !in_array($showField, $legalFieldNameArr))
            {
                return 'field [' . $showField . '] can not be used as show field';
            }
        }
        return '';
    }

    /**
     * check if show field is legal
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string      $infoType           bug,case or result
     * @param   array       $searchRowArr       search condition
     * @param   int         $productId          product id
     * @param   array       $showFieldArr       show field array
     * @param   array       $orderArr           order array
     * @param   string      $filterSql          filter sql
     * @param   int         $pageSize           page size
     * @param   int         $currentPageSize    current page size
     * @return  array                           export date result
     */
    public static function getExportData($infoType, $searchRowArr, $productId =null, $showFieldArr=null, $orderArr = null, $filterSql = null, $pageSize = null, $currentPage=null)
    {
        $resultInfo = array();

        if(!empty($productId))
        {
            $accessIdArr = Yii::app()->user->getState('visit_product_id');
            if(!in_array($productId, $accessIdArr))
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = Yii::t('Product', 'No access right to this product');
                return $resultInfo;
            }
        }
        $searchFieldConfig = SearchService::getSearchableFields($infoType, $productId);

        $checkSearchRowResult = self::checkSearchRowField($searchFieldConfig, $searchRowArr);
        if('' != $checkSearchRowResult)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $checkSearchRowResult;
            return $resultInfo;
        }

        if(!empty($showFieldArr))
        {
            $showFieldCheckResult = self::checkShowField($searchFieldConfig, $showFieldArr);
            if('' != $showFieldCheckResult)
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = $showFieldCheckResult;
                return $resultInfo;
            }
        }

        $getSqlResult = SqlService::baseGetGroupQueryStr($searchFieldConfig, $infoType, $searchRowArr);
        if(CommonService::$ApiResult['FAIL'] == $getSqlResult['status'])
        {
            $resultInfo = $getSqlResult;
            return $resultInfo;
        }
        $whereStr = $getSqlResult['detail'];

        if(!empty($filterSql))
        {
            $whereStr .= ' and ' . $filterSql;
        }
        if(empty($showFieldArr))
        {
            $showFieldArr = array_keys($searchFieldConfig);
            $showFieldArr = array_diff($showFieldArr, array(Info::MARK));
        }

        $allRelatedFieldArr = array();
        foreach($searchRowArr as $searchRowTmp)
        {
            $allRelatedFieldArr[] = $searchRowTmp['field'];
        }
        $isAllBasicField = SearchService::isAllBasicField(SearchService::getBasicFieldArr($infoType),
                        array_merge($showFieldArr, $allRelatedFieldArr));

        $sql = SqlService::getRawDataSql($searchFieldConfig, $infoType, $productId, $showFieldArr, $whereStr, $isAllBasicField);
        $totalNum = SqlService::getTotalFoundNum($infoType, $productId, $whereStr, $isAllBasicField);

        if(empty($pageSize))
        {
            $pageSize = $totalNum;
        }
        if($pageSize > 5000)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = 'items can not exceed 5000';
            return $resultInfo;
        }
        if(!isset($currentPage))
        {
            $currentPage = 1;
        }
        else
        {
            if(0 == $currentPage)
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = 'page should start from 1';
                return $resultInfo;
            }
            $maxPage = ceil($totalNum / $pageSize);
            if($currentPage > $maxPage && (0 != $maxPage))
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = 'current page [' . $currentPage . '] is greater than the max page [' . $maxPage . ']';
                return $resultInfo;
            }
        }
        if(empty($orderArr))
        {
            $orderArr = array('{{bug_info_view}}.id' => true);
        }
        $dataProvider = new CSqlDataProvider($sql, array(
                    'totalItemCount' => $totalNum,
                    'sort' => array(
                        'defaultOrder' => $orderArr,
                        'attributes' => array_keys($searchFieldConfig)
                    ),
                    'pagination' => array(
                        'pageSize' => $pageSize,
                        'currentPage' => $currentPage - 1
                    )
                ));
        $rawData = $dataProvider->getData();

        $rawData = SqlService::handleRawData($rawData, $infoType, $searchFieldConfig, $showFieldArr, $productId);
        $rawData = self::getExportComment($infoType, $rawData, $productId);
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        $resultInfo['detail'] = $rawData;
        return $resultInfo;
    }

    private static function getExportComment($infoType, $dataArr, $productId)
    {
        $infoIdArr = array();
        $fieldLabelArr = FieldConfigService::getCustomFieldLabel($infoType, $productId);
        foreach($dataArr as $data)
        {
            $infoIdArr[] = $data['id'];
        }
        $historyChangeArr = array();
        if(!empty($infoIdArr))
        {
            $getActionNoteSql = 'select * from {{' . $infoType .
                    '_action}} where ' . $infoType . 'info_id in (' .
                    join(',', $infoIdArr) . ') order by ' .
                    $infoType . 'info_id desc, created_at desc';
            $actionNoteInfos = Yii::app()->db->createCommand($getActionNoteSql)->queryAll();
            $actionIdArr = array();
            foreach($actionNoteInfos as $noteInfo)
            {
                $actionIdArr[] = $noteInfo['id'];
            }
            if(!empty($actionIdArr))
            {
                $actionIdKey = $infoType . 'action_id';
                $historySql = 'select * from {{' . $infoType . '_history}} where ' .
                        $actionIdKey . ' in (' . join(',', $actionIdArr) .
                        ') order by ' . $actionIdKey . ',id asc';
                $historyInfos = Yii::app()->db->createCommand($historySql)->queryAll();
                $historyChangeArr = array();

                foreach($historyInfos as $historyInfo)
                {
                    if(empty($historyChangeArr[$historyInfo[$actionIdKey]]))
                    {
                        $historyChangeArr[$historyInfo[$actionIdKey]] = array();
                    }
                    $historyChangeArr[$historyInfo[$actionIdKey]][] = $historyInfo;
                }
            }
            $infoCommentArr = array();
            foreach($actionNoteInfos as $noteInfo)
            {
                if(!empty($historyChangeArr[$noteInfo['id']]))
                {
                    $singleChangeArr = $historyChangeArr[$noteInfo['id']];
                }
                else
                {
                    $singleChangeArr = array();
                }

                $historyStr = '';
                foreach($singleChangeArr as $historyInfo)
                {
                    $historyStr .= '<br/>' . ActionHistoryService::getSingleHistoryStr($fieldLabelArr, $infoType, $productId, $historyInfo);
                }

                $singleActionStr = ActionHistoryService::getActionCleanContent($noteInfo, $infoType, $noteInfo[$infoType . 'info_id']);
                if('' != $historyStr)
                {
                    $singleActionStr .= $historyStr;
                }
                if(!in_array($noteInfo['action_note'], array('', '<br />', '<br/>', '<br>')))
                {
                    $singleActionStr .= '<br/><br/>' . ActionHistoryService::handDuplicateIdStr($noteInfo['action_note']);
                }

                if(empty($infoCommentArr[$noteInfo[$infoType . 'info_id']]))
                {
                    $infoCommentArr[$noteInfo[$infoType . 'info_id']] = array();
                }

                $infoCommentArr[$noteInfo[$infoType . 'info_id']][] = $singleActionStr;
            }
        }
        $dataCount = count($dataArr);
        for($i = 0; $i < $dataCount; $i++)
        {
            $dataArr[$i]['action_note'] = join("<br/><br/><br/>", $infoCommentArr[$dataArr[$i]['id']]);
        }
        return $dataArr;
    }

    /**
     * check if show field is legal
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $queryId            query id
     * @param   array       $showFieldArr       show field array
     * @param   array       $orderArr           order array
     * @param   string      $filterSql          filter sql
     * @param   int         $pageSize           page size
     * @param   int         $currentPageSize    current page size
     * @return  array                           export date result
     */
    public static function getExportDataByQueryId($queryId, $showFieldArr=null, $orderArr = null, $filterSql = null, $pageSize = null, $currentPage=null)
    {
        $resultInfo = UserQueryService::getQueryConditionById($queryId);
        if(CommonService::$ApiResult['FAIL'] == $resultInfo['status'])
        {
            return $resultInfo;
        }
        $savedSearchCondition = $resultInfo['detail'];
        return self::getExportData($savedSearchCondition['query_type'],
                $savedSearchCondition['search_condition'],
                $savedSearchCondition['product_id'],
                $showFieldArr,
                $orderArr,
                $filterSql,
                $pageSize,
                $currentPage);
    }

}

?>
