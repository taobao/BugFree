<?php 
if(isset($this->model))
{
    echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);    
}
else
{
    echo CHtml::textField($this->name, $this->value, $this->htmlOptions);
}
?>