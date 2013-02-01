<script type="text/javascript">
    var $returnUrl = '<?php echo Yii::app()->createUrl('testOption/index');?>';
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
                $('#TestOption_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            showSuccessMsg(responseText.detail,$returnUrl);
        }
    }
 </script>
<div class="form administration">

    <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-group-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr />
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        ?>
	<div class="row">
		<?php echo $form->label($model,'option_name'); ?>
		<?php
                if(!$model->isNewRecord)
                {
                    echo CHtml::encode($model->option_name);
                }
                else
                {
                    echo $form->textField($model,'option_name',array('size'=>45,
                    'maxlength'=>45,'class'=>'required'));
                }
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'option_value'); ?>
		<?php echo $form->textArea($model,'option_value',
                        array('rows'=>6, 'cols'=>50,'class'=>'required')); ?>
	</div>
        <div style="clear:both;"></div>
        <hr/>
	<?php echo CHtml::submitButton(Yii::t('Common','Save'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div>