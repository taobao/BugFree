<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/bugfree3_basic.css" />
        <?php Yii::app()->getClientScript()->registerCoreScript('jquery'); ?>
        <?php Yii::app()->getClientScript()->registerCoreScript('bgiframe'); ?>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/js/overlib.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/js/main.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/<?php echo $color; ?>.css" />
        <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_<?php echo $color; ?>.ico" type="image/x-icon" />
        <title><?php echo Yii::t('Report', '{type} report', array('{type}' => ucfirst($infoType))); ?></title>
        <style>
            html, body {
                background: #FFFFFF;
            }

            .subtilte {
                float: right;
                color: #FFFFFF;
                font-size: 15px;
                font-weight: bold;
                margin: 0px 10px;
                line-height: 41px;
            }

            #top {
                position: fixed;
                width: 100%;
                height: 41px;
                top: 0px;
                left: 0px;
                z-index: 10000;
            }

            #nav {
                position: fixed;
                left: 0px;
                top: 41px;
                height: 99%;
                overflow: auto;
                *float: left;
            }

            #nav ul {
                width: 225px;
                padding: 10px;
                margin: 0;
            }

            #nav li {
                list-style: none;
                padding: 0;
                margin: 3px 0px;
            }

            #nav li a {
                font-size: 12px;
            }

            #nav li a.selected {
                color: #333333;
            }

            #nav li a.selected:hover {
                text-decoration: none;
                cursor: text;
            }

            #nav li input {
                margin-right: 5px;
            }

            #content {
                position: absolute;
                top: 51px;
                width: 100%;
            }

            #main {
                margin: 0px 0px 0px 260px;
            }

            div.report {
                border: 1px solid #AAAAAA;
                text-align: center;
                margin: 0px 10px 10px 0px;
            }

            table.list {
                border-collapse: collapse;
                margin: 0px auto 10px auto;
            }

            table.list td {
                border-top: 1px solid #AAAAAA;
                border-bottom: 1px solid #AAAAAA;
            }
        </style>
    </head>
    <body>
        <div id="top">
            <div id="logo">
                <a href="<?php echo Yii::app()->createUrl('info/index',array('product_id'=>$productId,'type'=>Info::TYPE_BUG));?>">
                    <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/' . $color . '/logo.png'; ?>"
                         alt="BugFree" title="BugFree"/>
                </a>
            </div>
            <div class="subtilte">
                <?php echo Yii::t('Report', '{type} report', array('{type}' => ucfirst($infoType))); ?>
            </div>
        </div>
        <div id="content">
            <div id="nav">
                <ul>
                    <?php
                    echo CHtml::beginForm(Yii::app()->createUrl('report/index'), 'get');
                    //echo CHtml::beginForm('index.php', 'get');
                    //echo CHtml::hiddenField('r', 'report/index');
                    echo CHtml::hiddenField('product_id', $productId);
                    echo CHtml::hiddenField('type', $infoType);
                    echo CHtml::openTag('li');
                    echo CHtml::checkBox('select-all', false, array('class' => 'select-all'));
                    echo CHtml::submitButton(Yii::t('Report', 'submit'));
                    echo CHtml::closeTag('li');
                    foreach ($reports as $key => $report) {
                        echo CHtml::openTag('li');
                        $checked = false;
                        if(in_array($key, $selected))
                        {
                            $checked = true;
                        }
                        echo CHtml::checkBox('selected[]', $checked, array('id' => 'report' . $key, 'value' => $key, 'class' => 'report-check'));
                        echo CHtml::label($report->title, 'report-' . $key);
                        echo CHtml::closeTag('li');
                    }
                    echo CHtml::openTag('li');
                    echo CHtml::checkBox('select-all', false, array('class' => 'select-all'));
                    echo CHtml::submitButton(Yii::t('Report', 'submit'));
                    echo CHtml::closeTag('li');
                    echo CHtml::endForm();
                    ?>
                </ul>
                <br />
            </div>
            <div id="main">
                <?php
                foreach ($reports as $key => $report) {
                    if(!in_array($key, $selected))
                    {
                        continue;
                    }
                    echo CHtml::openTag('div', array('class' => 'report', 'id' => 'report-div-' . $key));
                    $data = $report->toChartData();
                    $this->Widget('application.extensions.highcharts.HighchartsWidget', array(
                        'options' => array(
                            'chart' => array(
                                'marginTop' => 40,
                            ),
                            'title' => array(
                                'text' => $report->title
                            ),
                            'xAxis' => $report->xAxis,
                            'yAxis' => $report->yAxis,
                            'exporting' => array('enabled' => false),
                            'credits' => array('enabled' => false),
                            'plotOptions' => array(
                                'pie' => array(
                                    'allowPointSelect' => true,
                                    'cursor' => 'pointer',
                                    'dataLabels' => array(
                                        'enabled' => true,
                                        'formatter' => 'js:function() {
                                            return this.point.name + ": " + 
                                                Math.round(this.point.percentage*10)/10+ "%";
                                        }'
                                    ),
                                ),
                            ),
                            'legend' => array('enabled' => false),
                            'tooltip' => $report->tooltip,
                            'series' => $data,
                        ),
                        'chartType' => 'highcharts',
                        'htmlOptions' => array('style' => 'height: 400px; margin: 0px 10px;')
                    ));

                    if ($report->showTable) {
                        echo CHtml::openTag('table', array('class' => 'list'));
                        foreach ($data[0]['data'] as $row) {
                            echo CHtml::openTag('tr');
                            echo CHtml::openTag('td');
                            echo CHtml::openTag('div', array('style' => 'width: 8px; height:8px; background: ' . $row['color']));
                            echo CHtml::closeTag('div');
                            echo CHtml::closeTag('td');
                            echo CHtml::openTag('td');
                            echo $row['name'];
                            echo CHtml::closeTag('td');
                            echo CHtml::openTag('td');
                            echo $row['y'];
                            echo CHtml::closeTag('td');
                            echo CHtml::closeTag('tr');
                        }
                        echo CHtml::closeTag('table');
                    }
                    echo CHtml::closeTag('div');
                }
                ?>
            </div>
        </div>
    </body>
    <script  type="text/javascript">
        $(document).ready(function(){
            $("#nav label").click(function(){
                var index = $(this).attr('for').substr(7);
                var current = $("div#report-div-" + index);
                if(null != $(current).position()) {
                    $(window).scrollTop(($(current).position().top));
                }
            });
            $("input.select-all").click(function() {
                if("checked" == $(this).attr("checked")) {
                    $("#nav input[type=checkbox]").attr("checked", "checked");
                } else {
                    $("#nav input[type=checkbox]").removeAttr("checked");
                }
            });
        });
    </script>
</html>