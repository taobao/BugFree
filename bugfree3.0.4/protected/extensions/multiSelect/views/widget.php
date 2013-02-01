<?php
$this->htmlOptions['multiple']="multiple";
if(isset($this->model))
{   
    echo CHtml::activeDropDownList($this->model, $this->attribute,$this->selectOptionData, $this->htmlOptions);
}
else
{
    echo CHtml::dropDownList($this->name, $this->value,$this->selectOptionData, $this->htmlOptions);
}
?>