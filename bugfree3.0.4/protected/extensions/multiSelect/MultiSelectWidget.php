<?php
/**
 * Created on Jul 21, 2011
 *
 * Usage:
 * <?php $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
 *      'model' => $model,
 *      'attribute' => 'username',
 *      'config' => '{cookieId: "ac-cookie"}'
 * ));
 * ?>
 */
class MultiSelectWidget extends CInputWidget
{
    // General Purpose
    protected $assetsPath;
    protected $cssFile;

    // HTML Part
    protected $element = array();
    public $config = '{noneSelected: "--Not Selected--",oneOrMoreSelected: "*"}';
    public $selectOptionData = array();

    // Initialize widget
    public function init()
    {
        // publish assets folder
        $this->assetsPath = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');

        // resolve HTML element name and id
        list($this->element['name'], $this->element['id']) = $this->resolveNameID();
        Yii::app()->getClientScript()->registerCoreScript('jquery');
        Yii::app()->getClientScript()->registerCoreScript('bgiframe');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/jquery.multiSelect.js');
        Yii::app()->clientScript->registerScript($this->element['id'],
            '$("#' . $this->element['id'] . '").multiSelect('. $this->config . ');'
        );

        if($this->cssFile !== null)
        {
            Yii::app()->clientScript->registerCssFile($this->cssFile);
        } else {
            Yii::app()->clientScript->registerCssFile($this->assetsPath . '/jquery.multiSelect.css');
        }
    }

    public function run()
    {
        $this->render('widget');
    }
}