<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'BugFree 3.0.4',
    // preloading 'log' component
    'preload' => array('log'),
    // ui language
    'language' => '',
    // theme
    'theme' => 'classic',
    // define default controller
    'defaultController' => 'site',
    // time zone
    'timeZone' => 'Asia/Shanghai',
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.service.*',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123456',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true
        ),
        'request'=>array(
            'enableCookieValidation'=>true,
        ),
        'cache'=>array(
            'class'=>'CDbCache',
            'connectionID' => 'db',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => true,
            'rules' => array(
                '<type:\w+>/<id:\d+>/<action:\w+>' => 'info/edit',
                '<type:\w+>/list/<product_id:\d+>' => 'info/index',
                '<type:\w+>/<id:\d+>' => 'info/edit',
                '<controller:\w+>/view/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'db' => array(
            'pdoClass' => 'NestedPDO',
            'connectionString' => '',
            'emulatePrepare' => true,
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => ''
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info, error, warning',
                    'categories' => 'bugfree.*'
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        //Set directly under the htdocs to enable pic preview,
        //Please ensure this directory is existed and writeable
        //Strongly suggest not modify this.
        //If have to modify this path, please modify the BugFreeApp/protected/extensions/kindeditor4/assets/php/upload_json.php also
        'uploadPath' => '../BugFile',
        'picPreviewApp' => 'http://'.$_SERVER['SERVER_ADDR'].'/BugFile',
        'allUserGroupId' => 1,
        'showCaseResultTab' => true,
        'ldap' => array(
            'host' => '',
            'port' => '',
            'base' => '',
            'user' => '',
            'pass' => ''
        ),
        'mail' => array(
            'on' => '1',
            'from_address' => "bugfree-noreply@test.com",
            'from_name' => 'BugFree',
            'send_method' => 'SMTP',     //MAIL|SENDMAIL|SMTP|QMAIL
            'send_params' => array(
                'host' => '',   // The server to connect. Default is localhost
                'smtp_auth' => false,    // Whether or not to use SMTP authentication. Default is FALSE
                'username' => '',        // The username to use for SMTP authentication.
                'password' => ''        // The password to use for SMTP authentication.
            )
        )
    )
);
