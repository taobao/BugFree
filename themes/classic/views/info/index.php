<script type="text/javascript">
    var $infoType = '<?php echo $infoType; ?>';
    var $baseUrl = '<?php echo Yii::app()->getBaseUrl(); ?>'
    var $showType = '<?php echo $showType; ?>';
    var $productId = <?php echo $productId; ?>;
    var $imgFolder = '<?php echo Yii::app()->theme->baseUrl . '/assets/images/'; ?>';
    var $productModuleId = <?php echo $productModuleId; ?>;
    var $totalNum = <?php echo empty($totalNum)?0:$totalNum; ?>;
    var $totalNumErrorStr = '<?php echo Yii::t('Common','items can not exceed 5000'); ?>';
    var $emptyTemplateTip = '<?php echo Yii::t('Common','No template can be used'); ?>';
    var $showMyQueryDiv = '<?php echo $showMyQueryDiv; ?>';
    function reloadPage(event, treeId, treeNode)
    {
        window.location = '<?php echo Yii::app()->createUrl('info/index',
                array('type'=>$infoType,'product_id'=>$productId,'productmodule_id'=>''));?>'+
            treeNode.id;
    }
    function disableHiddenPager()
    {
        $('div.pager li.hidden a').removeAttr('href');
    }
    $(function(){
        initShowGotoBCR();
        setSearchHeight();
        initTreeHeight();
        disableHiddenPager();

        if('result' != $infoType)
        {
            $('#create_div').css({left:$('#createli').offset().left+4+'px'});
            $('#create_div').show();
            var inNewDiv = false;
            $('#create_div_more').bind('mouseenter',function(){
                showCreate();
            })
            $('#create_div_more').bind('mouseleave',function(){
                setTimeout(function(){
                    if(!inNewDiv)
                    {
                        $('#create_template_div').hide();
                        $('#create_div_more').removeClass('show_template');
                    }
                },100);
            })
            $('#create_template_div').bind('mouseleave',function(){
                inNewDiv = false;
                $('#create_div_more').removeClass('show_template');
                $(this).hide();
            })
            $('#create_template_div').bind('mouseenter',function(){
                inNewDiv = true;
            })
        }
    })

    function showCreate() {
        $('#create_div_more').addClass('show_template');
        var inputOffset = $('#create_div_more').offset();
        $("#create_template_div").bgiframe();
        $("#create_template_div").css({
            left:inputOffset.left + "px",
            top:(inputOffset.top + $("#create_div_more").outerHeight()-1) + "px"
        }).show();
    }

    function deleteTemplateOrQuery($operationType,$id)
    {
        if(!confirm('<?php echo Yii::t('Common', 'Sure to delete?'); ?>'))
        {
            return;
        }
        else
        {
            jQuery.get("<?php echo Yii::app()->createUrl('search/deleteTemplateOrQuery');?>", {'id':$id,'type':$operationType}, function (data, textStatus){
                if('success' == textStatus)
                {
                    $('#user_'+$operationType+'_'+$id).remove();
                    if('query' == $operationType)
                    {
                        window.location.href = window.location.href;
                    }
                    else
                    {
                        $templateLi = $('#create_template_div ul li');
                        if(0 == $templateLi.length)
                        {
                            $('#create_template_div').empty().append($emptyTemplateTip);
                        }
                    }                   
                }
            });
        }
        return false;
    }

    function exportXml()
    {
        if($totalNum>5000)
        {
            alert($totalNumErrorStr);
        }
        else
        {
            var $oldAction = document.SearchBug.action;
            document.SearchBug.action = '<?php echo Yii::app()->createUrl('info/export',array('type'=>$infoType,'product_id'=>$productId));?>';
            document.SearchBug.submit();
            document.SearchBug.action = $oldAction;
        }
    }
    function mark($infoId,$isAdd)
    {
        jQuery.get("<?php echo Yii::app()->createUrl('search/mark');?>", {'id':$infoId,'type':$infoType,'isAdd':$isAdd}, function (data, textStatus){
            if('success' == textStatus)
            {
                $isAdd = 1-$isAdd;
                $('#marklink'+$infoId+' img').attr('src','<?php echo Yii::app()->theme->baseUrl . '/assets/images/';?>'+'flag_'+$isAdd+'.gif');
                $('#marklink'+$infoId).attr('href','javascript:mark('+$infoId+','+$isAdd+');void(0);');
            }
        });
    }
    $(window).resize(function() {
        setSearchHeight();
        resetTreeHeight();
    });

    function expandLeft()
    {
        var $leftDiv = $('#leftindex');
        var $rightDiv = $('#rightindex');
        var $img = $('#expandindex img');
        var $expand = 0;
        if('none' == $leftDiv.css('display'))
        {
            $expand = 1;
            $leftDiv.removeClass('not_expand');
            $rightDiv.removeClass('not_expand');
            $img.attr('src', '<?php echo Yii::app()->theme->baseUrl . '/assets/images/'; ?>late_left.gif');
        }
        else
        {
            $leftDiv.addClass('not_expand');
            $rightDiv.addClass('not_expand');
            $img.attr('src', '<?php echo Yii::app()->theme->baseUrl . '/assets/images/'; ?>late_right.gif');
        }
        jQuery.get("<?php echo Yii::app()->createUrl('search/setExpand');?>", {'expand':$expand}, function (data, textStatus){
            if('success' == textStatus)
            {
            }
        });
    }

    function setOutputType()
    {
        var showType = 'report';
        if($showType == 'report')
        {
            showType = 'grid';
        }
        $.get("<?php echo Yii::app()->createUrl('page/setOutPutType');?>",
        {'product_id':$productId,'info_type':$infoType,'show_type':showType},
        function(){window.location.href=window.location.href;window.location.reload;})
    }

    function setSearchHeight()
    {
        $height = $(window).height();
        $topheight = $('#SearchBlankCover').height();
        $('#indexmain').css('height',$height-63+'px');
        $('#SearchResultDiv').css('height',$height-$topheight-94+'px');
        $('#expandindex').show();
    }

    function initTreeHeight()
    {
        $height = $(window).height();
        $queryDiv = $('div.querydiv');
        if($showMyQueryDiv == '1')
        {
            $queryDiv.show();
            $('#product_module_tree').css('height',$height-320+'px');
            $('#query_div_switch').attr("src", $imgFolder+'arrow_down.gif');
        }
        else
        {
            $queryDiv.hide();
            $('#product_module_tree').css('height',$height-120+'px');
            $('#query_div_switch').attr("src", $imgFolder+'arrow_up.gif');
        }
    }

    function resetTreeHeight()
    {
        $height = $(window).height();
        $queryDiv = $('div.querydiv');      
        if($queryDiv.is(':visible'))
        {
            $('#product_module_tree').css('height',$height-320+'px');
        }
        else
        {
            $('#product_module_tree').css('height',$height-120+'px');
        }
    }

    function setQueryHeight()
    {
        $queryDiv = $('div.querydiv');
        var $expand = 0;
        if($queryDiv.is(':visible'))
        {
            $('#product_module_tree').css('height',$height-120+'px');
            $('#query_div_switch').attr("src", $imgFolder+'arrow_up.gif');
            $queryDiv.hide();
        }
        else
        {
            $expand = 1;
            $('#product_module_tree').css('height',$height-320+'px');
            $('#query_div_switch').attr("src", $imgFolder+'arrow_down.gif');
            $queryDiv.show();
        }
        jQuery.get("<?php echo Yii::app()->createUrl('search/setMyQueryDiv');?>", {'expand':$expand}, function (data, textStatus){});
    }

    function zTreeOnAsyncSuccess(event, treeId, msg)
    {
        var node = product_module_treeTree.getNodeByParam("id",$productModuleId);
        var selectedNode = product_module_treeTree.getSelectedNode();
        if(node != selectedNode)
        {
            product_module_treeTree.selectNode(node);
        }
    }
    function caseBatchRun()
    {
        if(0 == $totalNum)
        {
            alert('<?php echo Yii::t('ResultInfo','No case selected'); ?>');
        }
        else if(100<$totalNum)
        {
            alert('<?php echo Yii::t('ResultInfo','The count of cases can not be more than 100'); ?>');
        }
        else
        {
            openWindow('<?php echo Yii::app()->createUrl('info/edit',array('type'=>Info::TYPE_RESULT,'action'=>ResultInfo::ACTION_BATCH_OPEN,'batch_product_id'=>$productId)); ?>','_blank');
        }
    }
