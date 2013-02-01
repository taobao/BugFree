<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('userGroup/index'), 'get'); ?>
        <a class="add_link" href="<?php echo Yii::app()->createUrl('userGroup/edit');?>" ><?php echo Yii::t('Common', 'Add Group'); ?></a>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
        <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
        <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('userGroup/index');?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
$this->widget('View', array(
    'id' => 'searchresult-grid',
    'dataProvider' => $dataProvider,
    'rowCssClassExpression' => 'CommonService::getRowCss($data["is_dropped"])',
    'columns' => array(
        'id',
        'name',
        array('name' => Yii::t('AdminCommon','Group User'),'type'=>'raw',
            'value' => 'UserGroupService::getGroupUserOption($data["id"])'),
        array('name' => Yii::t('AdminCommon','Group Manager'),'type'=>'raw',
            'value' => 'UserGroupService::getGroupManagerOption($data["id"])'),
        array('name' => Yii::t('Common','Operation'),'type'=>'raw',
            'value'=>'UserGroupService::getGroupOperation($data["id"],$data["created_by"],$data["is_dropped"])'),
        array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
        'created_at',
        array('name' => 'updated_by', 'value' => 'CommonService::getUserRealName($data["updated_by"])'),
        'updated_at'
    )
));
?>


