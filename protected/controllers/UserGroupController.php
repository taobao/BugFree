<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserGroupController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserGroupController extends AdminController
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
                'actions' => array('edit', 'disable'),
                'users' => array('@'),
                )), parent::accessRules());
    }

    private function checkEditable($groupId)
    {
        if(!UserGroupService::isGroupEditable($groupId))
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    public function actionEdit()
    {
        $actionName = Yii::t('AdminCommon', 'Add Group');
        if(isset($_GET['id']))
        {
            $model = UserGroupService::loadModel($_GET['id']);
            $actionName = Yii::t('AdminCommon', 'Edit Group');
        }
        else
        {
            $model = new UserGroup();
            $model->group_user = array();
            $model->group_manager = '';
        }

        self::checkEditable($model->id);
        $this->breadcrumbs = array(
            Yii::t('AdminCommon', 'Back To Group List') => array('/userGroup/index'),
            $actionName
        );

        if(isset($_POST['UserGroup']))
        {
            $userGroupInfo = $_POST['UserGroup'];
            if(isset($_GET['id']))
            {
                $userGroupInfo['id'] = $_GET['id'];
            }
            $editResult = UserGroupService::editGroup($userGroupInfo);
            $model->attributes = $_POST['UserGroup'];

            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                if(isset($_GET['id']))
                {
                    $returnJson['detail'] = Yii::t('AdminCommon', 'Group edited successfully');
                }
                else
                {
                    $returnJson['detail'] = Yii::t('AdminCommon', 'Group added successfully');
                }
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
        $editResult = UserGroupService::disableUserGroup($_GET['id'], $_GET['is_dropped']);
        if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
        {
            $this->redirect(array('index'));
        }
        else
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $pageSize = CommonService::getPageSize();

        $criteria = new CDbCriteria();
        if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
        {
            $managedgroups = UserGroupService::getManagedGroup(Yii::app()->user->id);
            $criteria->addInCondition('id', $managedgroups);
        }
        $name = '';
        if(isset($_GET['name']))
        {
            $name = $_GET['name'];
            $criteria->addSearchCondition('name', $name);
        }
        $dataProvider = new CActiveDataProvider('UserGroup', array(
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

}
