<script type="text/javascript">

var limitedFieldCount = <?php echo CommonService::getQueryLimitNumber(); ?>;               //the max search row num
var templateFieldNumber = '<?php echo Info::TEMPLATE_NUMBER; ?>';        //template number
var searchConditionTmp = '<?php echo $searchConditionHtmlTemplate ?>';
var searchParamsPreFix = '<?php echo Info::QUERY_GROUP_NAME; ?>';
var infoType = '<?php echo $infoType;?>';
var dateFormatError = '<?php echo Yii::t('Common','Please use valid date format. For example, 2009-10-8 or -7.'); ?>';
<?php
foreach($searchFieldConfig as $key=>$value)
{
    echo 'var field_'.$key.'_type=\''.$value['type'].'\';'."\n";
}
echo $jsValueStr;
echo $jsOperatorStr;

//load autocomplete resource
$this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                        'name' => 'search_autocomplete',
                        'urlOrData' => TestUser::getSearchUserUrl(TestUser::USER_TYPE_BOTH)
                        ), true);
?>
function updateQueryValue(index,isKeepOldValue)
{
    var $fieldName = $('#'+searchParamsPreFix+'_field'+index).val();
    var $operatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
    var $oldFieldValue = $('#'+searchParamsPreFix+'_value'+index).val();
    if(('severity' == $fieldName || 'priority' == $fieldName) && 'IN' == $operatorValue)
    {
        $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input type="text" size="16" value="" id="BugFreeQuery_value'+index+'" name="BugFreeQuery[value'+index+']">');
        if(true == isKeepOldValue)
        {
            $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
        }
        return;
    }
    
    eval('var fieldValueSelect='+'field_'+$fieldName+'_value;');
    var $newValue = replaceTemplateWithIndex(fieldValueSelect,index);
    $('#'+searchParamsPreFix+'_value'+index).replaceWith($newValue);
    eval('var fieldType='+'field_'+$fieldName+'_type');
    if(fieldType == 'people')
    {
        if('assign_to_name' == $fieldName)
        {
            if('bug' == infoType)
            {
                eval("$('#"+searchParamsPreFix+'_value'+index+"').autocompleter('<?php echo Yii::app()->createUrl('search/userList',array('p'=>-3));?>')");
            }
            else if('case' == infoType)
            {
                eval("$('#"+searchParamsPreFix+'_value'+index+"').autocompleter('<?php echo Yii::app()->createUrl('search/userList',array('p'=>-1));?>')");
            }
            else if('result' == infoType)
            {
                eval("$('#"+searchParamsPreFix+'_value'+index+"').autocompleter('<?php echo Yii::app()->createUrl('search/userList',array('p'=>-2));?>')");
            }
        }
        else
        {
            eval("$('#"+searchParamsPreFix+'_value'+index+"').autocompleter('<?php echo Yii::app()->createUrl('search/userList');?>')");
        }      
    }
    else if(fieldType == 'multipeople')
    {
        eval("$('#"+searchParamsPreFix+'_value'+index+"').autocompleter('<?php echo Yii::app()->createUrl('search/userList');?>')");
    }
    if(true == isKeepOldValue)
    {
        $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
    }
    
}

function updateQueryOperator(index,isKeepOldValue)
{
    var $oldOperatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
    var $fieldName = $('#'+searchParamsPreFix+'_field'+index).val();
    eval('var fieldType='+'field_'+$fieldName+'_type');
    var $fieldName = $('#'+searchParamsPreFix+'_field'+index).val();
    eval('var fieldOperatorSelect='+'field_'+fieldType+'_operator');
    var $operatorValue = replaceTemplateWithIndex(fieldOperatorSelect,index);
    $('#'+searchParamsPreFix+'_operator'+index).replaceWith($operatorValue);
    if(true == isKeepOldValue)
    {
        $('#'+searchParamsPreFix+'_operator'+index).attr('value',$oldOperatorValue);
    }
}

