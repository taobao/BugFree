<script type="text/javascript">
    function showDetail($actionId)
    {
        jQuery.get("<?php echo Yii::app()->createUrl('adminAction/searchDetail'); ?>", {'id':$actionId}, function (data, textStatus){
            if('success' == textStatus)
            {
                $('#history_detail tr').remove();
                $('#history_detail').append(data);
            }
        });
        $('#admin_history_dialog').dialog('open');
    }
</script>
<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('adminAction/index'), 'get'); ?>
        <?php echo Yii::t('AdminCommon','Operation Type').$targetTableOption.'&nbsp;&nbsp;'.Yii::t('AdminCommon','target_id'); ?>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
        <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
        <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('adminAction/index'); ?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
            $this->widget('View', array(
                'id' => 'searchresult-grid',
                'dataProvider' => $dataProvider,
                'columns' => array(
                    'id',
                    'target_table',
                    'target_id',
                    'action_type',
                    array('name'=>Yii::t('Common','Detail'),
                        'type'=>'raw','value'=>'AdminActionService::getDetailLink($data["id"],$data["action_type"])'),
                    array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
                    'created_at'
                )
            ));
?>
<?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'admin_history_dialog',
            'htmlOptions' => array('style' => 'display:none'),
            'options' => array(
                'title' => Yii::t('AdminCommon', 'History Detail'),
                'autoOpen' => false,
                'width' => '800px',
                'modal' => true,
                'height' => 'auto',
                'resizable' => false
            )
        ));

        echo '<div id="searchresult-grid"><div id="SearchResultDiv" style="overflow: auto; width: 100%; height: 400px;background-color:#FFFFFF; ">
            <table style="white-space:nowrap;background-color:#FFFFFF;" class="items">
                <thead>
                <tr>
                <th >Action Field</th><th>Old Value</th><th>New Value</th></tr>
                </thead>
                <tbody id="history_detail">
                </tbody>
                </table></div></div>';
        $this->endWidget('zii.widgets.jui.CJuiDialog');
?>


