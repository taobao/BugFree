<script type="text/javascript">
    var $returnUrl = '<?php echo Yii::app()->createUrl('testUser/index');?>';
    $(document).ready(function() {
        setInternalInfo($('#TestUser_authmode').val())
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
                $('#FieldConfig_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            showSuccessMsg(responseText.detail+'|<a href="'+$returnUrl+'"><?php echo Yii::t('TestUser','Back To User List');?></a>',$returnUrl); 
        }
    }

    function checkPasswordFieldSet($checkFlag)
    {
        
        if($checkFlag != undefined)
        {
            $('.password_fieldset').removeAttr('disabled');
            $('.password_fieldset').removeClass('disabled');
        }
        else
        {
            $('.password_fieldset').attr('disabled','disabled');
            $('.password_fieldset').addClass('disabled');
        }
    }
    function setInternalInfo($authmode)
    {
        if('ldap' == $authmode)
        {
            $('#internal_info').hide();
        }
        else
        {
            $('#internal_info').show();
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
    ?>
    <div class="row">
        <?php
        if($model->isNewRecord)
        {
            echo $form->label($model, 'authmode');
            echo $form->dropDownList($model, 'authmode',
                    TestUserService::getAuthModeOptions(),
                    array('onchange'=>'setInternalInfo($(this).val())',
                        'class'=>'required'));
        }
        else
        {
            echo $form->label($model, 'authmode');
            echo CHtml::encode(TestUserService::getModeMessage($model->authmode));
        }
        ?>
    </div>

    <div class="row">
        
        <?php
        if($model->isNewRecord)
        {
            echo $form->label($model, 'username');
            echo $form->textField($model, 'username', array('size' => 45,
                'maxlength' => 45,'class'=>'required'));
        }
        else
        {
            echo $form->label($model, 'username');
            echo CHtml::encode($model->username);
        }
        ?>
    </div>
    <div id="internal_info">
    <div class="row">
        <?php echo $form->label($model, 'realname'); ?>
        <?php echo $form->textField($model, 'realname', array('size' => 45,
            'maxlength' => 45,'class'=>'required')).
                Yii::t('TestUser','Please input a display name assigned to');?>
    </div>
    <div class="row">
        <?php echo $form->label($model, 'password'); ?>
        <?php
            if($model->isNewRecord)
            {
                echo $form->textField($model, 'password', array('size' => 45,
               'maxlength' => 45,'class'=>'required')).
                        Yii::t('TestUser','Please input a default password');
            }
            else
            {
                echo $form->textField($model, 'password', array('size' => 45,
               'maxlength' => 45)).Yii::t('TestUser','Password no change if empty');
            }         
        ?>
    </div>
    <div class="row">
        <?php echo $form->label($model, 'email'); ?>
        <?php
           echo $form->textField($model, 'email', array('size' => 45,
               'maxlength' => 45,'class'=>'required')).
                Yii::t('TestUser','Please input an email address to receive email notification');
        ?>
    </div>
    </div>
    <div style="clear:both;"></div>
    <hr/>
    <?php echo CHtml::submitButton(Yii::t('Common','Save'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div>