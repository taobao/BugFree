<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserLogController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserLogController extends Controller
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
                'actions' => array('index'),
                'expression' => "$adminAccessable != 0", //only admin can do these operation
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
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
            $userArr = TestUserService::getUserList($name,'id');
            $criteria->addInCondition('created_by', array_keys($userArr));
            $criteria->addSearchCondition('ip', $name, true, 'OR');
        }
        $dataProvider = new CActiveDataProvider('UserLog', array(
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
        $model = UserLog::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}
