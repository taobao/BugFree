<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of FieldConfigController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class FieldConfigController extends Controller
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
        return array(
            array('allow',
                'actions' => array('index', 'edit', 'disable'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    private function checkEditable()
    {
        if(empty($_GET['product_id']))
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
        else
        {
            if(!ProductService::isProductEditable($_GET['product_id']))
            {
                throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
            }
        }
    }

    public function actionDisable()
    {
        $editResult = FieldConfigService::disableFieldConfig($_GET['id'], $_GET['is_dropped']);
        if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
        {
            $this->redirect(array('index', 'product_id' => $_GET['product_id'], 'type' => $_GET['type']));
        }
        else
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    public function actionEdit()
    {
        self::checkEditable();
        $productId = $_GET['product_id'];
        $productInfo = Product::model()->findByPk($productId);
        $type = $_GET['type'];
        $model = new FieldConfig();
        $actionName = Yii::t('FieldConfig', 'Add Field');
        if(isset($_GET['id']))
        {
            $model = FieldConfigService::loadModel($_GET['id']);
            $actionName = Yii::t('FieldConfig', 'Edit Field');
        }

        $this->breadcrumbs = array(
            Yii::t('Product', 'Back To Product List') => array('/product/index'),
            $productInfo->name . ' [' . ucfirst($_GET['type']) . Yii::t('Common', 'Manage Fields') . ']' => array('index', 'product_id' => $productId, 'type' => $type),
            $actionName
        );

        $model->type = $type;
        $model->scenario = $type;
        if(isset($_POST['FieldConfig']))
        {
            $model->attributes = $_POST['FieldConfig'];
            $fieldConfigInfo = $_POST['FieldConfig'];
            if(isset($_GET['id']))
            {
                $fieldConfigInfo['id'] = $_GET['id'];
            }
            $fieldConfigInfo['type'] = $type;
            $fieldConfigInfo['product_id'] = $productId;
            $editResult = FieldConfigService::editFieldConfig($fieldConfigInfo);
            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                if(isset($_GET['id']))
                {
                    $returnJson['detail'] = Yii::t('FieldConfig', 'Field edited successfully');
                }
                else
                {
                    $returnJson['detail'] = Yii::t('FieldConfig', 'Field added successfully');
                }
            }
            echo json_encode($returnJson);
            return;
        }
        $this->render('edit', array(
            'model' => $model,
            'actionName' => $actionName,
            'returnUrl' => Yii::app()->createUrl('fieldConfig/index',array('product_id'=>$productId,'type'=>$type))
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        self::checkEditable();
        $pageSize = CommonService::getPageSize();
        $productId = $_GET['product_id'];
        $productInfo = Product::model()->findByPk($productId);
        $this->breadcrumbs = array(
            Yii::t('Product', 'Back To Product List') => array('/product/index'),
            $productInfo->name . ' [' . ucfirst($_GET['type']) . Yii::t('Common', 'Manage Fields') . ']'
        );


        $dataProvider = new CActiveDataProvider('FieldConfig', array(
                    'criteria' => array(
                        'condition' => "product_id = :productId and type = :type",
                        'params' => array(':productId' => $productId,
                            ':type' => Yii::app()->request->getParam('type')),
                    ),
                    'pagination' => array(
                        'pageSize' => $pageSize,
                    ),
                ));


        $productInfo = Product::model()->findByPk($_GET['product_id']);
        if($productInfo !== null)
        {
            $this->render('index', array(
                'dataProvider' => $dataProvider,
                'productId' => $productInfo->id,
                'productName' => $productInfo->name,
                'type' => Yii::app()->request->getParam('type'),
            ));
        }
        else
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
    }

}
