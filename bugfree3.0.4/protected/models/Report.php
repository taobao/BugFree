<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * This is report model
 */
class Report
{
    public $title;
    public $productId;
    public $infoType;
    public $where;
    public $count;
    public $group;
    public $limit;
    public $order;
    public $asc;
    public $type;
    public $color;
    public $xAxis;
    public $yAxis;
    public $tooltip;
    public $showTable;
    public $showOther;
    public $table;
    
    private $data;
    
    const TYPE_PIE = 'pie';
    const TYPE_BAR = 'bar';
    const TYPE_COLUMN = 'column';
    const TYPE_LINE = 'line';
    
    const GROUP_LIMIT = 15;
    
    const CATEGORY_LIMIT = 20;
    const CATEGORY_STEP = 10;
    
    const COUNT_LABEL = 'lcount';
    const GROUP_LABEL = 'lgroup';
    const COLOR_LABEL = 'lcolor';
    const OTHER_LABEL = 'other';
    const EMPTY_LABEL = 'empty';
    
    public static $COLOR = array(
       '#4572A7', '#AA4643', '#89A54E', 
       '#80699B', '#3D96AE', '#DB843D',
       '#92A8CD', '#A47D7C', '#B5CA92',
       '#336666', '#990033', '#999933',
    );
    
    /**
     * This is construct function
     * 
     * @param string $infoType
     * @param integer $productId
     * @param string $group
     * @param string $type
     * @param array $config
     */
    public function __construct($infoType, $productId, $type, $where = '1 = 1', $config = array(), $show = false)
    {
        $this->productId = $productId;
        $this->group     = 'id'; 
        $this->type      = $type;
        $this->infoType  = $infoType;
        $this->where     = $where;
        $this->order     = Report::COUNT_LABEL;
        $this->count     = 'count(*)';
        $this->limit     = Report::GROUP_LIMIT;
        $this->asc       = false;
        $this->showTable = true;
        $this->showOther = true;
        $this->xAxis     = array('min' => 0);
        $this->yAxis     = array('min' => 0, 'title' => array('text' => null));
        $this->reverse   = false;
        $this->tooltip   = array('formatter' => 'js:function(){                       
                                    return this.point.name + ": <b>" + this.y + "</b>";
                                }');
        
        foreach($config as $key => $val)
        {
            if('where' == $key)
            { 
                $this->where .= ' AND ' . $val;
                continue;
            }
            $this->$key = $val;
        }
        
        $this->title = Yii::t('Report', $this->title);
        
        if(!$show)
        {
            $this->data = array();
        }
        
        if(!isset($this->data))
        {
            $this->data = $this->getData();
        }
        
        if($this->reverse)
        {
            $this->data = array_reverse($this->data);
        }
        
        if(Report::TYPE_BAR == $this->type || Report::TYPE_COLUMN == $this->type)
        {
            $categories = array();
            $count = count($this->data);
            foreach($this->data as $key => $row)
            {
                if($count > Report::CATEGORY_LIMIT)
                {
                    $step = (int)$count / Report::CATEGORY_STEP;
                    $res =  (int)($count - 1) % $step;
                    if($res == $key % $step)
                    {
                        $categories[] = $row[Report::GROUP_LABEL];
                    }
                    else
                    {
                        $categories[] = ' ';
                    }
                    $this->xAxis['labels'] = array(
                        'rotation' => -45,
                        'align' => 'right'
                    );
                }
                else
                {
                    $categories[] = empty($row[Report::GROUP_LABEL]) ? Yii::t('Report', Report::EMPTY_LABEL) : $row[Report::GROUP_LABEL];   
                }
            }
            $this->xAxis['categories'] = $categories;
        }
    }
    
    /**
     * getdata
     * 
     * @return array 
     */
    private function getData()
    {
        if(!isset($this->table))
        {
            $infoClass = ucfirst($this->infoType) . 'InfoView';
            $info = new $infoClass();
            $this->table = $info->tableName();
        }
        $data = array();
        $asc = $this->asc ? 'ASC' : 'DESC';
        $order = $this->order . ' ' . $asc;
        
        $addOnTableName = '{{etton' . $this->infoType . '_' . $this->productId . '}}';
        $command = Yii::app()->db->createCommand()
                ->select($this->group . ' as ' . Report::GROUP_LABEL . ', ' . $this->count . ' as ' . Report::COUNT_LABEL)
                ->from($this->table . ',' . $addOnTableName)
                ->where($this->where . ' AND {{' . $this->infoType . 'view}}.id = ' . $addOnTableName . '.' . $this->infoType . '_id')
                ->order($order)
                ->group('IFNULL(' . $this->group . ', "")');
        if(!$this->showOther && !empty($this->limit))
        {
            $data = $command->limit($this->limit);
        }
        $data = $command->queryAll();
        
        $count = count($data);
        
        if($count > $this->limit && $this->limit > 0)
        {
            $index = $this->limit - 1;
            $otherCount = 0;
            for($i = $index; $i < $count; $i++)
            {
                $otherCount += $data[$i][Report::COUNT_LABEL];
                unset($data[$i]);
            }
            if($this->showOther)
            {
                $data[$index] = array(
                    Report::GROUP_LABEL => Yii::t('Report', Report::OTHER_LABEL),
                    Report::COUNT_LABEL => $otherCount
                );
            }
        }
        
        return $data;
    }
    
    /**
     * to chart data
     * 
     * @return array
     */
    public function toChartData()
    {
        $data = array();

        foreach($this->data as $key => $row)
        {
            $colorCount = count(Report::$COLOR);
            
            $arr['name']  = $row[Report::GROUP_LABEL];
            $arr['y']     = (int)$row[Report::COUNT_LABEL];
            $arr['color'] = Report::$COLOR[$key % $colorCount];
            $arr['name']  = empty($arr['name']) ? Yii::t('Report', Report::EMPTY_LABEL) : $arr['name'];
            $data[] = $arr;
        }
        
        return array(
            array(
                'type' => $this->type,
                'data' => $data,
            )
        );
    }
}
?>