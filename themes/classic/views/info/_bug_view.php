<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bug-info-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>
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
                    'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->title)); ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'module_name',array('style'=>'padding-left:5px;')); ?>
                <span class="bugstatus_closed">&nbsp;</span>
                <?php echo $form->textField($model,'module_name',array('style'=>'width:580px;',
                    'readonly'=>'readonly','class'=>'readonly_field','title'=>$model->module_name)); ?>
	</div>
        </div>
        <div class="info_id"><span id="span_info_id"><?php echo $model->id; ?></span></div>
        </div>
        <div style="clear:both;">
            <fieldset style="width: 32%;float: left;">
                <legend><?php echo Yii::t('FieldConfig','bug_status'); ?></legend>
                <div class="row">
                    <?php echo $form->label($model,'bug_status'); ?>
                    <?php echo CHtml::encode($model->bug_status); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'assign_to'); ?>
                    <?php echo CHtml::encode(CommonService::getUserRealName($model->assign_to)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'mail_to'); ?>
                    <?php echo $form->textField($model,'mail_to',array(
            'readonly'=>'readonly','class'=>'info_input readonly_field','title'=>$model->mail_to)); ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'severity'); ?>
                    <?php
                    echo CHtml::encode(CommonService::getNameByValue(ProductService::getBugSeverityOption($model['product_id']), $model->severity));
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'priority'); ?>
                    <?php
                    echo CHtml::encode(CommonService::getNameByValue(ProductService::getBugPriorityOption($model['product_id']), $model->priority));
                    ?>
                </div>
                <?php echo empty($customfield['bug_status'])?'':$customfield['bug_status'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'updated_by'); ?>
                    <?php echo CHtml::encode($model->updated_by_name); ?>
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
                    <?php echo $form->labelEx($model,'created_by'); ?>
                    <?php echo CHtml::encode($model->created_by_name); ?>                
                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'created_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->created_at)); ?>
                </div>
                <?php echo empty($customfield['bug_open'])?'':$customfield['bug_open'] ; ?>
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','bug_resolve'); ?></legend>
                <div class="row">
                    <?php echo $form->labelEx($model,'resolved_by'); ?>
                    <?php echo CHtml::encode($model->resolved_by_name); ?>
                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'resolved_at'); ?>
                    <?php echo CHtml::encode(CommonService::getDateStr($model->resolved_at)); ?>
                </div>
                <?php echo empty($customfield['bug_resolve'])?'':$customfield['bug_resolve'] ; ?>
                <div class="row">
                    <?php echo $form->label($model,'solution'); ?>
                    <?php
                        echo CHtml::encode($model->solution);
                    ?>
                </div>
                <div class="row">
                    <?php echo $form->label($model,'duplicate_id'); ?>
                    <?php echo InfoService::getRelatedIdHtml('bug', $model->duplicate_id) ?>
                </div>
                
            </fieldset>
            <fieldset>
                <legend><?php echo Yii::t('FieldConfig','bug_close'); ?></legend>
                <?php echo empty($customfield['bug_close'])?'':$customfield['bug_close'] ; ?>
                <div class="row">
                    <?php echo $form->labelEx($model,'closed_by'); ?>
                    <?php echo CHtml::encode($model->closed_by_name); ?>
                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'closed_at'); ?>
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
                    <?php echo $form->labelEx($model,'related_bug'); ?>
                    <?php echo InfoService::getRelatedIdHtml('bug', $model->related_bug) ?>
                </div>
                <?php
                $isShowCaseResultTab = Yii::app()->params['showCaseResultTab'];
                if($isShowCaseResultTab)
                {
                    $caseResultStr = '<div class="row">' .
                            $form->label($model, 'related_case') .
                            InfoService::getRelatedIdHtml('case', $model->related_case) . '</div>';
                    $caseResultStr .= '<div class="row">' .
                            $form->label($model, 'related_result') .
                            InfoService::getRelatedIdHtml('result', $model->related_result) .
                            '</div>';
                    echo $caseResultStr;
                }
                ?>
            </fieldset>

            <fieldset>
                <legend><?php echo Yii::t('Common','Attachments'); ?></legend>
                <?php echo $model->attachment_file; ?>
             </fieldset>

            </div>
            </div>
             <div style="clear:both;">
                <fieldset id="fieldset_comment" style ="width:460px;float: left;word-break:break-all;word-wrap:break-word;overflow: hidden;">
                    <legend><?php echo Yii::t('Common','Comment'); ?></legend>
                    <?php
                    $this->renderPartial('_fullscreen',array('position'=>'left'));
                    echo ActionHistoryService::getActionHistory('bug', $model->id, $model->product_id);
                    ?>
                </fieldset>
                <fieldset id="fieldset_step" style="width: 460px;float: right;">
                    <legend><?php echo $model->getAttributeLabel('repeat_step') ?></legend>
                    <?php
                    $this->renderPartial('_fullscreen',array('position'=>'right'));
                    ?>
                    <div class="row" style="width: 460px;overflow-x: auto;word-break:break-all;word-wrap:break-word;">
                        <?php
                        echo $model->repeat_step;
                        ?>
                    </div>
                </fieldset>
             </div>
<?php $this->endWidget(); ?>

</div><!-- form -->