</script>
<div id="create_div" >
    <img style="float:left;" src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/add.gif'; ?>">
    <a href="<?php echo Yii::app()->createUrl('info/edit',array('type'=>$infoType,'action'=>  BugInfo::ACTION_OPEN,'product_id'=>$productId)); ?>" target="_blank">&nbsp;<?php echo Yii::t('Common','New').'&nbsp;'.ucfirst($infoType);?>&nbsp;&nbsp;&nbsp;</a>
    <img id="create_div_more" src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/create_down.gif'; ?>">
</div>
<div id="create_template_div" class="createtemplate" style="display:none;" onmouseover="inNewDiv = true;">
    <?php echo $templateStr; ?>
</div>
<div id="leftindex" class="<?php echo $expandClass; ?>">
    <?php
    echo '<div class="leftmenu">' . InfoService::getProductSelect($productId,$infoType) . '</div>';
    ?>
    <div id="product_module_tree" class="leftmenu" style="overflow-y:scroll;overflow-x: auto;padding-top: 0px;margin-top: 0px;"></div>
    <?php
    $this->widget('application.extensions.ztree.ZTreeWidget', array(
        'name' => 'product_module_tree',
        'id' => 'product_module_tree',
        'value' => 0,
        'zNodes' => '[]',
        'setting' => '{
                    showIcon: false,
                    showLine: true,
                    treeNodeKey: "id",
                    expandSpeed: "",
                    async: true,
                    asyncUrl: "'.Yii::app()->createUrl('search/getProductModule').'",
                    asyncParam: ["id"],
                    asyncParamOther : ["product_id", '.$productId.',"productmodule_id",'.$productModuleId.'],
                    callback: {
                            click:reloadPage,
                            asyncSuccess:zTreeOnAsyncSuccess
                    }
            }'
    ));
    ?>

    <?php
        if(1 == $showMyQueryDiv)
        {
            $imgName = 'arrow_down.gif';
            $displayStyle = '';
        }
        else
        {
            $imgName = 'arrow_up.gif';
            $displayStyle = 'display:none';
        }
    ?>
    <div id="querymenu" class="leftmenu" style="height:auto;position: absolute;bottom:16px;left: 20px;width: 230px;">
        <div class="title">
            <a href="#" onclick="setQueryHeight();return false;" style="float:right;margin-top: 0px;_margin-top: -8px;padding: 4px;">
                <img style="width:16px;" id="query_div_switch" src="<?php echo Yii::app()->theme->baseUrl.'/assets/images/'.$imgName; ?>" >
            </a>
            <?php echo Yii::t('Common', 'My Query'); ?>          
        </div>
        <div class="querydiv" style="<?php echo $displayStyle;?>">
            <ul>
                <?php echo $leftMenu; ?>
            </ul>
        </div>
    </div>
