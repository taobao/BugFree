<script type="text/javascript">
    $needToConfirm = true;
    var $oldValueArr = new Array();
    var $infoType = '<?php echo $infoType; ?>';
    var $baseUrl = '<?php echo Yii::app()->getBaseUrl(); ?>'
    var $modelName = '<?php echo ucfirst($infoType) . 'InfoView' ?>';
    var pageDirtyValue = <?php echo $isPageDirty; ?>;
    var actionType = '<?php echo $actionType; ?>';
    var productId = <?php echo $model->product_id; ?>;
    function confirmWhenExit()
    {
        if($needToConfirm == true)
        {
            try{
                repeat_step_editor.sync();
            }catch(e){};
            action_note_editor.sync();
            $newValueArr = getFormValue();
            if(isPageDirty($oldValueArr,$newValueArr))
            {
                return '<?php echo Yii::t('Common', 'If you leave this page, all changes will be lost'); ?>';
            }
        }
    }
    function getChildModule($parentId)
    {
        $productModuleId = $('select.product_module').attr('id');
        jQuery.get("<?php echo Yii::app()->createUrl('search/getChildModuleSelect'); ?>", {'parent_id':$parentId,'type':$infoType}, function (data, textStatus){
            if('success' == textStatus)
            {
                $('#'+$productModuleId).replaceWith(data);
                setAssignTo($infoType);
            }
        });
    }
    function submitForm()
    {
        $('div#buttonlist input.btn').attr('disabled','disabled');
        try{
            repeat_step_editor.sync();
        }catch(e){};
        action_note_editor.sync();
        if(1 == pageDirtyValue)
        {
            $('#isPageDirty').val(1);
        }
        else
        {
            $newValueArr = getFormValue();
            if(isPageDirty($oldValueArr,$newValueArr))
            {
                $('#isPageDirty').val(1);
            }
        }
        document.forms[0].submit();
    }

    function saveTemplate($title)
    {
        $title = jQuery.trim($title);
        $titleLength = $title.length;
        if($titleLength<1 || $titleLength>20)
        {
            alert('<?php echo Yii::t("Common", "title length must between 1~20"); ?>');
            return;
        }
        jQuery.get("<?php echo Yii::app()->createUrl('search/checkTemplate'); ?>", {'title':$title,'type':$infoType,'product_id':productId}, function (data, textStatus){
            if('success' == textStatus)
            {
                if(('' != data)&&(!confirm('<?php echo Yii::t("Common", "sure to update template"); ?>'+' '+$title+'?')))
                {
                    return false;
                }
                else
                {
                    $('#templateTitle').attr('value',$title);
                    $("#template_dialog").dialog("close");
                    document.forms[0].submit();
                }

            }
        });
        return false;
    }
    function deleteFile($id)
    {
        if(!confirm('<?php echo Yii::t('Common', 'Sure to delete?'); ?>'))
        {
            return;
        }
        else
        {
            $('#'+$modelName+'_deleted_file_id').val($('#'+$modelName+'_deleted_file_id').val()+','+$id);
            $('#file'+$id).remove();
        }
    }
    function isPageDirty($oldValueArr,$newValueArr)
    {
        if('' != $('.deleted_file_id_class').val())
        {
            return true;
        }
        for(var $id in $newValueArr)
        {
            if(($id != undefined)&&($id != 'undefined') &&($newValueArr[$id] != undefined) &&
                ($oldValueArr[$id] != undefined) && ($newValueArr[$id] != $oldValueArr[$id]))
            {
                return true;
            }
        }
        return false;
    }

    function getFormValue()
    {
        $returnValueArr = new Array();
        var $multiSelectElements = $('a.multiSelect');
        $multiSelectElements.each(function(){
            $id = $(this).attr('id');
            $value = $(this).find('span').text();
            $returnValueArr[$id] = $value;
        })
        var $pageInputElements = $('form :input');
        $pageInputElements.each(function(){
            $type = $(this).attr('type');
            $id = $(this).attr('id');
            $value = $(this).val();
            if('hidden' == $type)
            {
                $hiddenId = $(this).attr('id');
                if('ResultInfoView_result_step' == $hiddenId)
                {
                    $returnValueArr[$id] = $(this).val();
                }
            }
            else if('checkbox' == $type)
            {
                $name = $(this).attr('name');
                if('checked' == $(this).attr('checked'))
                {
                    if($returnValueArr[$name] == undefined)
                    {
                        $returnValueArr[$name] = $value;
                    }
                    else
                    {
                        $returnValueArr[$name] += ','+$value;
                    }
                }

            }
            else
            {
                $returnValueArr[$id] = $(this).val();
            }
        });
        return $returnValueArr;
    }
    function setAssignTo($type)
    {
        if('result' == $type)
        {
            return;
        }
        $idPrefix = '';
        if('bug' == $type)
        {
            $idPrefix = 'BugInfoView_';
        }
        else if('case' == $type)
        {
            $idPrefix = 'CaseInfoView_';
        }
        $moduleId = $('#'+$idPrefix+'productmodule_id').val();
        jQuery.get("<?php echo Yii::app()->createUrl('search/getModuleOwner'); ?>", {'module_id':$moduleId}, function (data, textStatus){
            if('success' == textStatus)
            {
                if('' != data)
                {
                    $('#'+$idPrefix+'assign_to_name').val(data);
                }
            }
        });
    }

    function setDefaultCursor()
    {
        $('div#buttonlist .btn').each(function(){
            if('disabled' == $(this).attr('disabled'))
            {
                $(this).css('cursor','default');
            }
        })
    }

    function maxLeft()
    {
        $('#fieldset_step').hide();
        $('#fieldset_comment').width('944px');
        $('#fieldset_comment dd').width('920px');
        $('#fieldset_comment dd blockquote').width('920px');
        $('#comment_view_button_left img.fullscreen').attr('src','<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/arrow_left.gif');
    }
    function normalLeft()
    {
        $('#fieldset_step').show();
        $('#fieldset_comment').width('460px');
        $('#fieldset_comment dd').width('435px');
        $('#fieldset_comment dd blockquote').width('435px');
        $('#comment_view_button_left img.fullscreen').attr('src','<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/arrow_right.gif');
    }
    function maxRight()
    {
        $('#fieldset_comment').hide();
        $('#fieldset_step').width('944px');
        $('#fieldset_step div.row').width('920px');
        $('#comment_view_button_right img.fullscreen').attr('src','<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/arrow_right.gif');
    }
    function normalRight()
    {
        $('#fieldset_comment').show();
        $('#fieldset_step').width('460px');
        $('#fieldset_step div.row').width('460px');
        $('#comment_view_button_right img.fullscreen').attr('src','<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/arrow_left.gif');
    }

    window.onload = function()
    {
        setDefaultCursor();
        $oldValueArr = getFormValue();
        initShowGotoBCR();
    }

    $(function(){
        $('#fieldset_comment').hover(function(){$('#comment_view_button_left img').show();}, function(){$('#comment_view_button_left img').hide();});
        $('#fieldset_step').hover(function(){$('#comment_view_button_right img').show();}, function(){$('#comment_view_button_right img').hide();});
        $('#comment_view_button_left').toggle(maxLeft, normalLeft);
        $('#comment_view_button_right').toggle(maxRight, normalRight);
        if('view' == actionType)
        {
            jQuery.get("<?php echo Yii::app()->createUrl('search/getPreNextId'); ?>",
            {'id':<?php echo isset($model->id) ? $model->id : 0; ?>,'type':$infoType,'product_id':productId}, function (data, textStatus){
                if('success' == textStatus)
                {
                    $('span#preNextSpan').html(data);
                }
            });
        }
    })
