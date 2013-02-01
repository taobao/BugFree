<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * ReportController
 */
class ReportController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to perform 'index'
                'actions' => array('index'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * This is report index action
     */
    public function actionIndex()
    {
        $this->layout = false;
        $infoType = Yii::app()->request->getParam('type', Info::TYPE_BUG);
        $productId = Yii::app()->request->getParam('product_id', Yii::app()->user->getState('product'));
        $color = 'blue';
        $selected = Yii::app()->request->getParam('selected', array());
        
        if(Info::TYPE_CASE == $infoType)
        {
            $color = 'green';
        }
        else if(Info::TYPE_RESULT == $infoType)
        {
            $color = 'orange';
        }

        $reportService = new ReportService();
        $reports = $reportService->getReports($infoType, $productId, $selected);
        $this->render('index', array(
            'infoType' => $infoType,
            'productId' => $productId,
            'color' => $color,
            'reports' => $reports,
            'selected' => $selected,
        ));
    }
}
?>