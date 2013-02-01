<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('userLog/index'), 'get'); ?>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
        <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
        <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('userLog/index'); ?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
$this->widget('View', array(
    'id' => 'searchresult-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        'id',
        array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
        'ip',
        'created_at'
    )
));
?>