</script>
<?php
$color = 'blue';
$type = 'case';
if(isset($_GET['type']))
{
    $type = $_GET['type'];

    if('case' == $type)
    {
        $color = 'green';
    }
    elseif('result' == $type)
    {
        $color = 'orange';
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/<?php echo $color; ?>.css" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_<?php echo $color; ?>.ico" type="image/x-icon" />
<title><?php echo empty($model->title) ? Yii::t('Common', 'New') . ucfirst($infoType) : ucfirst($infoType) . ' #' . $model->id . ' ' . $model->title; ?></title>
<?php $this->beginContent('//layouts/main'); ?>
<div id="edit-top-bar" >
    <div id="logo">
        <a href="<?php echo Yii::app()->createUrl('info/index', array('type' => $type, 'product_id' => $model->product_id)); ?>">
            <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/' . $color . '/logo.png'; ?>" alt="BugFree" title="BugFree"/>
        </a>
    </div>
    <div id="buttonlist">
        <?php echo $buttonList; ?>
    </div>
</div>

<?php
        if(Yii::app()->user->hasFlash('successMessage'))
        {
            echo '<div class="flash-success">' . Yii::app()->user->getFlash('successMessage') . '</div>';
        }
?>

<?php
        if(Yii::app()->user->hasFlash('failMessage'))
        {
            echo '<div class="flash-error">' . Yii::app()->user->getFlash('failMessage') . '</div>';
        }
?>

        <div id="editmain">
    <?php
        $renderViewName = '_' . $infoType;
        if('view' == $actionType)
        {
            $renderViewName .= '_view';
        }
        else
        {
            $renderViewName .= '_form';
        }
        echo $this->renderPartial($renderViewName,
                array(
                    'model' => $model,
                    'actionType' => $actionType,
                    'infoType' => $infoType,
                    'customfield' => $customfield));
    ?>
    </div>
<?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'template_dialog',
            'options' => array(
                'title' => Yii::t('Common', 'Save as template'),
                'autoOpen' => false,
                'width' => '400px',
                'modal' => true,
                'height' => 'auto',
                'resizable' => false
            )
        ));

        echo '<table class="dialog-table">
                  <tbody>
                  <tr>
                    <td style="text-align:center" >' .
        Yii::t('Common', 'Template Title') . '&nbsp;&nbsp;<input type="text" maxlength=20 id="dialogTemplateTitle" value=""/><br/><br/></td>
                  </tr>
                  <tr>
                    <td style="text-align:center" >' .
        CHtml::button(Yii::t('Common', 'Save'), array('onclick' => 'saveTemplate($("#dialogTemplateTitle").val());')) .
        '</td>
                  </tr>
                </tbody></table>';
        $this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php $this->endContent(); ?>


