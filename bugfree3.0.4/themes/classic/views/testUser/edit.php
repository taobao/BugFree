<script type="text/javascript">
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
        if('1' == $('#TestUser_email_flag').val())
        {
            if('' == $.trim($('#TestUser_email').val()))
            {
                alert('<?php echo Yii::t('TestUser','Email can not be empty'); ?>');
                return false;
            }
        }
        if('1' == $('#TestUser_wangwang_flag').val())
        {
            if('' == $.trim($('#TestUser_wangwang').val()))
            {
                alert('<?php echo Yii::t('TestUser','Wangwang can not be empty'); ?>');
                return false;
            }
        }
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
                $('#TestUser_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            var $returnUrl = '<?php echo Yii::app()->createUrl('testUser/edit',array('id'=>$model->id));?>';
            showSuccessMsg(responseText.detail,$returnUrl);       
        }
    }

    function checkPasswordFieldSet($checkFlag)
    {
        
        if($checkFlag != undefined)
        {
            $('#TestUser_change_password').attr('value','1');
            $('.password_fieldset').removeAttr('disabled');
            $('.password_fieldset').removeClass('disabled');
        }
        else
        {
            $('#TestUser_change_password').attr('value','0');
            $('.password_fieldset').attr('disabled','disabled');
            $('.password_fieldset').addClass('disabled');
        }
    }
</script>
<div class="form administration">

    <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'test-user-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr />
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        echo $form->hiddenField($model,'realname',array('value'=>$model->realname));
        ?>
    <div class="row">
        <?php echo $form->label($model, 'username'); ?>
        <?php echo CHtml::encode($model->username);  ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'realname'); ?>
        <?php echo CHtml::encode($model->realname);  ?>
    </div>
    <div class="row">
        <?php echo $form->label($model, 'email'); ?>
        <?php
           echo $form->textField($model, 'email', array('size' => 45,
               'maxlength' => 45));
        ?>
    </div>
    
    <?php
        if(TestUser::$Authmode['internal'] == $model->authmode)
        {
            echo $form->hiddenField($model,'change_password');
            echo '<div class="row"><fieldset style="width:400px;"><legend>';
            echo CHtml::checkBox('change_password','',array('onclick'=>'checkPasswordFieldSet($(this).attr(\'checked\'))'));
            echo Yii::t('TestUser','Change Password').'</legend><div class="row">';
            echo $form->labelEx($model,'password_old');
            echo $form->passwordField($model,'password_old',array('size'=>20,
                'maxlength'=>45,'disabled'=>'disabled','class'=>'password_fieldset disabled'));
            echo ' </div><div class="row">';
            echo $form->labelEx($model, 'password');
            echo $form->passwordField($model, 'password', array('size' => 20,
                'maxlength' => 45,'disabled'=>'disabled','class'=>'password_fieldset disabled'));
            echo ' </div><div class="row">';
            echo $form->label($model, 'password_repeat');
            echo $form->passwordField($model, 'password_repeat', array('size' => 20,
                'maxlength' => 45,'disabled'=>'disabled','class'=>'password_fieldset disabled'));
            echo '</div></fieldset></div>';
        }
    ?>

    <div class="row">
        <?php echo $form->label($model, 'email_flag'); ?>
        <?php echo $form->dropDownList($model, 'email_flag', CommonService::getTrueFalseOptions(),array('class'=>'required')); ?>
    </div>

        <div style="clear:both;"></div>
        <hr/>
	<?php echo CHtml::submitButton(Yii::t('Common','Save'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div>