function isDateNumber(str){
    var reg = /^-?[1-9]\d*$|^0$|^(19|20)\d{2}-(0?\d|1[012])-(0?\d|[12]\d|3[01])$/
    if(reg.test(str))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validateDateFormat()
{
    var $fieldArr = $("select[id*="+searchParamsPreFix+"_field]");
    var resultStr = '';
    $fieldArr.each(function(){
        var $selectedValue = $(this).val();
        eval('var fieldType='+'field_'+$selectedValue+'_type');
        var fieldId = $(this).attr('id');
        var filedPrefix = searchParamsPreFix+'_field';
        var indexNum = fieldId.substr(filedPrefix.length,fieldId.length);
        if('date' == fieldType)
        {
            var dateValue = $('#'+searchParamsPreFix+'_value'+indexNum).val();
            if('' != dateValue)
            {
                if(!isDateNumber(dateValue))
                {
                    resultStr = 'failed';
                    return false;
                }
            }
         }
    });
    return resultStr;
}

function updateQueryRow(index,isKeepOldValue)
{
    updateQueryOperator(index,isKeepOldValue);
    updateQueryValue(index,isKeepOldValue);
}
function validateParentheses()
{
    var stack = new Array();
    var $parenthesesArr = $("select[id*=ParenthesesName]");
    $parenthesesArr.css('color','black');
    $parenthesesArr.each(function(){
        var $selectedValue = $(this).find("option:selected").text();
        $selectedValue = jQuery.trim($selectedValue);
        if('' != $selectedValue)
        {
            if(stack.length == 0)
            {
                stack.push($(this));
            }
            else
            {
                if('(' == $selectedValue)
                {
                    stack.push($(this));
                }
                else
                {
                    var $preObj = stack.pop();
                    if('(' != $preObj.find("option:selected").text())
                    {
                        stack.push($preObj);
                        stack.push($(this));
                    }
                }
            }

        }
    });
    if(stack.length>0)
    {
        $("#SaveQuery").attr("disabled","disabled");
        $("#SaveQuery").css('color','grey');
        $("#SaveQuery").css('cursor','default');

        $("#PostQuery").attr("disabled","disabled");
        $("#PostQuery").css('color','grey');
        $("#PostQuery").css('cursor','default');
    }
    else
    {
        $("#PostQuery").removeAttr("disabled");
        $("#PostQuery").css('color','#000000');
        $("#PostQuery").css('cursor','pointer');
        $("#SaveQuery").removeAttr("disabled");
        $("#SaveQuery").css('color','#000000');
        $("#SaveQuery").css('cursor','pointer');
    }
    for(var i=0;i<stack.length;i++)
    {
        stack[i].css('color','red');
    }

}

function setSearchConditionOrder()
{
    var rowOrder = "";
    var $searchRows = $("tr[id^=SearchConditionRow]");
    $searchRows.each(function(){
        rowOrder += $(this).attr("id")+",";
    });
    $("#"+searchParamsPreFix+"_QueryRowOrder").attr("value",rowOrder);
}

function addSearchField(fieldRowNum)
{
    var $newSearchRow = replaceTemplateWithIndex(searchConditionTmp,searchConditionRowNum);
    $("#SearchConditionRow"+fieldRowNum).after($($newSearchRow));
    var $rowNum = $(".SearchConditionRow").length;
    if($rowNum >=limitedFieldCount )
    {
        $('.add_search_button').hide();
    }
    if($rowNum >1 )
    {
        $('.cancel_search_button').show();
    }
    searchConditionRowNum++;
    setSearchHeight();
}

function replaceTemplateWithIndex($templateStr,$index)
{
    raRegExp = new RegExp(templateFieldNumber,"g");
    return $templateStr.replace(raRegExp,$index);
}


function removeSearchField(fieldRowNum)
{
    $("#SearchConditionRow"+fieldRowNum).remove();
    validateParentheses();
    var $rowNum = $(".SearchConditionRow").length;
    if($rowNum <2 )
    {
        $('.cancel_search_button').hide();
    }
    if($rowNum <limitedFieldCount)
    {
        $('.add_search_button').show();
    }
    setSearchHeight();
}
function saveQuery($title)
{   
    $title = jQuery.trim($title);
    $titleLength = $title.length;
    if($title == 'Assigned To Me'||$title=='Opended By Me'||$title=='Mailed To Me'||
        $title=='Marked by me'||$title=='指派给我'||$title=='抄送给我'||
        $title=='由我创建'||$title=='我的标记')
    {
        alert('<?php echo Yii::t("Common","title name used by system, please use another one"); ?>');
        return;
    }
    if($titleLength<1 || $titleLength>20)
    {
        alert('<?php echo Yii::t("Common","title length must between 1~20"); ?>');
        return;
    }

    $queryTitleExisted = false;
    $('a.user_query_a').each(function(){
        $text = $(this).text();
        if($text == $title)
        {
            $queryTitleExisted = true;
            return false;
        }
        else
        {
            return true;
        }       
    });


    if($queryTitleExisted)
    {
        if(!confirm('<?php echo Yii::t("Common","sure to update query");?>'+
            ' '+$title+'?'))
        {
            return;
        }
    }
    $('#queryTitle').attr('value',$title);
    $('#saveQuery').attr('value','1');
    $("#save_query_dialog").dialog("close");
    submitSearchForm();
    return false;
}

function markQueryTitle()
{
    var $queryTitle = $('#queryTitle').attr('value');
    $('div.querydiv a.user_query_a').each(function(){
        $text = $(this).text();
        if($text == $queryTitle)
        {
            $(this).css('color','#115FA2');
            $(this).css('font-weight','bold');
            return ;
        }
    });

    $('div.querydiv a.basic').each(function(){
        $text = $(this).attr('title');
        if($text == $queryTitle)
        {
            $(this).css('color','#115FA2');
            $(this).css('font-weight','bold');
            return ;
        }
    });
}

function submitSearchForm()
{
    if('' != validateDateFormat())
    {
        alert(dateFormatError);
        return false;
    }
    document.SearchBug.submit();
}
$(function(){
    markQueryTitle();
    $rowNum = $(".SearchConditionRow").length;
    searchConditionRowNum = $rowNum;
    $('#SearchBug').submit(function(){
        setSearchConditionOrder();
        submitSearchForm();
    })
    for(var i=1;i<$rowNum;i++)
    {
        updateQueryRow(i,true);
    }
})
</script>


<form method="post" action="<?php echo Yii::app()->createUrl('info/index',array('type'=>$infoType,'product_id'=>$productId));?>" name="SearchBug" id="SearchBug">
<input type="hidden" value="1" id="queryaction" name="queryaction">
<input type="hidden" value="<?php echo $queryTitle;?>" id="queryTitle" name="queryTitle" >
<input type="hidden" value="0" id="saveQuery" name="saveQuery">
<input type="hidden" value="0" id="reset" name="reset">
<input type="hidden" value="" id="showField" name="showField">
   <div id="SearchBlankCover" style="background-color: #F0F0F0;">
   <table id="searchtable" style="background-color: #F0F0F0;">
    <caption style="padding-bottom: 1px; padding-top: 3px;text-align: center;font-weight: bold;font-size: 13px;"><?php echo Yii::t('Common','Query Builder'); if('' != $queryTitle){echo ' - '.CHtml::encode($queryTitle);}?></caption>
        <colgroup>
          <col width="6%" span="1">
          <col width="20%" span="1">
          <col width="20%" span="1">
          <col width="30%" span="1">
          <col width="6%" span="1">
          <col width="8%" span="1">
          <col width="10%" span="1">
        </colgroup>
        <tbody>
            <?php echo $searchConditionHtml; ?>
        <tr>
        <td colspan="7">
        <center>
            <input type="button" onclick="setSearchConditionOrder();submitSearchForm();" value="<?php echo Yii::t('Common','Post Query');?>" id="PostQuery" name="PostQuery" class="btn">
            <input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;" value="<?php echo Yii::t('Common','Save Query');?>" id="SaveQuery" name="SaveQuery" class="btn">
            <input type="button" onclick="$('#reset').attr('value',1);submitSearchForm();" value="<?php echo Yii::t('Common','Reset Query');?>" class="btn">
        </center>
        </td>
    </tr>
  </tbody></table>
  <input type="hidden" value="" name="<?php echo Info::QUERY_GROUP_NAME; ?>[queryRowOrder]" id="<?php echo Info::QUERY_GROUP_NAME; ?>_QueryRowOrder">
  </div>
</form>

<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'save_query_dialog',
        // additional javascript options for the dialog plugin
        'options'=>array(
            'title'=>Yii::t('Common','Query Title'),
            'autoOpen'=>false,
            'width' => '300px',
            'modal' => true,
            'height' => 'auto',
            'resizable' => false
        )
    ));

    echo '<table class="dialog-table" >
      <tbody>
      <tr>
        <td style="text-align:center" >
        <input type="text" maxlength=20 id="dialogQueryTitle" value="'.
        $queryTitle.'"/><br/><br/></td>
      </tr>
      <tr>
        <td style="text-align:center" >'.
        CHtml::button(Yii::t('Common','Save Query'), array('onclick'=>'saveQuery($("#dialogQueryTitle").val());')).
        '</td>
      </tr>
    </tbody></table>';
    $this->endWidget('zii.widgets.jui.CJuiDialog');
?>