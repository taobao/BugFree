<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of AdminController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class AdminController extends Controller
{
    public function accessRules()
    {
        $adminAccessable = CommonService::$TrueFalseStatus['FALSE'];
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin')||
                CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_manager'))
        {
            $adminAccessable = CommonService::$TrueFalseStatus['TRUE'];
        }
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index',),
                'expression' => "$adminAccessable != 0", //only admin can do these operation
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
}

?>
