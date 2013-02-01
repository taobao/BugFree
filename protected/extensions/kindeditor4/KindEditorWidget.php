<?php

class KindEditorWidget extends CInputWidget
{

    // General Purpose
    protected $assetsPath;
    protected $cssFile;
    public $miniMode = false;
    public $editorname = '';
    //	HTML Part
    protected $element = array();
    //	Javascript Part
    protected $editorOptions = array();

    //	Initialize widget
    public function init()
    {
        // publish assets folder
        $this->assetsPath = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');

        //	resolve HTML element name and id
        list($this->element['name'], $this->element['id']) = $this->resolveNameID();

        //	include CKEditor file
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/kindeditor-min.js');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/lang/zh_CN.js');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/plugins/preview/preview.js');

        if($this->miniMode)
        {
            $height = "height : '200px'";
        }
        else
        {
            $height = "height : '300px'";
        }

        $option = "{resizeType : 0,
                uploadJson : '".$this->assetsPath."/php/upload_json.php',
		fileManagerJson : '../php/file_manager_json.php',
		allowFileManager : true,
                width : '99%',
                newlineTag : 'br',".$height.
                ",items : ['undo', 'redo', '|', 'cut', 'copy', 'paste',
        'plainpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', '|', 'fullscreen', '/',
        'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough',  '|', 'image',
        'table', 'hr', 'code', 'link', 'unlink', 'about']
            }";

        if('' == $this->editorname)
        {
            $this->editorname = 'kindeditor_'.$this->element['id'];
        }
        Yii::app()->clientScript->registerScript("kindEditor" . $this->element['id'],
                "window.kindeditor_".$this->element['id'].";
                    KindEditor.ready(function(K) {
                                ".$this->editorname."=K.create('#" . $this->element['id'] . "'," . $option . ");
                                ".$this->editorname.".sync();
                        });"
        );

        if($this->cssFile !== null)
        {
            Yii::app()->clientScript->registerCssFile($this->cssFile);
        }
    }

    public function run()
    {
        $this->render('widget');
    }

}