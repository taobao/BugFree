<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Pager
 *
 * @author jiaoba <jiaoba@taobao.com>
 * @version 3.0
 */


Yii::import('zii.widgets.CBaseListView');
Yii::import('zii.widgets.grid.CDataColumn');
Yii::import('zii.widgets.grid.CLinkColumn');
Yii::import('zii.widgets.grid.CButtonColumn');
Yii::import('zii.widgets.grid.CCheckBoxColumn');

class View extends CBaseListView
{
    const FILTER_POS_HEADER='header';
    const FILTER_POS_FOOTER='footer';
    const FILTER_POS_BODY='body';

    private $_formatter;
    public $columns = array();
    public $rowCssClass = array('odd', 'even');
    public $rowCssClassExpression;
    public $showTableOnEmpty = true;
    public $selectionChanged;
    public $selectableRows = 1;
    public $baseScriptUrl;
    public $cssFile;
    public $nullDisplay = '&nbsp;';
    public $filterCssClass = 'filters';
    public $filterPosition = 'body';
    public $filter;
    public $hideHeader = false;
    public $template = "{pager}{items}";
    public $customTools = "";
    public $pageNumArr = array('10' => '10', '20' => '20', '35' => '35','50' => '50', '100' => '100');

    public function init()
    {
        parent::init();
        $this->initColumns();
    }

    protected function initColumns()
    {
        if($this->columns === array())
        {
            if($this->dataProvider instanceof CActiveDataProvider)
                $this->columns = $this->dataProvider->model->attributeNames();
            else if($this->dataProvider instanceof IDataProvider)
            {
                // use the keys of the first row of data as the default columns
                $data = $this->dataProvider->getData();
                if(isset($data[0]) && is_array($data[0]))
                    $this->columns = array_keys($data[0]);
            }
        }
        $id = $this->getId();
        foreach($this->columns as $i => $column)
        {
            if(is_string($column))
                $column = $this->createDataColumn($column);
            else
            {
                if(!isset($column['class']))
                    $column['class'] = 'CDataColumn';
                $column = Yii::createComponent($column, $this);
            }
            if(!$column->visible)
            {
                unset($this->columns[$i]);
                continue;
            }
            if($column->id === null)
                $column->id = $id . '_c' . $i;
            $this->columns[$i] = $column;
        }

        foreach($this->columns as $column)
            $column->init();
    }

    protected function createDataColumn($text)
    {
        if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches))
            throw new CException(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
        $column = new CDataColumn($this);
        $column->name = $matches[1];
        if(isset($matches[3]))
            $column->type = $matches[3];
        if(isset($matches[5]))
            $column->header = $matches[5];
        return $column;
    }

    public function run()
    {
        echo CHtml::openTag($this->tagName, $this->htmlOptions) . "\n";

        $this->renderContent();

        echo CHtml::closeTag($this->tagName);
    }

    public function renderItems()
    {
        if($this->dataProvider->getItemCount() > 0 || $this->showTableOnEmpty)
        {
            echo "<div id=\"SearchResultDiv\" style=\"overflow: auto; width:100%; \"><table style=\"white-space:nowrap\"  class=\"{$this->itemsCssClass}\">\n";
            $this->renderTableHeader();
            $this->renderTableFooter();
            $this->renderTableBody();
            echo "</table></div>";
        }
        else
            $this->renderEmptyText();
    }

    public function renderTableHeader()
    {
        if(!$this->hideHeader)
        {
            echo "<thead>\n";

            if($this->filterPosition === self::FILTER_POS_HEADER)
                $this->renderFilter();

            echo "<tr>\n";
            foreach($this->columns as $column)
                $column->renderHeaderCell();
            echo "</tr>\n";

            if($this->filterPosition === self::FILTER_POS_BODY)
                $this->renderFilter();

            echo "</thead>\n";
        }
        else if($this->filter !== null && ($this->filterPosition === self::FILTER_POS_HEADER || $this->filterPosition === self::FILTER_POS_BODY))
        {
            echo "<thead>\n";
            $this->renderFilter();
            echo "</thead>\n";
        }
    }

    public function renderFilter()
    {
        if($this->filter !== null)
        {
            echo "<tr class=\"{$this->filterCssClass}\">\n";
            foreach($this->columns as $column)
                $column->renderFilterCell();
            echo "</tr>\n";
        }
    }

    public function renderTableFooter()
    {
        $hasFilter = $this->filter !== null && $this->filterPosition === self::FILTER_POS_FOOTER;
        $hasFooter = $this->getHasFooter();
        if($hasFilter || $hasFooter)
        {
            echo "<tfoot>\n";
            if($hasFooter)
            {
                echo "<tr>\n";
                foreach($this->columns as $column)
                    $column->renderFooterCell();
                echo "</tr>\n";
            }
            if($hasFilter)
                $this->renderFilter();
            echo "</tfoot>\n";
        }
    }

    public function renderTableBody()
    {
        $data = $this->dataProvider->getData();
        $n = count($data);
        echo "<tbody>\n";

        if($n > 0)
        {
            for($row = 0; $row < $n; ++$row)
                $this->renderTableRow($row);
        }
        else
        {
            echo '<tr><td colspan="' . count($this->columns) . '">';
            $this->renderEmptyText();
            echo "</td></tr>\n";
        }
        echo "</tbody>\n";
    }

    public function renderTableRow($row)
    {
        if($this->rowCssClassExpression !== null)
        {
            $data = $this->dataProvider->data[$row];
            echo '<tr class="' . $this->evaluateExpression($this->rowCssClassExpression, array('row' => $row, 'data' => $data)) . '">';
        }
        else if(is_array($this->rowCssClass) && ($n = count($this->rowCssClass)) > 0)
            echo '<tr class="' . $this->rowCssClass[$row % $n] . '">';
        else
            echo '<tr>';
        foreach($this->columns as $column)
            $column->renderDataCell($row);
        echo "</tr>\n";
    }

    public function renderPager()
    {
        if(!$this->enablePagination)
            return;

        $pager = array();
        $class = 'LinkPager';
        $count = $this->dataProvider->totalItemCount;
        $total = $this->dataProvider->getPagination()->getPageCount();
        $current = $this->dataProvider->getPagination()->getCurrentPage();
        if($total > 0)
        {
            $current++;
        }
        $pager['header'] = '<b>' . $current . '</b>/<b>' . $total . '</b>'.
                Yii::t('Pager','Page Total:') .'<b>'. $count.'</b>'
                . CHtml::dropDownList('pageSize',
                        $this->dataProvider->getPagination()->getPageSize(),
                        $this->pageNumArr,
                        array(
                            'class' => 'page-size',
                            'onchange' => 'var data = {"pagesize": $(this).val()};'.
                                '$.get("'.Yii::app()->createUrl('page/setPageSize').'", data, function(){window.location.href=window.location.href;'.
                                'window.location.reload;});'       
                ));

        $pager['pages'] = $this->dataProvider->getPagination();
        echo '<div style="margin:6px 0 0 5px;float:left;">'.  $this->customTools.'</div><div class="' . $this->pagerCssClass . '">';
        $this->widget($class, $pager);
        echo '</div>';
    }

    public function getHasFooter()
    {
        foreach($this->columns as $column)
            if($column->getHasFooter())
                return true;
        return false;
    }

    public function getFormatter()
    {
        if($this->_formatter === null)
            $this->_formatter = Yii::app()->format;
        return $this->_formatter;
    }

    public function setFormatter($value)
    {
        $this->_formatter = $value;
    }

}

?>