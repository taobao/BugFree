<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * PageController
 * 分页控制器
 *
 * @version 3.0
 */
class PageController extends Controller
{

    /**
     * access rules
     * 权限控制配置数组
     *
     * 任意用户通过setPageSize动作
     * 禁止任意用户通过其余动作
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('setPageSize', 'setOutPutType'),
                'users' => array('@')
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }

    /**
     * set page size
     * 设置分页页数
     *
     * @param pagesize 分页页数
     */
    public function actionSetPageSize()
    {
        if(isset($_GET['pagesize']))
        {
            $pageSizeCookie = new CHttpCookie('pageSize', $_GET['pagesize']);
            $pageSizeCookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
            Yii::app()->request->cookies['pageSize'] = $pageSizeCookie;
            Yii::app()->user->setState('pageSize',$_GET['pagesize']);
        }
    }

    public function actionSetOutPutType()
    {
        if(!empty($_GET['product_id']) && (!empty($_GET['info_type'])) && (!empty($_GET['show_type'])))
        {
            Yii::app()->user->setState($_GET['product_id'] . '_' . $_GET['info_type'] . '_showtype', $_GET['show_type']);
        }
    }
}
?>