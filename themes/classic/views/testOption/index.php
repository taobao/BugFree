<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('testOption/index'), 'get'); ?>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
        <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
        <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('testOption/index');?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
$this->widget('View', array(
    'id' => 'searchresult-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        'id',
        'option_name',
        'option_value',
        array('name' => Yii::t('Common','Operation'),'type'=>'raw',
            'value'=>'TestOptionService::getOptionOperation($data["id"])'),
        array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
        'created_at',
        array('name' => 'updated_by', 'value' => 'CommonService::getUserRealName($data["updated_by"])'),
        'updated_at'
    )
));
?>


