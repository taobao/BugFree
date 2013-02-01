<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'case-info-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>
        <div>
        <div style="float: left;">
        <div class="row">
		<?php echo $form->label($model,'title',array('style'=>'padding-left:5px;')); ?>
                <?php echo $form->textField($model,'title',array('style'=>'width:580px;',
                    'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->title)); ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'module_name',array('style'=>'padding-left:5px;')); ?>
                <?php echo $form->textField($model,'module_name',array('style'=>'width:580px;',
                    'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->module_name)); ?>
	</div>
        </div>
        <div class="info_id"><span id="span_info_id"><?php echo $model->id; ?></span></div>
        </div>
        <div style="clear:both;">
            <fieldset style="width: 32%;float: left;">
                <legend><?php echo Yii::t('FieldConfig','case_status'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'case_status'); ?>
                    <?php echo CHtml::encode($model->case_status); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'assign_to'); ?>
                    <?php echo CHtml::encode($model->assign_to_name); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'mail_to'); ?>
                    <?php echo $form->textField($model,'mail_to',array(
            'readonly'=>'readonly','class'=>'info_input readonly_field','title'=>$model->mail_to)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'priority'); ?>
                    <?php
                    echo CHtml::encode(CommonService::getNameByValue(ProductService::getCasePriorityOption($model['product_id']), $model->priority));
                    ?>
                </div>
                <?php echo empty($customfield['case_status'])?'':$customfield['case_status'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'updated_by'); ?>
                    <?php echo CHtml::encode($model->updated_by_name); ?>
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
                    <?php echo CHtml::encode($model->created_by_name); ?>
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
                    <?php echo CHtml::encode(CommonService::getTrueFalseName($model->delete_flag)); ?>
                </div>
                <?php echo empty($customfield['case_other'])?'':$customfield['case_other'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','case_related'); ?></legend>
                <?php echo empty($customfield['case_related'])?'':$customfield['case_related'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'related_bug'); ?>
                    <?php echo InfoService::getRelatedIdHtml('bug', $model->related_bug) ?>
                </div>

                <div class="row">
                    <?php echo $form->label($model,'related_case'); ?>
                    <?php echo InfoService::getRelatedIdHtml('case', $model->related_case) ?>
                </div>

                <div class="row">
                    <?php echo $form->label($model,'related_result'); ?>
                    <?php echo InfoService::getRelatedIdHtml('result', $model->related_result) ?>
                </div>
            </fieldset>

            <fieldset>
                <legend><?php echo Yii::t('Common','Attachments'); ?></legend>
                <?php echo $model->attachment_file; ?>
             </fieldset>
            </div>
            </div>
             <div style="clear:both;">
                <fieldset id="fieldset_comment" style ="width:460px;float: left;word-break:break-all;word-wrap:break-word;">
                    <legend><?php echo Yii::t('Common','Comment'); ?></legend>
                    <?php
                    $this->renderPartial('_fullscreen',array('position'=>'left'));
                    echo ActionHistoryService::getActionHistory('case', $model->id, $model->product_id);
                    ?>
                </fieldset>
                <fieldset id="fieldset_step" style="width:460px;float: right;">
                    <legend><?php echo Yii::t('CaseInfo','Steps'); ?></legend>
                    <?php
                    $this->renderPartial('_fullscreen',array('position'=>'right'));
                    ?>
                    <div class="row" style="width: 460px;overflow-x: auto;word-break:break-all;word-wrap:break-word;">
                        <?php
                        echo $model->case_step;
                        ?>
                    </div>
                </fieldset>
             </div>
<?php $this->endWidget(); ?>

</div><!-- form -->