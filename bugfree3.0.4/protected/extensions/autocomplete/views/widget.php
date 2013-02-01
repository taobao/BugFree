<?php
if(isset($this->model))
{
    if($this->multiline)
    {
        echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
    }
    else
    {
        echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
    }
}
else
{
    if($this->multiline)
    {
        echo CHtml::textArea($this->name, $this->value, $this->htmlOptions);
    }
    else
    {
        echo CHtml::textField($this->name, $this->value, $this->htmlOptions);
    }
}
?>