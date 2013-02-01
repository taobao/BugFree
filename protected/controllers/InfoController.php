<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of InfoController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class InfoController extends Controller
{

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/main';

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'edit', 'export'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

//    public function filters()
//    {
//        return array_merge(parent::filters(), array(
//            'accessControl',
//        ));
//    }

    protected function isEditAction($actionType)
    {
        if(BugInfo::ACTION_OPEN_EDIT == $actionType ||
                BugInfo::ACTION_RESOLVE_EDIT == $actionType ||
                BugInfo::ACTION_CLOSE_EDIT == $actionType ||
                CaseInfo::ACTION_OPEN_EDIT == $actionType ||
                ResultInfo::ACTION_OPEN_EDIT == $actionType)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * edit a particular model.
     */
    public function actionEdit()
    {
        $this->layout = '//layouts/notitle_main';
        $infoType = $this->getInfoType();
        $basicModelName = ucfirst(strtolower($infoType)) . 'InfoView';
        $isPageDirty = 0;
        $actionType = 'view';
        if(!empty($_GET['action']))
        {
            $actionType = $_GET['action'];
        }

        list($productId, $model, $customInfo, $customFieldArr) = InfoService::initInfoPage($infoType,
                        $this, $actionType, Yii::app()->request);
        if(!Info::isProductAccessable($productId))
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
        if(isset($_POST[$basicModelName]))
        {
            if(('' == $_POST['templateTitle']) && empty($_POST['isPageDirty']) &&
                    (!empty($model->id)) && $this->isEditAction($actionType))
            {
                $this->redirect(array('edit', 'type' => $infoType, 'id' => $model->id));
            }
            else
            {
                $attachmentFile = CUploadedFile::getInstancesByName('attachment_file');
                list($model, $customFieldArr) = InfoService::saveInfoPage($infoType,
                                $model, $customInfo, $attachmentFile,
                                $this, $actionType, Yii::app()->request);
                $isPageDirty = 1;
            }
        }

        $buttonList = InfoService::getButtonList($infoType, $actionType, $model);
        $this->render('edit', array(
            'infoType' => $infoType,
            'isPageDirty' => $isPageDirty,
            'actionType' => $actionType,
            'model' => $model,
            'buttonList' => $buttonList,
            'customfield' => $customFieldArr
        ));
    }

    public function actionExport()
    {
        $infoType = $this->getInfoType();
        $productId = $this->getProductId($infoType);
        $fieldCookieKey = $productId . '_' . $infoType . '_showField';
        $cookieShowFieldStr = $this->getShowFieldCookie($fieldCookieKey);
        if(!empty($cookieShowFieldStr) && is_string($cookieShowFieldStr))
        {
            $showFieldArr = CommonService::splitStringToArray(',', $cookieShowFieldStr);
        }
        else
        {
            $showFieldArr = SearchService::getDefaultShowFieldArr($infoType);
        }
        $searchRowArr = Yii::app()->user->getState($productId . '_' . $infoType . '_search');

        $filterSql = Yii::app()->user->getState($productId . '_' . $infoType . '_filterSql');
        $orderArr = Yii::app()->user->getState($productId . '_' . $infoType . '_sortArr');
        if(Info::TYPE_BUG == $infoType)
        {
            $showFieldArr[] = 'repeat_step';
        }
        else if(Info::TYPE_CASE == $infoType)
        {
            $showFieldArr[] = 'case_step';
        }
        else if(Info::TYPE_RESULT == $infoType)
        {
            $showFieldArr[] = 'result_step';
        }
        if(!in_array('id', $showFieldArr))
        {
            array_unshift($showFieldArr, 'id');
        }
        $exportDataResult = ExportService::getExportData($infoType, $searchRowArr, $productId, $showFieldArr, $orderArr, $filterSql);
        if(CommonService::$ApiResult['SUCCESS'] == $exportDataResult['status'])
        {
            $rawData = $exportDataResult['detail'];
        }
        else
        {
            throw new CHttpException(400, $exportDataResult['detail']);
        }

        $fileStoreUrl = Yii::app()->params->uploadPath . '/' . $infoType . 'list.xml';
        $file = fopen($fileStoreUrl, "w");
        $searchFieldConfig = SearchService::getSearchableFields($infoType, $productId);
        $searchFieldConfig['case_step'] = array('label' => Yii::t('CaseInfo', 'case_step'),
            'isBasic' => true);
        $searchFieldConfig['result_step'] = array('label' => Yii::t('ResultInfo', 'result_step'),
            'isBasic' => true);
        $searchFieldConfig['repeat_step'] = array('label' => Yii::t('BugInfo', 'repeat_step'),
            'isBasic' => true);
        $searchFieldConfig['action_note'] = array('label' => Yii::t('Common', 'action_note'),
            'isBasic' => false);
        $showFieldArr[] = 'action_note';
        $content = ExportService::exportXML($rawData, $showFieldArr, $searchFieldConfig);
        file_put_contents($fileStoreUrl, $content);
        header('Content-type: text/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $infoType . 'list.xml');
        readfile($fileStoreUrl);
//      exit; //enable this will not output log
    }

    protected function getInfoType()
    {
        if(isset($_GET['type']) && in_array($_GET['type'], array('bug', 'case', 'result')))
        {
            $infoType = $_GET['type'];
            return $infoType;
        }
        else
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    private function setShowFieldCookie($fieldCookieKey, $showFieldStr)
    {
        $fieldCookie = new CHttpCookie($fieldCookieKey, $showFieldStr);
        $fieldCookie->expire = time() + 60 * 60 * 24 * 14;  
        Yii::app()->request->cookies[$fieldCookieKey] = $fieldCookie;
    }

    private function getShowFieldCookie($fieldCookieKey)
    {
        $showFieldStr = '';
        $cookies = Yii::app()->request->getCookies();
        if(!empty($cookies[$fieldCookieKey]))
        {
            $showFieldCookie = $cookies[$fieldCookieKey];
            $showFieldCookie->expire = time() + 60 * 60 * 24 * 14;
            $showFieldStr = $showFieldCookie->value;
            Yii::app()->request->cookies[$fieldCookieKey] = $showFieldCookie;
        }
        return $showFieldStr;
    }

    private function getModuleId($productId, $infoType)
    {
        $productModuleId = 0;
        $moduleCookieKey = $productId . '_' . $infoType . '_module_id';
        if(isset($_GET['productmodule_id']))
        {
            $productModuleId = $_GET['productmodule_id'];
            Yii::app()->user->setState($productId . '_' . $infoType . '_selectedModule', $_GET['productmodule_id']);
            $moduleIdCookie = new CHttpCookie($moduleCookieKey, $_GET['productmodule_id']);
            $moduleIdCookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
            Yii::app()->request->cookies[$moduleCookieKey] = $moduleIdCookie;
        }
        else
        {
            if(!empty($_POST['reset']) || isset($_GET['query_id']))//reset
            {
                Yii::app()->user->setState($productId . '_' . $infoType . '_selectedModule', null);
                $this->clearModuleIdCookie($productId, $infoType);
            }
            else
            {
                $cookies = Yii::app()->request->getCookies();
                if(!empty($cookies[$moduleCookieKey]))
                {
                    $productModuleId = $cookies[$moduleCookieKey]->value;
                    $moduleInfo = ProductModule::model()->findByPk($productModuleId);
                    if(empty($moduleInfo) || $productId != $moduleInfo['product_id'])
                    {
                        $productModuleId = 0;
                        unset($cookies[$moduleCookieKey]);
                    }
                }
            }
        }
        return $productModuleId;
    }

    private function clearModuleIdCookie($productId, $infoType)
    {
        $moduleCookieKey = $productId . '_' . $infoType . '_module_id';
        $cookies = Yii::app()->request->getCookies();
        if(!empty($cookies[$moduleCookieKey]))
        {
            unset($cookies[$moduleCookieKey]);
        }
    }

    protected function getShowFieldArr($defaultShowFieldArr, $infoType, $product)
    {
        $fieldCookieKey = $product . '_' . $infoType . '_showField';
        $showFieldArr = $defaultShowFieldArr;

        if(!empty($_POST['showField']))
        {
            $this->setShowFieldCookie($fieldCookieKey, $_POST['showField']);
            $showFieldArr = CommonService::splitStringToArray(',', $_POST['showField']);
        }
        else
        {
            $cookieShowFieldStr = $this->getShowFieldCookie($fieldCookieKey);
            if(!empty($cookieShowFieldStr) && is_string($cookieShowFieldStr))
            {
                $showFieldArr = CommonService::splitStringToArray(',', $cookieShowFieldStr);
            }
        }
        return $showFieldArr;
    }

    protected function getProductId($infoType)
    {
        if(isset($_GET['product_id']))
        {
            $productId = $_GET['product_id'];
            if(!Info::isProductAccessable($productId))
            {
                throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
            }
            Yii::app()->user->setState('product', $productId);
            TestUserService::updateUserProductCookie($productId);
            return $productId;
        }
        else
        {
            throw new CHttpException(400, Yii::t('Common', 'Required URL not found or permission denied.'));
        }
    }

    protected static function getQueryTitle($productId, $infoType)
    {
        $queryTitle = '';
        if(!empty($_POST['reset']))//reset
        {
            $queryTitle = '';
            Yii::app()->user->setState($productId . '_' . $infoType . '_queryTitle', null);
        }
        elseif(isset($_POST['queryTitle']))
        {
            $queryTitle = $_POST['queryTitle'];
        }
        elseif(isset($_GET['query_id']))
        {
            if('-1' == $_GET['query_id'])
            {
                $queryTitle = Yii::t('Common', 'Opened by me');
            }
            elseif('-2' == $_GET['query_id'])
            {
                $queryTitle = Yii::t('Common', 'Assigned to me');
            }
            elseif('-3' == $_GET['query_id'])
            {
                $queryTitle = Yii::t('Common', 'Marked by me');
            }
            elseif('-4' == $_GET['query_id'])
            {
                $queryTitle = Yii::t('Common', 'Mailed to me');
            }
            else
            {
                $savedQuery = UserQueryService::getQueryConditionById($_GET['query_id']);
                if(CommonService::$ApiResult['FAIL'] == $savedQuery['status'])
                {
                    $queryTitle = '';
                }
                else
                {
                    $queryTitle = $savedQuery['detail']['title'];
                }
            }
        }
        else
        {
            $sessionQueryTitle = Yii::app()->user->getState($productId . '_' . $infoType . '_queryTitle');
            if($sessionQueryTitle !== null)
            {
                $queryTitle = $sessionQueryTitle;
            }
        }
        Yii::app()->user->setState($productId . '_' . $infoType . '_queryTitle', $queryTitle);
        return $queryTitle;
    }

    protected function getSavedSearchRow($productId, $infoType, $queryId)
    {
        Yii::app()->user->setState($productId . '_' . $infoType . '_filterSql', null);
        Yii::app()->user->setState($productId . '_' . $infoType . '_filterColumn', null);
        if('-1' == $_GET['query_id'])
        {
            $savedSearchRow = UserQueryService::getOpenByMeQuery();
        }
        elseif('-2' == $_GET['query_id'])
        {
            $savedSearchRow = UserQueryService::getAssignToMeQuery();
        }
        elseif('-3' == $_GET['query_id'])
        {
            $savedSearchRow = UserQueryService::getMarkByMeQuery();
        }
        elseif('-4' == $_GET['query_id'])
        {
            $savedSearchRow = UserQueryService::getMailedToMeQuery();
        }
        else
        {
            $savedQuery = UserQueryService::getQueryConditionById($_GET['query_id']);
            if(CommonService::$ApiResult['FAIL'] == $savedQuery['status'])
            {
                $accessProductIdNameArr = Yii::app()->user->getState('visit_product_list');
                $productName = $accessProductIdNameArr[$productId];
                $savedSearchRow['search_condition'] = InfoService::getBlankSearchRowArr($productName, 0, $infoType);
            }
            else
            {
                $savedSearchRow = $savedQuery['detail'];
            }
        }
        $searchRowArr = $savedSearchRow['search_condition'];
        return $searchRowArr;
    }

    protected function getExpandClassName()
    {
        $expandSession = Yii::app()->user->getState('expand');
        if(!isset($expandSession))
        {
            $expandClass = '';
        }
        else
        {
            if(1 == $expandSession)
            {
                $expandClass = '';
            }
            else
            {
                $expandClass = 'not_expand';
            }
        }
        return $expandClass;
    }

    //this field should always be returned for the further use
    protected function getSqlSelectFieldArr($infoType, $showFieldArr)
    {
        $showFieldArr[] = Info::MARK;
        $showFieldArr[] = 'product_id';
        if(!in_array('id', $showFieldArr))
        {
            array_unshift($showFieldArr, 'id');
        }
        if('bug' == $infoType)
        {
            if(!in_array('bug_status', $showFieldArr))
            {
                array_unshift($showFieldArr, 'bug_status');
            }
        }

        if('result' == $infoType)
        {
            if(!in_array('result_value', $showFieldArr))
            {
                array_unshift($showFieldArr, 'result_value');
            }
        }
        return $showFieldArr;
    }

    private function getSavedQueryModuleId($searchRowArr, $productId, $infoType)
    {
        $productModuleId = 0;
        $moduleCookieKey = $productId . '_' . $infoType . '_module_id';
        if(('' == $searchRowArr[0]['leftParenthesesName']) &&
                ('module_name' == $searchRowArr[0]['field']) &&
                ('' == $searchRowArr[0]['rightParenthesesName']) &&
                ('UNDER' == $searchRowArr[0]['operator']) &&
                ('And' == $searchRowArr[0]['andor']))
        {
            $moduleFullPathName = $searchRowArr[0]['value'];
            $moduleSplitterPos = strpos($moduleFullPathName, ProductModule::MODULE_SPLITTER);
            if(false !== $moduleSplitterPos)
            {
                $moduleName = substr($moduleFullPathName, $moduleSplitterPos + 1);
                $moduleInfo = ProductModule::model()->findByAttributes(array('product_id' => $productId, 'full_path_name' => $moduleName));
                if(!empty($moduleInfo))
                {
                    $productModuleId = $moduleInfo->id;
                }
            }            
            Yii::app()->user->setState($productId . '_' . $infoType . '_selectedModule', $productModuleId);
            $moduleIdCookie = new CHttpCookie($moduleCookieKey, $productModuleId);
            $moduleIdCookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
            Yii::app()->request->cookies[$moduleCookieKey] = $moduleIdCookie;
        }
        return $productModuleId;
    }

    private function setSpecialModuleCondiftion($searchRowArr, $productName)
    {
        if(('' != $searchRowArr[0]['leftParenthesesName']) ||
                ('module_name' != $searchRowArr[0]['field']) ||
                ('' != $searchRowArr[0]['rightParenthesesName']) ||
                ('UNDER' != $searchRowArr[0]['operator']) ||
                ('And' != $searchRowArr[0]['andor']))
        {
            $blankSearchRowArr = InfoService::getBlankSearchRowArr($productName, 0);
            array_unshift($searchRowArr, $blankSearchRowArr[0]);
        }
        return $searchRowArr;
    }

    public function actionIndex()
    {
        $this->layout = '//layouts/index';
        $expandClass = $this->getExpandClassName();
        $infoType = $this->getInfoType();
        $productId = $this->getProductId($infoType);
        $accessProductIdNameArr = Yii::app()->user->getState('visit_product_list');
        $productName = $accessProductIdNameArr[$productId];
        $defaultShowFieldArr = SearchService::getDefaultShowFieldArr($infoType);
        $showFieldArr = $this->getShowFieldArr($defaultShowFieldArr, $infoType, $productId);
        $queryTitle = $this->getQueryTitle($productId, $infoType);
        $productModuleId = $this->getModuleId($productId, $infoType);
        $searchRowArr = InfoService::getBlankSearchRowArr($productName, $productModuleId, $infoType);
        $searchFieldConfig = SearchService::getSearchableFields($infoType, $productId);
        $defaultSelectFieldOptionStr = SearchService::getSelectFieldsOption($searchFieldConfig, $defaultShowFieldArr);
        $defaultShowFieldOptionStr = SearchService::getShowFieldsOption($searchFieldConfig, $defaultShowFieldArr);
        
        $templateStr = InfoService::getTemplateStr($productId, $infoType, Yii::app()->user->id);
        $leftMenu = UserQueryService::getQueryLinkStr($productId, $infoType, Yii::app()->user->id);

        //not request from post query, save query and reset query
        $filterSql = '';
        $filterColumn = '';
        if(!empty($_POST['queryaction']))
        {
            Yii::app()->user->setState($productId . '_' . $infoType . '_filterSql', null);
            Yii::app()->user->setState($productId . '_' . $infoType . '_filterColumn', null);
            if(!empty($_POST['reset']))//reset
            {
                Yii::app()->user->setState($productId . '_' . $infoType . '_showField', null);
                Yii::app()->user->setState($productId . '_' . $infoType . '_search', null);
            }
            else //save or reset query
            {
                if(!empty($_POST['saveQuery'])) //save query
                {
                    $result = UserQueryService::editUserQuery($queryTitle, $productId, $infoType, $_POST[Info::QUERY_GROUP_NAME]);
                    if(CommonService::$ApiResult['FAIL'] == $result['status'])
                    {
                        CommonService::jsAlert(Yii::t('Common', 'save query fail'));
                    }
                    CommonService::testRefreshSelf();
                }
                if(isset($_POST[Info::QUERY_GROUP_NAME]))
                {
                    $searchCondition = $_POST[Info::QUERY_GROUP_NAME];
                    $searchRowArr = SearchService::getSearchConditionArr($searchCondition);
                }
            }
        }
        else//search by get method(pager or link)
        {
            if(isset($_GET['query_id'])) //load from saved query
            {
                $searchRowArr = $this->getSavedSearchRow($productId, $infoType, $_GET['query_id']);
                Yii::app()->user->setState($productId . '_' . $infoType . '_filterSql', null);
                Yii::app()->user->setState($productId . '_' . $infoType . '_filterColumn', null);
                $productModuleId = $this->getSavedQueryModuleId($searchRowArr, $productId, $infoType);
            }
            else
            {
                $sessionSearchRowArr = Yii::app()->user->getState($productId . '_' . $infoType . '_search');
                if($sessionSearchRowArr !== null)
                {
                    $searchRowArr = $sessionSearchRowArr;
                }
            }
            if(!empty($_GET['filter']))
            {
                $filterArr = CommonService::splitStringToArray('|', $_GET['filter']);
                if(Info::$InputType['date'] == $searchFieldConfig[$filterArr[0]]['type'])
                {
                    $filterSql = $filterArr[0] . " like '" . addslashes($filterArr[1]) . "%'";
                }
                else
                {
                    $filterSql = $filterArr[0] . " = '" . addslashes($filterArr[1]) . "'";
                }
                $filterColumn = $filterArr[0];
                Yii::app()->user->setState($productId . '_' . $infoType . '_filterSql', $filterSql);
                Yii::app()->user->setState($productId . '_' . $infoType . '_filterColumn', $filterArr[0]);
            }
            else
            {
                $sessionFilterColumn = Yii::app()->user->getState($productId . '_' . $infoType . '_filterColumn');
                if($sessionFilterColumn !== null)
                {
                    $filterSql = Yii::app()->user->getState($productId . '_' . $infoType . '_filterSql');
                    $filterColumn = $sessionFilterColumn;
                }
            }
        }
        
        if(isset($_GET['productmodule_id']))
        {
            $searchRowArr[0]['leftParenthesesName'] = '';
            $searchRowArr[0]['field'] = 'module_name';
            $searchRowArr[0]['rightParenthesesName'] = '';
            $searchRowArr[0]['operator'] = 'UNDER';
            $searchRowArr[0]['andor'] = 'And';
            $searchRowArr[0]['value'] = '';
            $selectedModuleInfo = ProductModule::model()->findByPk($_GET['productmodule_id']);
            if($selectedModuleInfo !== null)
            {
                $searchRowArr[0]['value'] = $productName . '/' . $selectedModuleInfo->full_path_name;
            }
            else
            {
                $searchRowArr[0]['value'] = $productName;
            }
        }

        $searchRowArr = $this->setSpecialModuleCondiftion($searchRowArr, $productName);
        Yii::app()->user->setState($productId . '_' . $infoType . '_search', $searchRowArr);
        $getSqlResult = SqlService::baseGetGroupQueryStr($searchFieldConfig, $infoType, $searchRowArr);
        if(CommonService::$ApiResult['FAIL'] == $getSqlResult['status'])
        {
            $whereStr = ' 1<>1 ';
        }
        else
        {
            $whereStr = $getSqlResult['detail'];
        }
        if('' != $filterSql)
        {
            $whereStr .= ' and ' . $filterSql;
        }
        $selectFieldOptionStr = SearchService::getSelectFieldsOption($searchFieldConfig, $showFieldArr);
        $showFieldOptionStr = SearchService::getShowFieldsOption($searchFieldConfig, $showFieldArr);

        $defaultSelectFieldOptionStr = addslashes($defaultSelectFieldOptionStr);
        $defaultSelectFieldOptionStr = str_replace(array("\r\n", "\r", "\n"), "", $defaultSelectFieldOptionStr);
        $defaultShowFieldOptionStr = addslashes($defaultShowFieldOptionStr);
        $defaultShowFieldOptionStr = str_replace(array("\r\n", "\r", "\n"), "", $defaultShowFieldOptionStr);
        $jsValueStr = SearchService::getJsValueOption($this, $searchFieldConfig);
        $jsOperatorStr = SearchService::getJsOperatorOption($searchFieldConfig);
        $searchConditionHtml = SearchService::getSearchHtml($infoType, $searchFieldConfig, $searchRowArr);
        $searchConditionHtmlTemplate = SearchService::getSearchConditionRowHtml(
                        Info::TEMPLATE_NUMBER,
                        $searchFieldConfig,
                        InfoService::getTemplateSearchRowArr());
        $searchConditionHtmlTemplate = addslashes($searchConditionHtmlTemplate);
        $searchConditionHtmlTemplate = str_replace(array("\r\n", "\r", "\n"), "", $searchConditionHtmlTemplate);

        $showType = Yii::app()->user->getState($productId . '_' . $infoType . '_showtype');
        if(empty($showType))
        {
            $showType = Info::SHOW_TYPE_GRID;
        }
        $relatedFields = $this->getRelatedFields($searchRowArr, $showFieldArr);
        $isAllBasicField = SearchService::isAllBasicField(SearchService::getBasicFieldArr($infoType),
                        $relatedFields);
        $renderArr = array(
            'expandClass' => $expandClass,
            'productId' => $productId,
            'productModuleId' => $productModuleId,
            'infoType' => $infoType,
            'searchFieldConfig' => $searchFieldConfig,
            'selectFieldOptionStr' => $selectFieldOptionStr,
            'showFieldOptionStr' => $showFieldOptionStr,
            'defaultSelectFieldOptionStr' => $defaultSelectFieldOptionStr,
            'defaultShowFieldOptionStr' => $defaultShowFieldOptionStr,
            'searchConditionHtml' => $searchConditionHtml,
            'searchConditionHtmlTemplate' => $searchConditionHtmlTemplate,
            'queryTitle' => $queryTitle,
            'jsValueStr' => $jsValueStr,
            'jsOperatorStr' => $jsOperatorStr,
            'leftMenu' => $leftMenu,
            'templateStr' => $templateStr,
            'showType' => $showType,
            'showMyQueryDiv' => Yii::app()->user->getState('my_query_div')
        );

        if($showType == Info::SHOW_TYPE_GRID)
        {
            $renderArr = array_merge($renderArr,
                            $this->getGridShowContent($searchFieldConfig,
                                    $showFieldArr, $infoType, $productId,
                                    $filterColumn, $whereStr, $isAllBasicField));
            $renderArr['rowCssClassExpressionStr'] = InfoService::getRowCssClassExpressionStr($infoType);
        }

        $this->render('index', $renderArr);
    }

    private function getRelatedFields($searchRowArr, $showFieldArr)
    {
        $fieldArr = array();
        foreach($searchRowArr as $searchRow)
        {
            $fieldArr[] = $searchRow['field'];
        }
        return array_merge($fieldArr, $showFieldArr);
    }

    private function getGridShowContent($searchFieldConfig, $showFieldArr, $infoType, $productId, $filterColumn, $whereStr, $isAllBasicField = false)
    {
        $pageSize = CommonService::getPageSize();
        $viewColumnArr = SearchService::getViewColumnArr($searchFieldConfig, $showFieldArr, $infoType, $productId, $filterColumn);
        $totalNum = SqlService::getTotalFoundNum($infoType, $productId, $whereStr, $isAllBasicField);

        $sql = SqlService::getRawDataSql($searchFieldConfig, $infoType,
                        $productId, $this->getSqlSelectFieldArr($infoType, $showFieldArr), $whereStr, $isAllBasicField);

        $dataProvider = new CSqlDataProvider($sql, array(
                    'totalItemCount' => $totalNum,
                    'sort' => array(
                        'defaultOrder' => array(
                            'id' => true,
                        ),
                        'multiSort' => true,
                        'attributes' => array_merge($showFieldArr, array(Info::MARK))
                    ),
                    'pagination' => array(
                        'pageSize' => $pageSize,
                    ),
                ));
        $sortArr = $dataProvider->getSort()->getDirections();
        Yii::app()->user->setState($productId . '_' . $infoType . '_sortArr', $sortArr);

        $preNextSessionSql = SqlService::getPreNextSql($searchFieldConfig, $infoType, $productId, $whereStr,
                        Yii::app()->user->getState($productId . '_' . $infoType . '_sortArr', $isAllBasicField));
        Yii::app()->user->setState($productId . '_' . $infoType . '_prenextsql', $preNextSessionSql);
        return array('viewColumnArr' => $viewColumnArr,
            'dataProvider' => $dataProvider,
            'totalNum' => $totalNum);
    }

}

?>