</div>
<div id="expandindex" onclick="expandLeft();" style="display:none;">
    <table cellpadding="0" cellspacing="0" border="0" style="height:100%;">
        <tr>
            <td style="vertical-align: middle;margin: 0px 0px 0px 0px;padding: 0px 0px 0px 0px;width: 10px;">
                <?php
                    if('' == $expandClass)
                    {
                        echo '<img src="'.Yii::app()->theme->baseUrl . '/assets/images/late_left.gif" alt=""/>';
                    }
                    else
                    {
                        echo '<img src="'.Yii::app()->theme->baseUrl . '/assets/images/late_right.gif" alt=""/>';
                    }
                ?>
            </td>
        </tr>
    </table>
</div>
<div id="rightindex" class="<?php echo $expandClass; ?>">
    <?php
                echo $this->renderPartial('_search_page',
                        array(
                            'productId' => $productId,
                            'searchConditionHtml' => $searchConditionHtml,
                            'searchFieldConfig' => $searchFieldConfig,
                            'infoType' => $infoType,
                            'jsValueStr' => $jsValueStr,
                            'jsOperatorStr' => $jsOperatorStr,
                            'searchConditionHtmlTemplate' => $searchConditionHtmlTemplate,
                            'queryTitle' => $queryTitle));
                echo $this->renderPartial('_select_show_field',
                        array('selectFieldOptionStr' => $selectFieldOptionStr,
                            'showFieldOptionStr' => $showFieldOptionStr,
                            'defaultSelectFieldOptionStr' => $defaultSelectFieldOptionStr,
                            'defaultShowFieldOptionStr' => $defaultShowFieldOptionStr));
                if((Info::TYPE_CASE == $infoType) || (Info::TYPE_BUG == $infoType))
                {
                    echo $this->renderPartial('_import_info', array('productId' => $productId, 'infoType'=>$infoType, 'productModuleId' => $productModuleId));
                }

                if($showType == Info::SHOW_TYPE_GRID)
                {
                    $tools = '<a onclick="$(\'#select_show_field_dialog\').dialog(\'open\'); return false;" id="CustomSetLink" href="javascript:void(0);">' .
                        Yii::t('Common', 'Custom Fields') . '</a>&nbsp;|&nbsp;<a href="javascript:exportXml();">' . Yii::t('Common', 'Export') . '</a>';
                    if((Info::TYPE_CASE == $infoType) || (Info::TYPE_BUG == $infoType))
                    {
                        $tools .= '&nbsp;|&nbsp;<a onclick="$(\'#import_info_dialog\').dialog(\'open\'); return false;" href="javascript:void(0);">' .
                                Yii::t('Common', 'Import') . '</a>';
                    }
                    if(Info::TYPE_CASE == $infoType)
                    {
                        $tools .= '&nbsp;|&nbsp;<a href="javascript:caseBatchRun();">' . Yii::t('Common', 'Batch Run') . '</a>';
                    }
//                    $tools .= '&nbsp;|&nbsp;<span id="VReport"><a href="javascript:setOutputType()">' .
//                        Yii::t('Common', 'Report') . '</a></span>';
                    $tools .= '&nbsp;|&nbsp;<span id="VReport"><a target="_blank" href="'.Yii::app()->createUrl('report/index',array('type'=>$infoType,'product_id'=>$productId)).'">' .
                        Yii::t('Common', 'Report') . '</a></span>';
                    $this->widget('View', array(
                        'id' => 'searchresult-grid',
                        'dataProvider' => $dataProvider,
                        'rowCssClassExpression'=>$rowCssClassExpressionStr,
                        'customTools' => $tools,
                        'columns' => $viewColumnArr
                    ));
                }
                else
                {
                    echo '<div style="margin:6px 0 4px 5px;"><a href="javascript:setOutputType()">'.Yii::t('Common','Table').'</a></div>';
                    echo '<div id="SearchResultDiv" style="background-color: grey;overflow:auto;">';
                    echo $this->renderPartial('_report_search');
                    echo '</div>';
                }

//                $tools = '<a onclick="$(\'#select_show_field_dialog\').dialog(\'open\'); return false;" id="CustomSetLink" href="javascript:void(0);">' .
//                        Yii::t('Common', 'Custom Fields') . '</a>&nbsp;|&nbsp;<span id="VReport"><a href="javascript:alert(\'to do!!!!\');">' .
//                        Yii::t('Common', 'Report') . '</a></span>&nbsp;|&nbsp;<a href="javascript:exportXml();">' . Yii::t('Common', 'Export') . '</a>';
//                if('case' == $infoType)
//                {
//                    $tools .= '&nbsp;|&nbsp;<a href="javascript:alert(\'to do!!!!\');">' .
//                        Yii::t('Common', 'Import') . '</a></span>&nbsp;|&nbsp;<a href="javascript:alert(\'to do!!!!\');">' . Yii::t('Common', 'Batch Run') . '</a>';
//                }
//
//                $this->widget('View', array(
//                    'id' => 'searchresult-grid',
//                    'dataProvider' => $dataProvider,
//                    'rowCssClassExpression'=>$rowCssClassExpressionStr,
//                    'customTools' => $tools,
//                    'columns' => $viewColumnArr
//                ));
    ?>
</div>
