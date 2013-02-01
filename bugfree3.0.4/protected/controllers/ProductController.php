<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ProductController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class ProductController extends AdminController
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
                'actions' => array('edit', 'disable', 'merge'),
                'users' => array('@'),
                )), parent::accessRules());
    }

    private function checkEditable($productId)
    {
        if(!ProductService::isProductEditable($productId))
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    public function actionEdit()
    {
        $actionName = Yii::t('Product', 'Add Product');
        $this->breadcrumbs = array(
            Yii::t('Product', 'Back To Product List') => array('index')
        );
        if(isset($_GET['id']))
        {
            $model = ProductService::loadModel($_GET['id']);
            $actionName = Yii::t('Product', 'Edit Product');
            $this->breadcrumbs[] = $model->name;
        }
        elseif(isset($_GET['source_id']))
        {
            $model = ProductService::loadModel($_GET['source_id']);
            $model->name = '';
            $model->display_order = '';
            $model->product_manager = '';
            $model->group_name = '';
            $actionName = Yii::t('Product', 'Copy Product');
        }
        else
        {
            $model = new Product;
            $model->solution_value = "By Design,Duplicate,External,Fixed,Not Repro,Postponed,Won't Fix";
        }

        self::checkEditable($model->id);
        if(isset($_POST['Product']))
        {
            $model->attributes = $_POST['Product'];
            $productInfo = $_POST['Product'];
            if(isset($_GET['id']))
            {
                $productInfo['id'] = $_GET['id'];
            }
            if(isset($_GET['source_id']))
            {
                $editResult = ProductService::copyProduct($_GET['source_id'], $productInfo);
            }
            else
            {
                $editResult = ProductService::editProduct($productInfo);
            }

            $returnJson['status'] = $editResult['status'];
            $returnJson['detail'] = $editResult['detail'];
            if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
            {
                if(isset($_GET['id']))
                {
                    $returnJson['detail'] = Yii::t('Product', 'Product edited successfully');
                }
                elseif(isset($_GET['source_id']))
                {
                    $returnJson['detail'] = Yii::t('Product', 'Product copied successfully');
                }
                else
                {
                    $returnJson['detail'] = Yii::t('Product', 'Product added successfully');
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

    /**
     * Merge product.
     */
    public function actionMerge()
    {
        $editResult = ProductService::mergeProduct($_GET['src_id'], $_GET['dis_id']);
        if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
        {
            echo '';
        }
        else
        {
            $resultStr = '';
            if(is_array($editResult['detail']))
            {
                $resultStr = json_encode($editResult['detail']);
            }
            else
            {
                $resultStr = $editResult['detail'];
            }
            echo $resultStr;
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDisable()
    {
        $editResult = ProductService::disableProduct($_GET['id'], $_GET['is_dropped']);
        if($editResult['status'] == CommonService::$ApiResult['SUCCESS'])
        {
            $this->redirect(array('index'));
        }
        else
        {
            $model->addErrors($editResult['detail']);
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $name = '';
        $pageSize = CommonService::getPageSize();
        $productIdNameArr = ProductService::getActiveProductIdNameArr();
        $criteria = new CDbCriteria();
        if(CommonService::$TrueFalseStatus['FALSE'] == Yii::app()->user->getState('system_admin'))
        {
            $managedProducts = TestUserService::getManagedProduct(Yii::app()->user->id);
            $criteria->addInCondition('id', $managedProducts);
            $criteria->addCondition('is_dropped="0"');
        }
        if(isset($_GET['name']))
        {
            $name = $_GET['name'];
            $criteria->addSearchCondition('name', $name);
        }
        $dataProvider = new CActiveDataProvider('Product', array(
                    'criteria' => $criteria,
                    'sort' => array(
                        'defaultOrder' => array(
                            'is_dropped' => false,
                            'display_order' => true,                           
                        )
                    ),
                    'pagination' => array(
                        'pageSize' => $pageSize,
                    ),
                ));
        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'name' => $name,
            'productIdNameArr' => $productIdNameArr
        ));
    }

}
