<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'userInfo', //check user info
        );
    }

    public function filterUserInfo($filterChain)
    {
        $productId = Yii::app()->user->getState('product');
        if((true != Yii::app()->user->isGuest) &&
                empty($productId))
        {
            LoginService::setUserInfo();
        }
        $filterChain->run();
    }

    public function init()
    {
        $language = Yii::app()->user->getState('language');
        if(isset($language))
        {
            Yii::app()->language = $language;
        }
    }
}