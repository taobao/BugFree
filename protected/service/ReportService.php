<?php
/**
 * This is report service
 */
class ReportService
{
    public static $BASIC_REPORT_DATA = array(
        Info::TYPE_BUG => array(
            array(
                'title' => 'bug per module',
                'type'  => Report::TYPE_BAR,
                'group' => 'module_name',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per status',
                'type'  => Report::TYPE_PIE,
                'group' => 'bug_status',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per created_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'created_by_name',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per severity',
                'type'  => Report::TYPE_BAR,
                'group' => 'severity',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per priority',
                'type'  => Report::TYPE_BAR,
                'group' => 'priority',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per created_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'created_by_name',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug open per day',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug open per week',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(DATE_SUB(created_at, INTERVAL (if(DATE_FORMAT(created_at, "%w") = 0,7,DATE_FORMAT(created_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug open per month',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per resolved_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'resolved_by_name',
                'where' => 'resolved_by_name != ""',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per solution',
                'type'  => Report::TYPE_PIE,
                'group' => 'solution',
                'where' => 'solution != ""',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug resolve per day',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'solution != ""  AND resolved_at IS NOT NULL',
                'group' => 'DATE_FORMAT(resolved_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug resolve per week',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'solution != "" AND resolved_at IS NOT NULL',
                'group' => 'DATE_FORMAT(DATE_SUB(resolved_at, INTERVAL (if(DATE_FORMAT(resolved_at, "%w") = 0,7,DATE_FORMAT(resolved_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug resolve per month',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'solution != "" AND resolved_at IS NOT NULL',
                'group' => 'DATE_FORMAT(resolved_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per closed_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'closed_by_name',
                'where' => 'closed_by_name != ""',
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug close per day',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'closed_at != "Closed" AND closed_at IS NOT NULL',
                'group' => 'DATE_FORMAT(closed_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug close per week',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'bug_status = "Closed" AND closed_at IS NOT NULL',
                'group' => 'DATE_FORMAT(DATE_SUB(closed_at, INTERVAL (if(DATE_FORMAT(closed_at, "%w") = 0,7,DATE_FORMAT(closed_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug close per month',
                'type'  => Report::TYPE_COLUMN,
                'where' => 'bug_status = "Closed" AND closed_at IS NOT NULL',
                'group' => 'DATE_FORMAT(closed_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug actived per day',
                'type'  => Report::TYPE_COLUMN,
                'table' => '{{bugview}},{{bug_action}}',
                'where' => '{{bugview}}.id = {{bug_action}}.buginfo_id AND action_type = "activated"',
                'group' => 'DATE_FORMAT({{bug_action}}.created_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug actived per week',
                'type'  => Report::TYPE_COLUMN,
                'table' => '{{bugview}},{{bug_action}}',
                'where' => '{{bugview}}.id = {{bug_action}}.buginfo_id AND action_type = "activated"',
                'group' => 'DATE_FORMAT(DATE_SUB({{bug_action}}.created_at, INTERVAL (if(DATE_FORMAT({{bug_action}}.created_at, "%w") = 0,7,DATE_FORMAT({{bug_action}}.created_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug actived per month',
                'type'  => Report::TYPE_COLUMN,
                'table' => '{{bugview}},{{bug_action}}',
                'where' => '{{bugview}}.id = {{bug_action}}.buginfo_id AND action_type = "activated"',
                'group' => 'DATE_FORMAT({{bug_action}}.created_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_BUG,
            ),
            array(
                'title' => 'bug per assign_to',
                'type'  => Report::TYPE_BAR,
                'group' => 'assign_to_name',
                'where' => 'assign_to_name != "" AND bug_status != "Closed"',
                'infoType' => Info::TYPE_BUG,
            ),
        ),
        Info::TYPE_CASE => array(
            array(
                'title' => 'case per module',
                'type'  => Report::TYPE_BAR,
                'group' => 'module_name',
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case per status',
                'type'  => Report::TYPE_PIE,
                'group' => 'case_status',
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case per priority',
                'type'  => Report::TYPE_BAR,
                'group' => 'priority',
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case per created_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'created_by_name',
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case open per day',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case open per week',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(DATE_SUB(created_at, INTERVAL (if(DATE_FORMAT(created_at, "%w") = 0,7,DATE_FORMAT(created_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_CASE,
            ),
            array(
                'title' => 'case open per month',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_CASE,
            ),
        ),
        Info::TYPE_RESULT => array(
            array(
                'title' => 'result per module',
                'type'  => Report::TYPE_BAR,
                'group' => 'module_name',
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result per status',
                'type'  => Report::TYPE_PIE,
                'group' => 'result_status',
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result per value',
                'type'  => Report::TYPE_PIE,
                'group' => 'result_value',
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result per created_by',
                'type'  => Report::TYPE_BAR,
                'group' => 'created_by_name',
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result open per day',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => false,
                'limit' => 90,
                'showOther' => false,
                'showTable' => false,
                'reverse' => true,
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result open per week',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(DATE_SUB(created_at, INTERVAL (if(DATE_FORMAT(created_at, "%w") = 0,7,DATE_FORMAT(created_at, "%w")))-1 DAY), "%Y-%m-%d")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_RESULT,
            ),
            array(
                'title' => 'result open per month',
                'type'  => Report::TYPE_COLUMN,
                'group' => 'DATE_FORMAT(created_at, "%Y-%m")',
                'order' => Report::GROUP_LABEL,
                'asc'   => true,
                'limit' => 0,
                'showTable' => false,
                'infoType' => Info::TYPE_RESULT,
            ),
        ),
    );

    /**
     * get reports
     *
     * @param string $infoType
     * @param integer $productId
     * @param string $where
     * @param array $selected
     * @return array
     */
    public function getReports($infoType, $productId, $selected = array())
    {
        $reports = array();
        $productIds = Yii::app()->user->getState('visit_product_id');
        $condition = 'product_id IN (' . join(',', $productIds) . ')';
        if(isset($productId))
        {
            $condition .= ' AND product_id = ' . $productId;
        }

        $searchRowArr = Yii::app()->user->getState($productId . '_' . $infoType . '_search');
        if(null == $searchRowArr)
        {
            $searchRowArr = array();
        }
        $searchFieldConfig = SearchService::getSearchableFields($infoType, $productId);
        $result = SqlService::baseGetGroupQueryStr($searchFieldConfig, $infoType, $searchRowArr);

        if(CommonService::$ApiResult['SUCCESS'] == $result['status'])
        {
            $condition .= ' AND ' . $result['detail'];
        }

        $reportDatas = $this->getReportData($infoType, $productId, $condition, $selected);
        foreach($reportDatas as $key => $data)
        {
            $show = false;
            if(!empty($selected) && in_array($key, $selected))
            {
                $show = true;
            }
            $reports[] = new Report($infoType, $productId, $data['type'], $condition, $data, $show);
        }
        return $reports;
    }

    /**
     * get report data
     *
     * @param string $infoType
     * @param integer $productId
     * @param string $where
     * @return array
     */
    private function getReportData($infoType, $productId, $where, $selected)
    {
        $data = ReportService::$BASIC_REPORT_DATA[$infoType];
        $count = count($data);
        if(Info::TYPE_BUG == $infoType)
        {
            $show = false;
            if(in_array($count, $selected))
            {
                $show = true;
            }
            $data[] = $this->getBugLiveDaysData($productId, $where, $show);
            $show = false;
            if(in_array(++$count, $selected))
            {
                $show = true;
            }
            $data[] = $this->getBugHistorysData($productId, $where, $show);
        }
        $data = array_merge($data, $this->getCustomFieldData($infoType, $productId));
        return $data;
    }

    /**
     * get custom field data
     *
     * @internal
     * @param string $infoType
     * @param string $productId
     * @return array
     */
    private function getCustomFieldData($infoType, $productId)
    {
        $data = array();
        $fields = FieldConfig::model()->findAllByAttributes(array('product_id' => $productId, 'is_dropped' => false, 'type' => ucfirst($infoType)));
        foreach($fields as $field)
        {
            switch($field->field_type)
            {
                case FieldConfig::FIELD_TYPE_SINGLESELECT:
                case FieldConfig::FIELD_TYPE_SINGLEUSER: {
                    $data[] = array(
                        'title' => Yii::t('Report', '{type} per {group}', array('{type}' => ucfirst($infoType), '{group}' => $field->field_label)),
                        'type'  => Report::TYPE_BAR,
                        'group' => $field->field_name,
                        'infoType' => $infoType,
                    );
                    break;
                }
                case FieldConfig::FIELD_TYPE_DATE: {
                    $data[] = array(
                        'title' => Yii::t('Report', '{type} {group} per day', array('{type}' => ucfirst($infoType), '{group}' => $field->field_label)),
                        'type'  => Report::TYPE_COLUMN,
                        'where' => $field->field_name . ' IS NOT NULL',
                        'group' => 'DATE_FORMAT(' . $field->field_name . ', "%Y-%m-%d")',
                        'order' => Report::GROUP_LABEL,
                        'asc'   => false,
                        'limit' => 90,
                        'showOther' => false,
                        'showTable' => false,
                        'reverse' => true,
                        'infoType' => $infoType,
                    );
                    $data[] = array(
                        'title' => Yii::t('Report', '{type} {group} per week', array('{type}' => ucfirst($infoType), '{group}' => $field->field_label)),
                        'type'  => Report::TYPE_COLUMN,
                        'where' => $field->field_name . ' IS NOT NULL',
                        'group' => 'DATE_FORMAT(DATE_SUB(' . $field->field_name . ', INTERVAL (if(DATE_FORMAT(' . $field->field_name . ', "%w") = 0,7,DATE_FORMAT(' . $field->field_name . ', "%w")))-1 DAY), "%Y-%m-%d")',
                        'order' => Report::GROUP_LABEL,
                        'asc'   => true,
                        'limit' => 0,
                        'showTable' => false,
                        'infoType' => $infoType,
                    );
                    $data[] = array(
                        'title' => Yii::t('Report', '{type} {group} per month', array('{type}' => ucfirst($infoType), '{group}' => $field->field_label)),
                        'type'  => Report::TYPE_COLUMN,
                        'where' => $field->field_name . ' IS NOT NULL',
                        'group' => 'DATE_FORMAT(' . $field->field_name . ', "%Y-%m")',
                        'order' => Report::GROUP_LABEL,
                        'asc'   => true,
                        'limit' => 0,
                        'showTable' => false,
                        'infoType' => $infoType,
                    );
                    break;
                }
                default: {
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * get bug live days data
     *
     * @param string $where
     * @return array
     */
    private function getBugLiveDaysData($productId, $where, $show = false)
    {
        $infoClass = ucfirst(Info::TYPE_BUG) . 'InfoView';
        $info = new $infoClass();
        $table = $info->tableName();
        $addOnTableName = '{{ettonbug_' . $productId . '}}';
        $condition = 'bug_status = "Closed"';
        $condition .= ' AND ' . $where;
        $data = array();

        if($show)
        {
            $rawData = Yii::app()->db->createCommand()
                ->select('(TO_DAYS(closed_at) - TO_DAYS(created_at)) as ' . Report::GROUP_LABEL . ', COUNT(*) as ' . Report::COUNT_LABEL)
                ->from($table . ',' . $addOnTableName)
                ->where($condition . ' AND ' . $table . '.id = ' . $addOnTableName . '.bug_id')
                ->order(Report::COUNT_LABEL . ' ASC')
                ->group(Report::GROUP_LABEL)
                ->queryAll();
            $data = array(
                array(Report::GROUP_LABEL => Yii::t('Report', '0 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '1 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '2 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '3 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '4 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '5 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '6 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '7 days'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '1-2 weeks'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '2-4 weeks'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '1-3 months'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', '3-6 months'), Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', 'other'), Report::COUNT_LABEL => 0),
            );

            foreach($rawData as $row)
            {
                $days = (int)$row[Report::GROUP_LABEL];
                $count = (int)$row[Report::COUNT_LABEL];
                if(7 >= $days)
                {
                    $data[$days][Report::COUNT_LABEL] += $count;
                }
                else if(14 >= $days && 7 <= $days)
                {
                    $data[8][Report::COUNT_LABEL] += $count;
                }
                else if(28 >= $days)
                {
                    $data[9][Report::COUNT_LABEL] += $count;
                }
                else if(90 >= $days)
                {
                    $data[10][Report::COUNT_LABEL] += $count;
                }
                else if(180 >= $days)
                {
                    $data[11][Report::COUNT_LABEL] += $count;
                }
                else if(180 < $days)
                {
                    $data[12][Report::COUNT_LABEL] += $count;
                }
            }
        }

        return array(
            'title' => Yii::t('Report', 'bug per live days'),
            'type'  => Report::TYPE_BAR,
            'data' => $data,
            'infoType' => Info::TYPE_BUG,
        );
    }

    private function getBugHistorysData($productId, $where, $show)
    {
        $infoClass = ucfirst(Info::TYPE_BUG) . 'InfoView';
        $info = new $infoClass();
        $infoTable = $info->tableName();
        $actionClass = ucfirst(Info::TYPE_BUG) . 'Action';
        $action = new $actionClass();
        $actionTable = $action->tableName();
        $addOnTableName = '{{ettonbug_' . $productId . '}}';
        $table = $infoTable . ',' . $actionTable . ',' . $addOnTableName;

        $condition = $infoTable . '.id = ' . $actionTable . '.buginfo_id AND bug_status = "Closed" AND '
                . $infoTable . '.id = ' . $addOnTableName . '.bug_id';
        $condition .= ' AND ' . $where;
        $data = array();
        if($show)
        {
            $rawData = Yii::app()->db->createCommand()
                    ->select($infoTable . '.id as ' . Report::GROUP_LABEL . ', COUNT(*) as ' . Report::COUNT_LABEL)
                    ->from($table)
                    ->where($condition)
                    ->order(Report::COUNT_LABEL . ' ASC')
                    ->group(Report::GROUP_LABEL)
                    ->queryAll();
            $data = array(
                array(Report::GROUP_LABEL => 1, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 2, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 3, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 4, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 5, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 6, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 7, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 8, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 9, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 10, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 11, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 12, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 13, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 14, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => 15, Report::COUNT_LABEL => 0),
                array(Report::GROUP_LABEL => Yii::t('Report', 'other'), Report::COUNT_LABEL => 0)
            );

            foreach($rawData as $row)
            {
                $count = (int)$row[Report::COUNT_LABEL];
                if($count > 15)
                {
                    $data[15][Report::COUNT_LABEL] += 1;
                }
                else
                {
                    $data[$count-1][Report::COUNT_LABEL] += 1;
                }
            }
        }

        return array(
            'title' => Yii::t('Report', 'bug per history'),
            'type'  => Report::TYPE_COLUMN,
            'data' => $data,
            'infoType' => Info::TYPE_BUG,
        );
    }
}
?>