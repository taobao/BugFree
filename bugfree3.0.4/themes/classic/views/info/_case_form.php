<script type="text/javascript">
    window.onbeforeunload = confirmWhenExit;
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'case-info-form',
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
		<?php echo $form->textField($model,'title',
                        array('style'=>'width:580px;','maxlength'=>255,'class'=>'required')); ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'productmodule_id',array('style'=>'padding-left:5px;')); ?>
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
                <legend><?php echo Yii::t('FieldConfig','case_status'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'case_status'); ?>
                    <?php
                        if($model->isNewRecord)
                        {
                            echo $form->hiddenField($model,'case_status');
                            echo CHtml::encode($model->case_status);
                        }
                        else
                        {
                             echo $form->dropDownList($model,'case_status',
                                $model->getStatusOption(),array('class'=>'required info_input',
                                'style'=>'width:190px;'));
                        }
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'assign_to_name'); ?>
                    <?php $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                            'model' => $model,
                            'attribute' => 'assign_to_name',
                            'htmlOptions' => array('class'=>'info_input required'),
                            'urlOrData' => TestUser::getSearchUserUrl(TestUser::USER_TYPE_ACTIVE)
                        ));?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'mail_to'); ?>
                    <?php $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                            'model' => $model,
                            'attribute' => 'mail_to',
                            'config' => '{multiple:true}',
                            'htmlOptions' => array('class'=>'info_input'),
                            'urlOrData' => TestUser::getSearchUserUrl()
                        ));?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'priority'); ?>
                    <?php
                        echo $form->dropDownList($model,'priority',
                        ProductService::getCasePriorityOption($model['product_id']),
                            array('class'=>'info_input required',
                                'style'=>'width:190px;'));
                    ?>
                </div>
                <?php echo empty($customfield['case_status'])?'':$customfield['case_status'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'updated_by'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->updated_by)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'updated_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->updated_at)); ?>
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
                <?php echo empty($customfield['case_open'])?'':$customfield['case_open'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','case_script'); ?></legend>
                <?php echo empty($customfield['case_script'])?'':$customfield['case_script'] ; ?>
            </fieldset>
            </div>
            <div style="float:right;width: 33%;">
                <fieldset>
                <legend><?php echo Yii::t('Common','Other Info'); ?></legend>
                 <div class="row">
                    <?php echo $form->label($model,'delete_flag'); ?>
                    <?php 
                    if($model->isNewRecord)
                    {
                        echo CHtml::encode(CommonService::getTrueFalseName($model->delete_flag));
                    }
                    else
                    {
                        echo $form->dropDownList($model,'delete_flag',
                            CommonService::getTrueFalseOptions(),
                            array('class'=>'required','style'=>'width:190px;'));
                    }
                    ?>
                </div>
                <?php echo empty($customfield['case_other'])?'':$customfield['case_other'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','case_related'); ?></legend>
                <?php echo empty($customfield['case_related'])?'':$customfield['case_related'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'related_bug'); ?>
                    <?php echo $form->textField($model,'related_bug',array('class'=>'info_input','maxlength'=>255)); ?>
                </div>

                <div class="row">
                    <?php echo $form->label($model,'related_case'); ?>
                    <?php echo $form->textField($model,'related_case',array('class'=>'info_input','maxlength'=>255)); ?>
                </div>

                <div class="row">
                    <?php echo $form->label($model,'related_result'); ?>
                    <?php echo InfoService::getRelatedIdHtml('result', $model->related_result) ?>
                </div>
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
                     'htmlOptions'=>array('size'=>16),
                     'remove'=>'<img src="'.Yii::app()->theme->baseUrl . '/assets/images/deletefile.gif" alt="remove" />',
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
                    <?php echo ActionHistoryService::getActionHistory('case', $model->id, $model->product_id); ?>
                </fieldset>
                <fieldset style="width: 460px;float: right;">
                    <legend><?php echo Yii::t('CaseInfo','Steps'); ?></legend>
                    <div class="row">
                        <?php
                        $this->widget('application.extensions.kindeditor4.KindEditorWidget',
                                array('model' => $model,'attribute' => 'case_step',
                                    'htmlOptions'=>array('style' => 'width:100px;'),
                                    'editorname'=>'repeat_step_editor'));
                        ?>
                    </div>
                </fieldset>


             </div>
<?php $this->endWidget(); ?>

</div><!-- form -->