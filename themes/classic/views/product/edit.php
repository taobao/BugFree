<script type="text/javascript">
    var $returnUrl = '<?php echo Yii::app()->createUrl('product/index');?>';
    $(document).ready(function() {
        var options = {
            dataType:'json',
            beforeSubmit:showRequest,
            success:showResponse
        };
        $('form').submit(function() {
            case_step_template_editor.sync();
            bug_step_template_editor.sync();
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
                $('#Product_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            showSuccessMsg(responseText.detail+'|<a href="'+$returnUrl+'"><?php echo Yii::t('Product','Back To Product List');?></a>',$returnUrl);
        }
    }
</script>
<div class="form administration">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'product-form',
	'enableAjaxValidation'=>false
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr/>
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        ?>
	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255,'class'=>'required')); ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('size'=>60,
                    'maxlength'=>255,'class'=>'required')).Yii::t('Common','Please input an integer between 0~255'); ?>
	</div>


        <div class="row">
		<?php echo $form->label($model,'product_manager'); ?>
                <?php
                if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo $form->textField($model,'product_manager',array('style' => 'width:550px;',
                        'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->product_manager));
                }
                else
                {
                    $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                        'model' => $model,
                        'multiline' => true,
                        'attribute' =>'product_manager',
                        'htmlOptions' => array(
                            'style' => 'width:550px;',
                            'rows' => 4
                        ),
                        'urlOrData' => TestUser::getSearchUserUrl(),
                        'config' => '{multiple:true}'
                    ));
                }

                ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'group_name'); ?>
                <?php
                $this->widget('application.extensions.multiSelect.MultiSelectWidget', array(
                    'model' => $model,
                    'attribute' => 'group_name',
                    'selectOptionData' => UserGroupService::getAllActiveGroup(),
                    'htmlOptions' => array(
                        'style' => 'width:530px;'
                    )
                ));
                ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'bug_severity'); ?>
                <?php
                if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo $form->textField($model,'bug_severity',array('style' => 'width:550px;',
                        'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->bug_severity)).Yii::t('Product','Please contact with system admin to modify');
                }
                else
                {
                    echo $form->textField($model,'bug_severity',array('style' => 'width:550px;',
                        'class'=>'required','title'=>$model->bug_severity)).Yii::t('Product','Seperate with comma, save value is 1,2,3,4...');
                }
                ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'bug_priority'); ?>
                <?php
                if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo $form->textField($model,'bug_priority',array('style' => 'width:550px;',
                        'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->bug_priority)).Yii::t('Product','Please contact with system admin to modify');
                }
                else
                {
                    echo $form->textField($model,'bug_priority',array('style' => 'width:550px;',
                        'class'=>'required','title'=>$model->bug_priority)).Yii::t('Product','Seperate with comma, save value is 1,2,3,4...');
                }
                ?>
	</div>

        <div class="row">
		<?php echo $form->label($model,'case_priority'); ?>
                <?php
                if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo $form->textField($model,'case_priority',array('style' => 'width:550px;',
                        'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->case_priority)).Yii::t('Product','Please contact with system admin to modify');
                }
                else
                {
                    echo $form->textField($model,'case_priority',array('style' => 'width:550px;',
                        'class'=>'required','title'=>$model->case_priority)).Yii::t('Product','Seperate with comma, save value is 1,2,3,4...');
                }
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'solution_value'); ?>
                <?php
                if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo $form->textField($model,'solution_value',array('style' => 'width:550px;',
                        'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->solution_value)).Yii::t('Product','Please contact with system admin to modify');
                }
                else
                {
                    echo $form->textField($model,'solution_value',array('style' => 'width:550px;',
                        'class'=>'required','title'=>$model->solution_value));
                }
                ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'bug_step_template'); ?>
                <div style="width:560px;margin-left: 190px;">
		<?php
                $this->widget('application.extensions.kindeditor4.KindEditorWidget',
                                array('model' => $model,'attribute' => 'bug_step_template',
                                    'htmlOptions'=>array('style' => 'width:100%;'),
                                    'editorname'=>'bug_step_template_editor'));
                ?>
                </div>
	</div>
        <br/>
        <div class="row">
		<?php echo $form->label($model,'case_step_template'); ?>
                <div style="width:560px;margin-left: 190px;">
		<?php
                $this->widget('application.extensions.kindeditor4.KindEditorWidget',
                                array('model' => $model,'attribute' => 'case_step_template',
                                    'htmlOptions'=>array('style' => 'width:100%;'),
                                    'editorname'=>'case_step_template_editor'));
                ?>
                </div>
	</div>

        <div class="row">
		<?php echo $form->label($model,'is_dropped'); ?>
                <?php echo $form->dropDownList($model,'is_dropped',
                        CommonService::getTrueFalseOptions(),
                        array('class'=>'required')); ?>
	</div>
        <br/>
        <div style="clear:both;"></div>
        <hr/>
        <br/>
	<?php echo CHtml::submitButton(Yii::t('Product','Save Product'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->