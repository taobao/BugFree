<?php
/**
 * Created on Jul 21, 2011
 *
 * Usage:
 * <?php $this->widget('application.extensions.ztree.ZTreeWidget', array(
 *      'model' => $model,
 *      'attributes' => 'username',
 *      'zNodes' => '[]'
 *      'setting' => '{}'
 * ));
 * ?>
 */
class ZTreeWidget extends CWidget
{
    // General Purpose
    protected $assetsPath;
    protected $cssFile;

    // HTML Part
    protected $element = array();

    /**
     * @var CModel the data model associated with this widget.
     */
    public $model;
    /**
     * @var string the attribute associated with this widget. Starting from version 1.0.9,
     * the name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
     */
    public $attribute;
    /**
     * @var string the input name. This must be set if {@link model} is not set.
     */
    public $name;
    /**
     * @var string the input value
     */
    public $value;
    /**
     * @var string the selected id value
     */
    public $singleSelectedId = 'not_set';
    /**
     * @var array additional HTML options to be rendered in the input tag
     */
    public $htmlOptions = array();

    public $isMenu= false;
    public $setting = '{}';
    public $zNodes = '[]';
    public $id = 'ztree';
    public $menuId = 'ztree-menu';

    // Initialize widget
    public function init()
    {
        // publish assets folder
        $this->assetsPath = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');

        Yii::app()->getClientScript()->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/jquery.ztree.js');

        if($this->cssFile !== null)
        {
            Yii::app()->clientScript->registerCssFile($this->cssFile);
        } else {
            Yii::app()->clientScript->registerCssFile($this->assetsPath . '/zTreeStyle.css');
        }
        if($this->isMenu) {
            // resolve HTML element name and id
            list($this->element['name'], $this->element['id']) = $this->resolveNameID();
            Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/menu.js');
            Yii::app()->clientScript->registerScript($this->element['id'],
                '$("body").append("<div class=\"zTreeMenu\" id=\"' . $this->menuId . '\"><ul class=\"tree\" id=\"ul_'.$this->menuId.'\"></ul></div>");
                 var '.$this->menuId .' = $("#ul_' . $this->menuId . '").zTree(' . $this->setting . ', ' . $this->zNodes . ');
                 var menu'.$this->menuId .' = $.Menu($("#' . $this->element['id'] . '"), $("#' . $this->menuId . '"));
                 $("#' . $this->element['id'] . '").click(function(){
                     menu'.$this->menuId .'.showMenu('.$this->menuId.',\''.$this->menuId.'\',\''.$this->singleSelectedId.'\');
                 }).attr("autocomplete", "off");
                 function getCheckedNodesName(treeObj) {
                    var returnStr = "";
                    var tmp = treeObj.getCheckedNodes();
                    for (var i=0; i<tmp.length; i++) {
                        returnStr += tmp[i].name+",";
                    }
                    if(returnStr != "")
                    {
                        returnStr = returnStr.substr(0,returnStr.length-1);
                    }
                    return returnStr;
                }
                function change' . $this->element['id'] . '(event, treeId, treeNode){$(\'#'.$this->element['id'].'\').attr(\'value\',getCheckedNodesName('.$this->menuId.'));}
                function click' . $this->element['id'] . '(event, treeId, treeNode){$(\'#'.$this->menuId.'\').hide();$(\'#'.$this->element['id'].'\').attr(\'value\',treeNode.name);}
                menu'.$this->menuId .'.autoHide();'
            );
        } else {
            Yii::app()->clientScript->registerScript($this->id,
                'var '.$this->id.' = $("#' . $this->id . '").html("<ul class=\"tree\" id=\"ztree_'.$this->id.'\"></ul>"); '.
                    $this->id.'Tree = $("#' .
                    $this->id . ' ul").zTree(' . $this->setting . ', ' . $this->zNodes . ');'.
                    'var node = '.$this->id.'Tree.getNodeByParam("id",'.$this->value.');'.
                    $this->id.'Tree.selectNode(node);'
            );
        }
    }

    /**
     * @return array the name and the ID of the input.
     */
    protected function resolveNameID()
    {
        if ($this->name !== null)
            $name = $this->name;
        else if (isset($this->htmlOptions['name']))
            $name = $this->htmlOptions['name'];
        else if ($this->hasModel())
            $name = CHtml::activeName($this->model, $this->attribute);
        else
            throw new CException(Yii::t('yii', '{class} must specify "model" and "attribute" or "name" property values.', array('{class}' => get_class($this))));

        if (($id = $this->getId(false)) === null)
        {
            if (isset($this->htmlOptions['id']))
                $id = $this->htmlOptions['id'];
            else
                $id=CHtml::getIdByName($name);
        }

        return array($name, $id);
    }

    /**
     * @return boolean whether this widget is associated with a data model.
     */
    protected function hasModel()
    {
        return $this->model instanceof CModel && $this->attribute !== null;
    }

    public function run()
    {
        $this->htmlOptions['readonly'] = 'readonly';
   //     $this->htmlOptions['style'] = 'background-color:#F6F6F6;';
        if($this->isMenu)
            $this->render('widget');
    }
}