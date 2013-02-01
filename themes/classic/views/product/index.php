<script type="text/javascript">
    function showMergeDialog($id)
    {
        $("#product_merge_dialog").dialog("open");
        $("#merge_source_id").val($id);
    }

    function mergeProduct($disId,$srcId)
    {
        if($disId == $srcId)
        {
            alert("<?php echo Yii::t('Product','Can not merge self!'); ?>");
            return;
        }
        $("#admin_mask_dialog").dialog("open");
        $("#product_merge_dialog").dialog("close");
        jQuery.get("<?php echo Yii::app()->createUrl('product/merge'); ?>", {'dis_id':$disId,'src_id':$srcId}, function (data, textStatus){
            if('success' == textStatus)
            {               
                if('' == data)
                {
                    alert('<?php echo Yii::t('Product','Product merged successfully'); ?>');
                    $("#admin_mask_dialog").dialog("close");
                    window.location.href = window.location.href;
                }
                else
                {
                    alert(data);
                    $("#admin_mask_dialog").dialog("close");
                }
            }
        });
    }
</script>

<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('product/index'), 'get'); ?>
    <?php
    if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
    {
        echo '<a class="add_link" href="' . Yii::app()->createUrl('product/edit') . '" >' . Yii::t('Common', 'Add Product') . '</a>';
    }
    ?>
    <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
    <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
    <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('product/index'); ?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
    $this->widget('View', array(
        'id' => 'searchresult-grid',
        'dataProvider' => $dataProvider,
        'rowCssClassExpression' => 'CommonService::getRowCss($data["is_dropped"])',
        'columns' => array(
            'display_order',
            'id',
            'name',
            array('name' => Yii::t('Product', 'group_name'), 'type' => 'raw', 'value' => 'ProductService::getProductGroupOption($data["id"])'),
            array('name' => Yii::t('Product', 'product_manager'), 'type' => 'raw', 'value' => 'ProductService::getProductManagerOption($data["id"])'),
            array('name' => Yii::t('Common', 'Operation'), 'type' => 'raw', 'value' => 'ProductService::getProductOperation($data["id"],$data["is_dropped"])'),
            array('name' => Yii::t('Common', 'Manage Fields'), 'type' => 'raw', 'value' => 'ProductService::getProductCustomFieldLink($data["id"])'),
            array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
            'created_at',
            array('name' => 'updated_by', 'value' => 'CommonService::getUserRealName($data["updated_by"])'),
            'updated_at'
        )
    ));
?>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'product_merge_dialog',
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('Product', 'Select target product'),
            'autoOpen' => false,
            'width' => '300px',
            'modal' => true,
            'height' => 'auto',
            'resizable' => false,
        )
    ));
    echo '<input type="hidden" value="0" id="merge_source_id" name="merge_source_id">';
    echo '<br/>' . CHtml::dropDownList('merge_dis_id', '', $productIdNameArr, array('id' => 'merge_dis_id'));
    echo '&nbsp;&nbsp;' . CHtml::button(Yii::t('Common', 'Merge'),
            array('onclick' => 'if(confirm("' . Yii::t('Product', 'Sure to merge?') .
                '")){mergeProduct($("#merge_dis_id").val(),$("#merge_source_id").val());}else{return false;}'));
    $this->endWidget('zii.widgets.jui.CJuiDialog');
?>

