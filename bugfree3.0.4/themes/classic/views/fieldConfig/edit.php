<script type="text/javascript">
    var $returnUrl = '<?php echo $returnUrl;?>';
    $(document).ready(function() {
        var options = {
            dataType:'json',
            beforeSubmit:showRequest,
            success:showResponse
        };
        $('form').submit(function() {
            $(this).ajaxSubmit(options);
            return false;
        });
        showMatchExpression($('#FieldConfig_validate_rule').val());
        showResultGroup($('#FieldConfig_edit_in_result').val());
    });
    function showRequest(formData, jqForm, options) {
        $('.error').removeClass('error');
        return true;
    }
    function showResponse(responseText, statusText, xhr, $form)  {
        if('failed' == responseText.status)
        {
            var detailedArr = responseText.detail;
            var msg = '';
            for(var field in detailedArr)
            {
                msg += detailedArr[field]+'<br/>';
                $('#FieldConfig_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            showSuccessMsg(responseText.detail,$returnUrl);              
        }
    }
    function showMatchExpression(validateRule)
    {
        if('match' == validateRule)
        {
            $('#matchRow').show();
        }
        else
        {
            $('#matchRow').hide();
        }
    }

    function showResultGroup(editflag)
    {
        if('1' == editflag)
        {
            $('#resultGroupRow').show();
        }
        else
        {
            $('#resultGroupRow').hide();
        }
    }
</script>
<div class="form administration">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'field-config-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr/>
	<div class="row">
		<?php echo $form->label($model,'field_name'); ?>
                <?php
                if($model->isNewRecord)
                {
                    echo $form->textField($model,'field_name',
                            array('size'=>45,'maxlength'=>45,'class'=>'required'));
                }
                else
                {
                    echo  CHtml::encode($model->field_name);
                }
                ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'field_type'); ?>
                <?php echo $form->dropDownList($model,
                        'field_type', $model->getFieldTypes(),
                        array('class'=>'required')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'field_value'); ?>
		<?php
                echo Yii::t('FieldConfig','Can use http://... to get outer data which should like xxx,yyy...').'<br/>';
                echo $form->textArea($model,'field_value',array('rows'=>6, 'cols'=>50));
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'default_value'); ?>
		<?php echo $form->textArea($model,'default_value',array('rows'=>6, 'cols'=>50)); ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'is_required'); ?>
                <?php echo $form->checkBox($model,'is_required'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_dropped'); ?>
                <?php echo $form->dropDownList($model,'is_dropped',
                        CommonService::getTrueFalseOptions(),
                        array('class'=>'required')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'field_label'); ?>
		<?php echo $form->textField($model,'field_label',
                        array('size'=>45,'maxlength'=>45,'class'=>'required')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'belong_group'); ?>
		<?php echo $form->dropDownList($model,'belong_group',
                        $model->getFieldSets($model->type),
                        array('class'=>'required')); ?>
	</div>

        <?php
            if('bug' == $model->type)
            {
                echo '<div class="row">';
                echo $form->label($model,'edit_in_result');
                echo $form->dropDownList($model,'edit_in_result',
                        CommonService::getTrueFalseOptions(),
                        array('class'=>'required',
                            'onchange'=>'showResultGroup($(this).val())'));
                echo '</div><div class="row" id="resultGroupRow">';
                echo $form->label($model,'result_group');
                echo $form->dropDownList($model,'result_group',
                        $model->getFieldSets('result'),
                        array('class'=>'required'));
                echo '</div>';
            }

        ?>

	<div class="row">
		<?php echo $form->label($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('class'=>'required')).Yii::t('Common','Please input an integer between 0~255'); ?>
	</div>

        <div class="row">
            <?php
            if(Info::TYPE_BUG == $model->type)
            {
                echo $form->label($model,'editable_action_name');
                $this->widget('application.extensions.multiSelect.MultiSelectWidget', array(
                    'model' => $model,
                    'attribute' => 'editable_action_name',
                    'selectOptionData' => BugInfo::getActions(),
                    'htmlOptions' => array(
                        'style' => 'width:240px;',
                        'class'=>'required'
                    )
                ));
            }
            ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'validate_rule'); ?>
		<?php echo $form->dropDownList($model,
                        'validate_rule', $model->getValidationRules(),
                        array('class'=>'required','onchange'=>'showMatchExpression($(this).val())')); ?>
	</div>

	<div class="row" id="matchRow">
		<?php echo $form->labelEx($model,'match_expression'); ?>
		<?php echo $form->textField($model,'match_expression',
                        array('size'=>60,'maxlength'=>255,'class'=>'required')).'Ex. /^\d+$/ will match number'; ?>
	</div>
        <div style="clear:both;"></div>
        <hr/>
        <br/>
	<?php echo CHtml::submitButton(Yii::t('Common','Save'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div>