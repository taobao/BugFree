<?php

class TestFileController extends Controller
{
    public $layout = '//layouts/main';

    public function accessRules()
    {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('view'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id)
    {
        TestFileService::viewFile($id);
    }
}
