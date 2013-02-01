<script type="text/javascript">
$(function(){
        var $selectList = '<?php echo $defaultSelectFieldOptionStr; ?>';
        var $showList = '<?php echo $defaultShowFieldOptionStr; ?>';
	$('#addField').click(function() {
		$('#fieldsToSelectList option:selected').appendTo('#fieldsToShowList');
	});
	$('#deleteField').click(function() {
		$('#fieldsToShowList option:selected').appendTo('#fieldsToSelectList');
	});
	$('#addAllField').click(function() {
		$('#fieldsToSelectList option').appendTo('#fieldsToShowList');
	});
	$('#deleteAllField').click(function() {
		$('#fieldsToShowList option').appendTo('#fieldsToSelectList');
	});
        addDbClickFunction();
        $('#upButton').click(function(){
            var $selectedItems = $('#fieldsToShowList option:selected');
            $selectedItems.each(function(){
                $(this).after($(this).prev());
            })
        });

        $('#downButton').click(function(){
            var $selectedItems = $('#fieldsToShowList option:selected');
            $selectedItems.each(function(){
                $(this).before($(this).next());
            })
        })

        $('#defaultButton').click(function(){
            $('#selectFieldDiv select').replaceWith($selectList);
            $('#showFieldDiv select').replaceWith($showList);
            addDbClickFunction();
        })
});

function addDbClickFunction()
{
    $('#fieldsToSelectList').dblclick(function(){
            $("option:selected",this).appendTo('#fieldsToShowList');
    });
    $('#fieldsToShowList').dblclick(function(){
       $("option:selected",this).appendTo('#fieldsToSelectList');
    });
}

function setSelectField()
{
    var $selectedFields = $('#fieldsToShowList option');
    var $selectFieldStr = '';
    $selectedFields.each(function(){
        $selectFieldStr += $(this).val() +',';
    });
    if('' == $selectFieldStr)
    {
        alert('<?php echo Yii::t('Common','Show field can not be empty'); ?>');
        return;
    }
    $('#showField').attr('value',$selectFieldStr);
    $('#select_show_field_dialog').dialog('close');
    setSearchConditionOrder();
    document.SearchBug.submit();
}
</script>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'select_show_field_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>Yii::t('Common','Custom Fields'),
        'autoOpen'=>false,
        'modal' => true,
        'width' => '600px',
        'height' => 'auto',
        'resizable' => false
    )
));

echo '<table class="dialog-table" id="CustomSetTable">
      <tbody>
      <tr align="center" valign="middle" class="BgRow">
        <td align="left" width="5%">&nbsp;</td>
        <td width="20%">
          <fieldset style="border: 0pt none; font-size: 12px;">
            <legend>'.Yii::t('Common','Available Fields').'</legend><div id="selectFieldDiv">'.
            $selectFieldOptionStr.
          '</div></fieldset>
        </td>
        <td width="10%">
            <input style="width:50px" type="button" id="addAllField" name="addAllField" value=">>|"><br>
            <input style="width:50px" type="button" id="addField" name="addField"  value=">>"><br>
            <input style="width:50px" type="button" id="deleteField" name="deleteField" value="<<"><br>
            <input style="width:50px" type="button" id="deleteAllField" name="deleteAllField" value="|<<">
        </td>
        <td width="20%">
          <fieldset style="border: 0pt none; font-size: 12px;">
          <legend>'.Yii::t('Common','Display Fields').'</legend><div id="showFieldDiv">'.
          $showFieldOptionStr.
          '</div></fieldset>
        </td>
        <td align="left" width="8%">
          <input type="button" id="upButton" name="upButton"  style="width: 30px;" value="↑"><br>
          <input type="button" id="downButton" name="downButton"  style="width: 30px;" value="↓">
        </td>
      </tr>
      <tr>
        <td style="text-align:center" colspan="5">
          <input type="button" style="width:120px" onclick="setSelectField();"  value="'.Yii::t('Common','OK').'">
          <input type="button" style="width:120px" id="defaultButton" name="defaultButton"  value="'.Yii::t('Common','Default Fields').'"><br>
        </td>
      </tr>
    </tbody></table>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>