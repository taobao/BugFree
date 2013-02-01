<script type="text/javascript">
    window.onbeforeunload = confirmWhenExit;
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bug-info-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <?php echo $form->hiddenField($model,'deleted_file_id',array('value'=>'','class'=>'deleted_file_id_class')); ?>
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        echo $form->hiddenField($model,'product_id');
        ?>
        <?php echo CHtml::hiddenField('isPageDirty'); ?>
        <?php echo CHtml::hiddenField('templateTitle'); ?>
        <div>
        <div style="float: left;">
        <div class="row">
		<?php echo $form->label($model,'title',array('style'=>'padding-left:5px;')); ?>
                <?php
                    if(isset($model->bug_status))
                    {
                        echo '<span class="bugstatus_'.  strtolower($model->bug_status).'">&nbsp;</span>';
                    }
                    else
                    {
                        echo '<span class="bugstatus_active">&nbsp;</span>';
                    }
                ?>
		<?php echo $form->textField($model,'title',array('style'=>'width:580px;',
                    'maxlength'=>255,'class'=>'required')); ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'productmodule_id',array('style'=>'padding-left:5px;')); ?>
                <span class="bugstatus_closed">&nbsp;</span>
                <?php
                    echo InfoService::getModuleSelect($model->product_id,$model->productmodule_id,$infoType);
                ?>
	</div>
        </div>
        <div class="info_id">
            <span id="span_info_id">
                <?php 
                if(!empty($model->id))
                {
                    echo $model->id; 
                }
                else
                {
                    echo Yii::t('Common','New').ucfirst($infoType);
                }
                ?>
            </span>
        </div>
        </div>
        <div style="clear:both;">
            <fieldset style="width: 32%;float: left;">
                <legend><?php echo Yii::t('FieldConfig','bug_status'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'bug_status'); ?>
                    <?php echo CHtml::encode($model->bug_status); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'assign_to_name'); ?>
                    <?php

                    if(BugInfo::STATUS_CLOSED != $model->bug_status)
                    {
                        $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                            'model' => $model,
                            'htmlOptions' => array(
                                'class' => 'info_input required'
                            ),
                            'attribute' => 'assign_to_name',
                            'urlOrData' => TestUser::getSearchUserUrl(TestUser::USER_TYPE_ACTIVE)
                        ));
                    }
                    else
                    {
                        $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                            'model' => $model,
                            'htmlOptions' => array(
                                'class' => 'info_input required'
                            ),
                            'attribute' => 'assign_to_name',
                            'urlOrData' => TestUser::getSearchUserUrl(TestUser::USER_TYPE_CLOSE)
                        ));
                    }
                    
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'mail_to'); ?>
                    <?php $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                            'model' => $model,
                            'htmlOptions' => array(
                                'class' => 'info_input'
                            ),
                            'attribute' => 'mail_to',
                            'config' => '{multiple:true}',
                            'urlOrData' => TestUser::getSearchUserUrl()
                        ));?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'severity'); ?>
                    <?php
                        echo $form->dropDownList($model,'severity',
                         ProductService::getBugSeverityOption($model['product_id']),
                            array('class'=>'info_input required',
                                'style'=>'width:190px;'));
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'priority'); ?>
                    <?php
                        echo $form->dropDownList($model,'priority',
                         ProductService::getBugPriorityOption($model['product_id']),
                            array('class'=>'info_input',
                                'style'=>'width:190px;'));
                    ?>
                </div>
                <?php echo empty($customfield['bug_status'])?'':$customfield['bug_status'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'updated_by'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->updated_by)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'updated_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->updated_at)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'reopen_count'); ?>
                    <?php echo CHtml::encode($model->reopen_count); ?>
                </div>
            </fieldset>
            <div style="float: left;width: 33%">
            <fieldset>
                <legend><?php echo Yii::t('Common','Open'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'created_by'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->created_by)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'created_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->created_at)); ?>
                </div>                
                <?php echo empty($customfield['bug_open'])?'':$customfield['bug_open'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','bug_resolve'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'resolved_by'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->resolved_by)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'resolved_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->resolved_at)); ?>
                </div>
                <?php echo empty($customfield['bug_resolve'])?'':$customfield['bug_resolve'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'solution'); ?>
                    <?php
                    if(BugInfo::STATUS_ACTIVE != $model->bug_status)
                    {
                        echo $form->dropDownList($model,'solution',
                        InfoService::getBugSolutionOptions($model->product_id),
                        array('onchange'=>'if("'.BugInfo::DUPLICATE_SOLUTION.
                            '" == $(this).val()){$("#BugInfoView_duplicate_id").show();$("#BugInfoView_duplicate_id").focus();}else{$("#BugInfoView_duplicate_id").hide();}',
                            'class'=>'info_input required',
                            'style'=>'width:190px;'));
                    }
                    else
                    {
                        echo CHtml::encode($model->solution);
                    }
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'duplicate_id'); ?>
                    <?php
                        if(BugInfo::STATUS_ACTIVE != $model->bug_status)
                        {
                            $displayStyle = 'display:none;';
                            if(BugInfo::DUPLICATE_SOLUTION == $model->solution)
                            {
                                $displayStyle = '';
                            }
                            echo $form->textField($model,'duplicate_id',array('class'=>'required info_input','style'=>$displayStyle));
                        }
                        else
                        {
                            echo CHtml::encode($model->duplicate_id);
                        }
                        
                    ?>
                </div>
                
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','bug_close'); ?></legend>
                <?php echo empty($customfield['bug_close'])?'':$customfield['bug_close'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'closed_by'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->closed_by)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'closed_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->closed_at)); ?>
                </div>
            </fieldset>
            </div>
            <div style="float:right;width: 33%;">
                <fieldset>
                <legend><?php echo Yii::t('Common','Other Info'); ?></legend>
                <?php echo empty($customfield['bug_other'])?'':$customfield['bug_other'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','bug_related'); ?></legend>
                <?php echo empty($customfield['bug_related'])?'':$customfield['bug_related'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'related_bug'); ?>
                    <?php echo $form->textField($model,'related_bug',array('maxlength'=>255,'class'=>'info_input')); ?>
                </div>
                <?php
                $isShowCaseResultTab = Yii::app()->params['showCaseResultTab'];
                if($isShowCaseResultTab)
                {
                    $caseResultStr = '<div class="row">' .
                            $form->label($model, 'related_case') .
                            $form->textField($model, 'related_case',
                                    array('maxlength' => 255, 'class' => 'info_input')) . '</div>';
                    $caseResultStr .= '<div class="row">' .
                            $form->label($model, 'related_result') .
                            $form->hiddenField($model, 'related_result') .
                            InfoService::getRelatedIdHtml('result', $model->related_result) .
                            '</div>';
                    echo $caseResultStr;
                }
                ?>
            </fieldset>

            <fieldset>
                <legend>
                    <?php
                        echo Yii::t('Common','Attachments').'('.TestFileService::getFileSize(CommonService::getMaxFileSize()).')';
                    ?>
                </legend>
                <?php echo $model->attachment_file; ?>
                <?php
                    $this->widget('CMultiFileUpload', array(
                     'model'=>$model,
                     'name' => 'attachment_file',
                     'accept'=>'',
                     'htmlOptions'=>array('class'=>'info_input'),
                     'remove'=>'<img src="'.Yii::app()->theme->baseUrl.'/assets/images/deletefile.gif" alt="remove" />',
                     'options'=>array(
                     )
                  ));
                ?>
             </fieldset>

            </div>
            </div>
             <div style="clear:both;">
                <fieldset class="action_note" style="width:460px;">
                    <legend><?php echo Yii::t('Common','Comment'); ?></legend>
                    <div class="row" style="overflow: auto;padding: 2px;">
                        <?php
                        $this->widget('application.extensions.kindeditor4.KindEditorWidget',
                                array('model' => $model,'attribute' => 'action_note',
                                    'htmlOptions'=>array('style' => 'width:100px;'),
                                    'miniMode'=>true,'editorname'=>'action_note_editor'));
                        ?>
                    </div>
                    <?php echo ActionHistoryService::getActionHistory('bug', $model->id, $model->product_id); ?>
                </fieldset>
                <fieldset style="width: 460px;float: right;">
                    <legend><?php echo Yii::t('BugInfo','Repro Steps'); ?></legend>
                    <div class="row" style="overflow: auto;">
                        <?php
                        $this->widget('application.extensions.kindeditor4.KindEditorWidget',
                                array('model' => $model,'attribute' => 'repeat_step',
                                    'htmlOptions'=>array('style' => 'width:100px;'),
                                    'editorname'=>'repeat_step_editor'));
                        ?>
                    </div>
                </fieldset>
             </div>
<?php $this->endWidget(); ?>

</div><!-- form -->
