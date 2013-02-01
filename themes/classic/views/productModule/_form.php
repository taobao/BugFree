<div class="form administration">
<?php
$formAction = Yii::app()->createUrl('productModule/index',
        array('product_id'=>$productId,'selected_id'=>$selectedId));
$form=$this->beginWidget('CActiveForm', array(
	'id'=>$moduleFormId,
        'action' => $formAction,
	'enableAjaxValidation'=>false,
));
?>

        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr class="dash"/>
	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>30,'maxlength'=>45,'class'=>'required')); ?>
	</div>
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        ?>
        <div class="row">
		<?php echo $form->label($model,'parent_id'); ?>
                <?php
                if('add' != $moduleFormId)
                {
                    echo $form->dropDownList($model,'parent_id',
                            $moduleOptionArr,
                            array('style'=>'width:400px;'));
                }
                else
                {
                    echo CHtml::encode($moduleOptionArr[$model->parent_id]);
                }
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'owner_name'); ?>
                <?php
                $ownerAttributeName = 'owner_name';
                if('add' == $moduleFormId)
                {
                    $ownerAttributeName = 'add_owner_name';                   
                }
                $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                        'model' => $model,
                        'attribute' => $ownerAttributeName,
                        'htmlOptions' => array(
                            'style' => 'width:240px'
                        ),
                        'urlOrData' => TestUser::getSearchUserUrl()
                    ));
                ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('class'=>'required')).Yii::t('Common','Please input an integer between 0~255'); ?>
	</div>

	<hr class="dash"/>
	<?php
        if('edit' == $moduleFormId)
        {
            echo $form->hiddenField($model,'id');
            echo CHtml::hiddenField('is_delete');
            echo CHtml::hiddenField('separate_as_product');
            $actionButtonStr = CHtml::submitButton(Yii::t('ProductModule','Save Module'),
                    array('class'=>'margin-left-190'));
            if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
            {
                $actionButtonStr .= CHtml::submitButton(Yii::t('ProductModule','Separate as product'),
                    array('style'=>'margin-left:10px;','onclick'=>'if(confirm("'.
                        Yii::t('ProductModule', 'Sure to separate as product?').
                        '")){$("#separate_as_product").val(1);$("#admin_mask_dialog").dialog("open");}else{return false;}'));
            }
            $actionButtonStr .= CHtml::submitButton(Yii::t('ProductModule','Delete Module'),
                    array('style'=>'margin-left:10px;','onclick'=>'if(confirm("'.
                        Yii::t('Common', 'Sure to delete?').'")){$("#is_delete").val(1);}else{return false;}'));
            echo $actionButtonStr;
        }
        else
        {
            echo CHtml::submitButton(Yii::t('ProductModule','Add Module'),
                    array('class'=>'margin-left-190'));
        }        
        ?>
<?php $this->endWidget(); ?>

</div>