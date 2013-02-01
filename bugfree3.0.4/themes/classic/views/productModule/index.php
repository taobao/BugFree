<script type="text/javascript">
    var $productId = <?php echo $productId; ?>;
    var $selectedModuleId = <?php echo $selectedId; ?>;
    var $selectedParentId = <?php echo $selectedParentId; ?>;
    var $returnUrl = '<?php echo Yii::app()->createUrl('productModule/index',array('product_id'=>$productId,'selected_id'=>$selectedId));?>';
    var $deleteReturnUrl = '<?php echo Yii::app()->createUrl('productModule/index',array('product_id'=>$productId,'selected_id'=>$selectedParentId));?>';
    function reloadPage(event, treeId, treeNode)
    {
        window.location = '<?php echo Yii::app()->createUrl('productModule/index',array('product_id'=>$productId));?>'+'&selected_id='+treeNode.id;
    }
    $(document).ready(function() {
        var options = {
            dataType:'json',
            beforeSubmit:showRequest,
            success:showResponse
        };
        $('form').submit(function() {
            $(this).ajaxSubmit(options);
            return false;
        });
    });
    function zTreeOnAsyncSuccess(event, treeId, msg)
    {
        var node = product_module_treeTree.getNodeByParam("id",$selectedModuleId);
        var selectedNode = product_module_treeTree.getSelectedNode();
        if(node != selectedNode)
        {
            product_module_treeTree.selectNode(node);
        }
    }
    function showRequest(formData, jqForm, options) {
        $('.error').removeClass('error');
        return true;
    }
    function showResponse(responseText, statusText, xhr, $form)  {
        $("#admin_mask_dialog").dialog("close");
        if('failed' == responseText.status)
        {
            var errorFormId = responseText.formid;
            var detailedArr = responseText.detail;
            var msg = '';
            for(var field in detailedArr)
            {
                msg += detailedArr[field]+'<br/>';
                if('owner_name' == field&&'add' == errorFormId)
                {
                    $('form#'+errorFormId+' #ProductModule_add_'+field).addClass('error');
                }
                else
                {
                    $('form#'+errorFormId+' #ProductModule_'+field).addClass('error');
                }
            }
            showErrorMsg(msg);
        }
        else
        {
            if(('1' == $('#is_delete').val())||('1' == $('#separate_as_product').val()))
            {
                showSuccessMsg(responseText.detail,$deleteReturnUrl);
            }
            else
            {
                showSuccessMsg(responseText.detail,$returnUrl);
            }           
        }
    }
</script>
<div class="form administration">
    <h1><?php echo Yii::t('ProductModule', 'Edit Product Modules'); ?></h1>
    <hr/>
    <table>
        <tr>
            <td>
                <div id="product_module_tree" class="module_tree" style="overflow-y:scroll;overflow-x: auto;padding-top: 0px;margin-top: 0px;"></div>
                    <?php

                    $this->widget('application.extensions.ztree.ZTreeWidget', array(
        'name' => 'product_module_tree',
        'id' => 'product_module_tree',
        'value' => 0,
        'zNodes' => '[]',
        'setting' => '{
                    showIcon: false,
                    showLine: true,
                    treeNodeKey: "id",
                    expandSpeed: "",
                    async: true,
                    asyncUrl: "'.Yii::app()->createUrl('search/getProductModule').'",
                    asyncParam: ["id"],
                    asyncParamOther : ["product_id", '.$productId.',"productmodule_id",'.$selectedId.'],
                    callback: {
                            click:reloadPage,
                            asyncSuccess:zTreeOnAsyncSuccess
                    }
            }'
    ));
                    ?>
            </td>
            <td width="100%">
                <?php
                    if(0 != $selectedId)
                    {
                        echo $this->renderPartial('_form', array('model' => $editedModel,
                        'actionName' => Yii::t('ProductModule', 'Edit Module'),
                        'productId' => $productId, 'selectedId' => $selectedId,
                        'moduleFormId' => 'edit',
                        'moduleOptionArr' => $moduleOptionArr));
                    }
                ?>
                <?php
                    echo $this->renderPartial('_form', array('model' => $addedModel,
                        'actionName' => Yii::t('ProductModule', 'Add Module'),
                        'productId' => $productId, 'selectedId' => $selectedId,
                        'moduleFormId' => 'add',
                        'moduleOptionArr' => $moduleOptionArr));
                ?>
            </td>
        </tr>
    </table>
</div>