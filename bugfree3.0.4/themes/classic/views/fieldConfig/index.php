<br/>
<?php
$basicShowColumnsArr = array(
    'display_order',
    'id',
    'field_name',
    array('name'=>'field_type','value'=>'Yii::t("FieldConfig", $data["field_type"])'),
    'field_label',
    array('name' => 'belong_group', 'value' => 'Yii::t("FieldConfig",$data["belong_group"])'),
    array('name' => 'is_required', 'value' => 'Yii::t("Common",$data["is_required"])'),
    array('name' => 'validate_rule', 'value' => 'Yii::t("FieldConfig",$data["validate_rule"])'),
    array('name' => 'field_value', 'type' => 'raw', 'value' => 'FieldConfigService::handleFieldValueStr($data["field_value"])'),
    'default_value',
    array('name' => Yii::t('Common', 'Operation'), 'type' => 'raw',
        'value' => 'FieldConfigService::getFieldOperation($data["id"],' .
        $productId . ',"' . $type . '",$data["is_dropped"],$data["edit_in_result"])')
);
if('bug' == $type)
{
    $basicShowColumnsArr[] = 'editable_action';
    $basicShowColumnsArr[] = array('name' => 'edit_in_result',
        'value' => 'CommonService::getTrueFalseName($data["edit_in_result"])');
    $basicShowColumnsArr[] = array('name' => 'result_group', 'value' => 'Yii::t("FieldConfig",$data["result_group"])');
}

$basicShowColumnsArr = array_merge($basicShowColumnsArr, array(array('name' => 'created_by',
        'value' => 'CommonService::getUserRealName($data["created_by"])'),
    'created_at', array('name' => 'updated_by',
        'value' => 'CommonService::getUserRealName($data["updated_by"])'),
    'updated_at'));
$this->widget('View', array(
    'id' => 'searchresult-grid',
    'dataProvider' => $dataProvider,
    'customTools' => '<a href="'.Yii::app()->createUrl('fieldConfig/edit',
            array('product_id'=>$productId,'type'=>$type)).'" >' . Yii::t('FieldConfig', 'Add new field') . '</a>',
    'rowCssClassExpression' => 'CommonService::getRowCss($data["is_dropped"])',
    'columns' => $basicShowColumnsArr
));
?>


