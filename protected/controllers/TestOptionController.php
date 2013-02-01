<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestOptionController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TestOptionController extends Controller
{

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/admin';

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        $adminAccessable = CommonService::$TrueFalseStatus['FALSE'];
        if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
        {
            $adminAccessable = CommonService::$TrueFalseStatus['TRUE'];
        }
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'edit'),
                'expression' => "$adminAccessable != 0", //only admin can do these operation
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionEdit()
    {
        if(isset($_GET['id']))
        {
            $model = $this->loadModel($_GET['id']);
            $actionName = Yii::t('AdminCommon', 'Edit Option');
        }
        else
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }

        $this->breadcrumbs = array(
            Yii::t('AdminCommon', 'Back To Option List') => array('/testOption/index'),
            $actionName
        );

        if(isset($_POST['TestOption']))
        {
            $optionInfo = $_POST['TestOption'];
            if(TestOption::DB_VERSION == $model['option_name'])
            {
                $returnJson['status'] = CommonService::$ApiResult['FAIL'];
                $returnJson['detail']['option_value'] = Yii::t('AdminCommon', 'db_version not allow edit');
                echo json_encode($returnJson);
                return;
            }
            $optionInfo['id'] = $_GET['id'];
            $editResult = TestOptionService::editOption($optionInfo);
            $model->attributes = $_POST['TestOption'];

            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                $returnJson['detail'] = Yii::t('AdminCommon', 'Option edited successfully');
            }
            echo json_encode($returnJson);
            return;
        }

        $this->render('edit', array(
            'model' => $model,
            'actionName' => $actionName
        ));
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
            $criteria->addSearchCondition('option_name', $name);
        }
        $dataProvider = new CActiveDataProvider('TestOption', array(
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
            'name' => $name
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = TestOption::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}
