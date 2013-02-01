<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of SiteController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class SiteController extends Controller
{

    public function init()
    {
        if(isset($_POST['LoginForm']))
        {
            $language = $_POST['LoginForm']['language'];
            if(LoginForm::LANGUAGE_ZH_CN == $language || LoginForm::LANGUAGE_EN == $language)
            {
                Yii::app()->language = $language;
            }
        }
        else
        {
            $cookies = Yii::app()->request->getCookies();
            if(!empty($cookies['language']))
            {
                Yii::app()->language = $cookies['language']->value;
            }
        }
    }

    /**
     * access rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('login', 'logout', 'error', 'permission'),
                'users' => array('*')
            ),
            array(
                'allow',
                'actions' => array('index'),
                'users' => array('@')
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->layout = '//layouts/blank_error';
        if($error = Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionIndex()
    {
        $setUserInfoResult = LoginService::setUserInfo();
        if(!empty($setUserInfoResult))
        {
            $this->redirect(Yii::app()->homeUrl);
        }
        //if the url like ../bugfree
        if(empty($_GET))
        {
            $this->redirect(array('info/index', 'type' => 'bug', 'product_id' => Yii::app()->user->getState('product')));
        }
        else
        {
            //for the history url link use
            if(isset($_GET['r']))
            {
                //url like ..bugfree/index.php?r=info/edit&type=bug&id=132046
                if(('info/edit' == $_GET['r']) && (isset($_GET['type'])) && (isset($_GET['id'])))
                {
                    $this->redirect(Yii::app()->createUrl('info/edit', array('type' => $_GET['type'], 'id' => $_GET['id'])));
                }
                else if(('info/index' == $_GET['r']) && (isset($_GET['type'])) && (isset($_GET['product_id'])))//url like ..bugfree/index.php?r=info/index&type=bug&product_id=132046
                {
                    $this->redirect(Yii::app()->createUrl('info/index', array('type' => $_GET['type'], 'product_id' => $_GET['product_id'])));
                }
            }
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $this->layout = '//layouts/blank';
        $model = new LoginForm;
        if(isset($_GET['language']))
        {
            $model->language = $_GET['language'];
        }
        else
        {
            $cookies = Yii::app()->request->getCookies();
            if(!empty($cookies['language']))
            {
                $model->language = $cookies['language']->value;
            }
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            $result = LoginService::login($_POST['LoginForm']);
            if($result['status'] == 'success')
            {
                $returnUrl = Yii::app()->user->returnUrl;
                $this->redirect($returnUrl);
            }
            else
            {
                $model->addErrors($result['detail']);
            }
        }
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionPermission()
    {
        $this->layout = '//layouts/permission';
        $permissionTable = ProductService::getPermissionTable();
        $this->render('permission', array('permissionTable' => $permissionTable));
    }

}