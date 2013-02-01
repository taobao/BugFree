<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of AdminActionController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class AdminActionController extends Controller
{

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/admin';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
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
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'searchDetail'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionSearchDetail()
    {
        $returnStr = '';
        if(isset($_GET['id']))
        {
            $returnStr = AdminActionService::getDetailContent($_GET['id']);
        }
        echo $returnStr;
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $pageSize = CommonService::getPageSize();
        $criteria = new CDbCriteria();
        $name = '';
        if(isset($_GET['name']))
        {
            $name = $_GET['name'];
            $criteria->addSearchCondition('target_id', $name);
        }
        $targetTable = '';
        if(isset($_GET['target_table'])&&(!empty ($_GET['target_table'])))
        {
            $targetTable = $_GET['target_table'];
            $criteria->addCondition('target_table='.Yii::app()->db->quoteValue($targetTable));
        }
        $targetTableOption = CHtml::dropDownList('target_table', $targetTable,
                        array(
                            '' => Yii::t('AdminCommon','All'),
                            'product' => Yii::t('AdminCommon','product'),
                            'product_module' => Yii::t('AdminCommon','product_module'),
                            'field_config' => Yii::t('AdminCommon','field_config'),
                            'test_user' => Yii::t('AdminCommon','test_user'),
                            'user_group' => Yii::t('AdminCommon','user_group'),
                            'test_option' => Yii::t('AdminCommon','test_option')));
        $dataProvider = new CActiveDataProvider('AdminAction', array(
                    'criteria' => $criteria,
                    'sort' => array(
                        'defaultOrder' => array(
                            'id' => true,
                        )
                    ),
                    'pagination' => array(
                        'pageSize' => $pageSize,
                    ),
                ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'name' => $name,
            'targetTableOption' => $targetTableOption
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = UserLog::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}