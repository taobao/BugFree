<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ProductModuleController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ProductModuleController extends Controller
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
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index'),
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

    public function actionIndex()
    {
        self::checkEditable();
        $productId = $_GET['product_id'];
        $productInfo = Product::model()->findByPk($_GET['product_id']);
        $this->breadcrumbs = array(
            Yii::t('Product', 'Back To Product List') => array('/product/index'),
            $productInfo->name . Yii::t('ProductModule', 'Edit Product Modules')
        );
        $selectedId = 0;
        $selectedParentId = 0;
        $addedModel = new ProductModule();
        $addedModel->product_id = $productId;
        $addedModel->parent_id = 0;
        $addedModel->display_order = 0;

        $moduleOptionArr = ProductModuleService::getSelectModuleListOption($productId);


        if(isset($_GET['selected_id']) && (0 != $_GET['selected_id']))
        {
            $selectedId = $_GET['selected_id'];
            $editedModel = ProductModuleService::loadModel($selectedId);
            if($editedModel->parent_id != null)
            {
                $selectedParentId = $editedModel->parent_id;
            }
            $addedModel->parent_id = $_GET['selected_id'];
        }
        else
        {
            $editedModel = new ProductModule();
        }

        if(isset($_POST['ProductModule']))
        {
            $returnJson = array();
            $productModuleInfo = $_POST['ProductModule'];
            $productModuleInfo['product_id'] = $productId;
            //delete product module
            if(!empty($_POST['is_delete']))
            {
                $editResult = ProductModuleService::deleteModule($_POST['ProductModule']['id']);               
            }
            else if(!empty($_POST['separate_as_product']))
            {
                $editResult = ProductModuleService::separateModule($_POST['ProductModule']['id']);
            }
            else //add or update module
            {
                if(isset($_POST['ProductModule']['id']))
                {
                    if(!empty($_POST['is_delete']))
                    {
                        ProductModule::model()->findByPk($_POST['ProductModule']['id'])->delete();
                    }
                    $editedModel->attributes = $_POST['ProductModule'];
                    $productModuleInfo['add_owner_name'] = $productModuleInfo['owner_name'];
                }
                else
                {
                    $addedModel->attributes = $_POST['ProductModule'];
                    $productModuleInfo['owner_name'] = $productModuleInfo['add_owner_name'];
                    if(!empty($_GET['selected_id']))
                    {
                        $productModuleInfo['parent_id'] = $_GET['selected_id'];
                    }
                    else
                    {
                        $productModuleInfo['parent_id'] = 0;
                    }
                }
                $editResult = ProductModuleService::editProductModule($productModuleInfo);
            }
            $returnJson['status'] = $editResult['status'];
            if($editResult['status'] == CommonService::$ApiResult['FAIL'])
            {
                if(isset($_POST['ProductModule']['id']))
                {
                    $editedModel->addErrors($editResult['detail']);
                    $returnJson['formid'] = 'edit';
                }
                else
                {
                    $addedModel->addErrors($editResult['detail']);
                    $returnJson['formid'] = 'add';
                }
                $returnJson['detail'] = $editResult['detail'];
            }
            else
            {
                if(!empty($_POST['is_delete']))
                {
                    $returnJson['detail'] = Yii::t('ProductModule', 'Module deleted successfully');
                }
                elseif(!empty($_POST['separate_as_product']))
                {
                    $returnJson['detail'] = Yii::t('ProductModule', 'Module separated as product successfully');
                }
                elseif(isset($_POST['ProductModule']['id']))
                {
                    $returnJson['detail'] = Yii::t('ProductModule', 'Module edited successfully');
                }
                else
                {
                    $returnJson['detail'] = Yii::t('ProductModule', 'Module added successfully');
                }
            }
            echo json_encode($returnJson);
            return;
        }

        $this->render('index', array(
            'productId' => $productInfo->id,
            'selectedId' => $selectedId,
            'selectedParentId' => $selectedParentId,
            'productName' => $productInfo->name,
            'addedModel' => $addedModel,
            'editedModel' => $editedModel,
            'moduleOptionArr' => $moduleOptionArr
        ));
    }

}
