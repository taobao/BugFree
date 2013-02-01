<?php
/**
 * Created on Jul 21, 2011
 *
 * Usage:
 * <?php $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
 *      'model' => $model,
 *      'attribute' => 'username',
 *      'urlOrData' => '["Red", "Black", "Green", "Blue", "Yellow"]',
 *      'config' => '{cookieId: "ac-cookie"}'
 * ));
 * ?>
 */
class AutoCompleteWidget extends CInputWidget
{
    // General Purpose
    protected $assetsPath;
    protected $cssFile;
    public $multiline = false;

    // HTML Part
    protected $element = array();

    public $urlOrData;
    public $config = '{}';

    // Initialize widget
    public function init()
    {
        // publish assets folder
        $this->assetsPath = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');

        // resolve HTML element name and id
        list($this->element['name'], $this->element['id']) = $this->resolveNameID();

        Yii::app()->getClientScript()->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/jquery.ajaxQueue.js');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/jquery.cookie.js');
        //Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/thickbox-compressed.js');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/json2.js');
        Yii::app()->clientScript->registerScriptFile($this->assetsPath . '/jquery.autocomplete.js');

        Yii::app()->clientScript->registerCssFile($this->assetsPath . '/thickbox.css');

        Yii::app()->clientScript->registerScript($this->element['id'],
            '$("#' . $this->element['id'] . '").autocompleter(' . $this->urlOrData . ', ' . $this->config . ');'
        );

        if($this->cssFile !== null)
        {
            Yii::app()->clientScript->registerCssFile($this->cssFile);
        } else {
            Yii::app()->clientScript->registerCssFile($this->assetsPath . '/jquery.autocomplete.css');
        }
    }

    public function run()
    {
        $this->render('widget');
    }
}