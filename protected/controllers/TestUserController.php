<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestUserController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TestUserController extends AdminController
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
        return array_merge(array(array('allow',
                'actions' => array('edit', 'adminedit', 'disable', 'syncPinyin'),
                'users' => array('@'),
                )), parent::accessRules());
    }

    private function checkUserEditable($userId, $actionType)
    {
        if(!TestUserService::isUserEditable($userId, $actionType))
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    public function actionAdminEdit()
    {
        $actionName = Yii::t('TestUser', 'Add User');
        if(isset($_GET['id']))
        {
            $model = TestUserService::loadModel($_GET['id']);
            $actionName = Yii::t('TestUser', 'Edit User');
        }
        else
        {
            $model = new TestUser();
        }
        self::checkUserEditable($model->id, TestUserService::ADMIN_EDIT_USER);

        $this->breadcrumbs = array(
            Yii::t('TestUser', 'Back To User List') => array('/testUser/index'),
            $actionName
        );

        if(isset($_POST['TestUser']))
        {
            $userInfo = $_POST['TestUser'];
            if(isset($_GET['id']))
            {
                $userInfo['id'] = $_GET['id'];
            }
            $editResult = TestUserService::editUser($userInfo, TestUserService::ADMIN_EDIT_USER);
            $model->attributes = $_POST['TestUser'];
            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                if(isset($_GET['id']))
                {
                    $returnJson['detail'] = Yii::t('TestUser', 'User information edited successfully');
                }
                else
                {
                    $returnJson['detail'] = Yii::t('TestUser', 'User added successfully');
                }
            }
            echo json_encode($returnJson);
            return;
        }

        $this->render('adminedit', array(
            'model' => $model,
            'actionName' => $actionName
        ));
    }

    public function actionEdit()
    {
        $this->layout = '//layouts/user_edit';
        $actionName = Yii::t('Common', 'Edit My Info');
        self::checkUserEditable($_GET['id'], TestUserService::SELF_EDIT_USER);
        $model = TestUserService::loadModel($_GET['id']);
        if(isset($_POST['TestUser']))
        {
            $userInfo = $_POST['TestUser'];
            $userInfo['id'] = $_GET['id'];

            $editResult = TestUserService::editUser($userInfo, TestUserService::SELF_EDIT_USER);
            $model->attributes = $_POST['TestUser'];

            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                $returnJson['detail'] = Yii::t('TestUser', 'User information edited successfully');
            }
            echo json_encode($returnJson);
            return;
        }

        $this->render('edit', array(
            'model' => $model,
            'actionName' => $actionName
        ));
    }

    public function actionDisable()
    {
        $userInfo['id'] = $_GET['id'];
        $userInfo['is_dropped'] = $_GET['is_dropped'];
        $editResult = TestUserService::editUser($userInfo, TestUserService::ADMIN_EDIT_USER);
        if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
        {
            echo '';
        }
        else
        {
            echo Yii::t('Common', 'Operate failed');
        }
    }

    public function actionIndex()
    {
        $pageSize = CommonService::getPageSize();

        $criteria = new CDbCriteria();
        $name = '';
        if(isset($_GET['name']))
        {
            $name = $_GET['name'];
            $criteria->addCondition("realname like ".Yii::app()->db->quoteValue('%'.$_GET['name'].'%').
                    " or username like ".Yii::app()->db->quoteValue('%'.$_GET['name'].'%')." or email like ".
                    Yii::app()->db->quoteValue('%'.$_GET['name'].'%'));
        }
        $dataProvider = new CActiveDataProvider('TestUser', array(
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
     * get the update user pinyin sql
     * @return sql str
     */
    public function actionSyncPinyin()
    {
        set_time_limit(0);
        $count = TestUser::model()->count();
        echo 'total count:' . $count;
        $start = 0;
        $errorno = 0;
        while($start < $count)
        {
            $condition = new CDbCriteria();
            $condition->limit = 100;
            $condition->offset = $start;
            $users = TestUser::model()->findAll($condition);
            foreach($users as $user)
            {
                if(!empty($user['realname']))
                {
                    $pinyin = PinyinService::pinyin(strtolower($user['realname']));
                    //not full translated
                    if($pinyin[2] == false)
                    {
                        $errorno += 1;
                        echo "Error#update {{test_user}} set full_pinyin='" . $pinyin[0] . "',first_pinyin='" .
                        $pinyin[1] . "' where id=" . $user['id'] . " and realname = '" . $user['realname'] .
                        "' and username='" . $user['username'] . "' ;<br/>";
                    }
                    else//full translated
                    {
                        echo "update {{test_user}} set full_pinyin='" . $pinyin[0] . "',first_pinyin='" .
                        $pinyin[1] . "' where id=" . $user['id'] . " ;<br/>";
                    }
                }
            }
            $start += 100;
        }
        echo "total error:" . $errorno . '<br/>';
    }

}
