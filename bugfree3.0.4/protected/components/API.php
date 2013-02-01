<?php
/**
 * This is BugFree API class
 *
 * @package bugfree.protected.components
 */
class API
{
    const API_RAND_KEY = '_bugfree_api_rand';
    const API_KEY = '';
    
    const ERROR_NONE = 0;
    const ERROR_MODE_EMPTY = 1;
    const ERROR_MODE_NOT_FOUNT = 2;
    const ERROR_SESSION_ID_EMPTY = 3;
    const ERROR_USER_AUTH_FAILED = 4;
    const ERROR_NOT_LOGIN = 5;
    const ERROR_PERMISSION_DEINED = 6;
    const ERROR_SAVE_INFO = 7;
    const ERROR_GET_INFO = 8;
    const ERROR_XML_INVALID = 9;
    const ERROR_QUERY_EMPTY = 10;
    const ERROR_QUERY = 11;
    const ERROR_PRODUCT_EMPTY = 12;
    const ERROR_NOT_SUPPORT_CSV = 13;
    const ERROR_USER_EMPTY = 14;
    const ERROR_MODULE_EMPTY = 15;
    const ERROR_DATE_FORMAT = 16;
    const ERROR_REOPEN_NUMBER = 17;
    
    const XML_FIELDS = 'fields';
    const XML_NAME = 'name';
    const XML_OPERATOR = 'operator';
    const XML_VALUE = 'value';
    const XML_LOGIC = 'logic';
    
    static $OPERATOR = array(
        'GT' => '>',
        'LT' => '<',
        'GE' => '>=',
        'LE' => '<=',
        'EQ' => '=',
        'NEQ' => '!=',
        'LIKE' => 'LIKE',
        'NOTLIKE' => 'NOT LIKE',
        'IN' => 'IN',
    );
    /**
     * This is encrypt function
     * 
     * @internal
     * @return string
     */
    private function encrypt($username, $password, $rand)
    {
        return md5(md5($username . $password) . API::API_KEY . $rand);
    }
    
    /**
     * This is login function
     * 
     * @param string $sessionId
     * @param string $username
     * @param string $auth
     * @return array
     */
    public function login($sessionId, $username, $auth)
    {
        $timeout = time();
        $session = Yii::app()->getSession();
        $session->close();
        $session->setSessionID($sessionId);
        $session->open();
        $code = API::ERROR_USER_AUTH_FAILED;
        $info = Yii::t('API', 'user authenticate failed error info');
        $rand = $session->get(API::API_RAND_KEY);
        $user = TestUser::model()->findByAttributes(array('username' => $username));
        if(null !== $user)
        {
            if($auth == $this->encrypt($username, $user->password, $rand))
            {
                $code = API::ERROR_NONE;
                $info = '';
                $identity = new UserIdentity($username, $user->password);
                $identity->errorCode = UserIdentity::ERROR_NONE;
                $identity->setState('id', $user->id);
                $identity->setState('username', $username);
                Yii::app()->user->login($identity, 0);
                $timeout += $session->getTimeout();
                LoginService::setUserInfo();
                /*
                 * CWebUser::login() function will call sesssion_regenrate_id() function
                 * I dont konw why to do that right now and store the data to old session
                 */
                $data = $_SESSION;
                $session->close();
                $session->setSessionID($sessionId);
                $session->open();
                foreach($data as $key => $val)
                {
                    $_SESSION[$key] = $val;
                }
            }
        }
        
        return array($code, $info, $timeout);
    }
    
    /**
     * This is to get the api rand function
     * 
     * Get the api rand for api authenticating
     * @return array
     */
    public function getApiRand()
    {
        $sessionId = Yii::app()->getSession()->getSessionID();
        $rand = substr(md5(mt_rand()),1,5);
        Yii::app()->getSession()->add(API::API_RAND_KEY, $rand);
        
        return array($sessionId, $rand);
    }
    
    /**
     * This is to verify function
     * 
     * @param string $sessionId  session id
     * @return array
     */
    public function verify($sessionId)
    {
        $code = API::ERROR_NOT_LOGIN;
        $info = Yii::t('API', 'not login error info');
        if(!empty($sessionId))
        {
            $session = Yii::app()->getSession();
            $session->close();
            $session->setSessionID($sessionId);
            $session->open();
            Yii::app()->getRequest()->getCookies()->remove(Yii::app()->user->getStateKeyPrefix());
            if(null !== Yii::app()->user->id)
            {
                $code = API::ERROR_NONE;
                $info = '';
            }
        }
        
        return array($code, $info);
    }
    
    /**
     * This is get info function, via info id and type
     * 
     * @param string $lowerItem
     * @param integer $id
     * @param boolean $compatible
     * @return type 
     */
    public function getInfo($lowerItem, $id, $compatible = false)
    {
        $code = API::ERROR_NONE;
        $result = @InfoService::loadRawData($lowerItem, $id);
        $detail = $result['detail'];
        if(CommonService::$ApiResult['FAIL'] === $result['status'])
        {
            $code = API::ERROR_SAVE_INFO;
            $info = $detail;
        }
        else
        {
            $info = $detail->getBasicInfo()->attributes + $detail->getCustomInfo();
            $info['repeat_step'] = CHtml::encode($info['repeat_step']);
            $actionList = ActionHistoryService::getInfoActionForApi($lowerItem, $id, $detail->getBasicInfo()->product_id);
            $info['action_list'] = $actionList;
            if($compatible)
            {
                foreach($info as $key => $val)
                {
                    unset($info[$key]);
                    $key = $this->fieldNew2Old($key, $lowerItem);
                    $info[$key] = $val;
                }
            }
            else
            {
                $info['attachment_file'] = array();
                foreach($detail->getBasicInfo()->attachment_file as $val)
                {
                    unset($val['is_dropped']);
                    unset($val['target_id']);
                    unset($val['target_type']);
                    unset($val['add_action_id']);
                    unset($val['delete_action_id']);
                    $info['attachment_file'][] = $val;
                }
            }
        }
        return array($code, $info);
    }
    
    /**
     * This is save info function, use it whatever saving or updating 
     * 
     * @param string $lowerItem
     * @param array $fields
     * @param array $systemFields
     * @param string $actionType
     * @return array 
     */
    public function saveInfo($lowerItem, $fields, $systemFields, $actionType, $compatible = false)
    {
        $item = ucfirst($lowerItem);
        $modelName = $item . 'InfoView';
        $model = new $modelName();
        $basicInfoFields = array_keys($model->attributes);
        $basicInfoFields[] = 'action_note'; 
        $basicInfo = array();
        $customInfo = array();
        $isNeedBBCodeTransfer = true;
        if(isset($fields['no_bbcode_transfer'])&&!empty($fields['no_bbcode_transfer']))
        {
            $isNeedBBCodeTransfer = false;
        }
        foreach($fields as $key => $field)
        {
            if(in_array($key, $systemFields))
            {
                continue;
            }
            
            if($compatible)
            {
                if('AssignedTo' ==  $key || 'ScriptedBy' == $key)
                {
                    $field = $this->getRealNameByName($field);
                }
                else if('MailTo' == $key)
                {
                    $field = $this->getRealNamesByMailTo($field);
                }
                $key = $this->fieldOld2New($key, $lowerItem);
            }
            
            if($isNeedBBCodeTransfer && in_array($key, array('action_note', 'repeat_step', 'case_step', 'result_step')))
            {
                $field = BBCode::bbcode2html($field);
            }
            
            if(('no_bbcode_transfer' !=$key) && !in_array($key, $basicInfoFields))
            {
                $customInfo[$key] = $field;
                continue;
            }
            $basicInfo[$key] = $field;
        }
        
        if(Info::ACTION_OPEN == $actionType && isset($basicInfo['id']))
        {
            unset($basicInfo['id']);
        }
        
        if(Info::ACTION_OPEN_EDIT == $actionType 
                && 'bug' == $lowerItem 
                && isset($basicInfo['id']))
        {
            $bug = BugInfo::model()->findByPk($basicInfo['id']);
            if(!isset($basicInfo['bug_status']))
            {
                $basicInfo['bug_status'] = $bug->bug_status;
            }
            if(null !== $bug)
            {
                switch ($basicInfo['bug_status'])
                {
                    case BugInfo::STATUS_ACTIVE: {
                        if(BugInfo::STATUS_ACTIVE !== $bug->bug_status)
                        {
                            $actionType = BugInfo::ACTION_ACTIVATE;
                        }
                        else
                        {
                            $actionType = BugInfo::ACTION_OPEN_EDIT;
                        }
                        break;
                    }
                    case BugInfo::STATUS_RESOLVED: {
                        if(BugInfo::STATUS_RESOLVED !== $bug->bug_status)
                        {
                            $actionType = BugInfo::ACTION_RESOLVE;
                        }
                        else
                        {
                            $actionType = BugInfo::ACTION_RESOLVE_EDIT;
                        }
                        break;
                    }
                    case BugInfo::STATUS_CLOSED: {    
                        if(BugInfo::STATUS_CLOSED !== $bug->bug_status)
                        {
                            $actionType = BugInfo::ACTION_CLOSE;
                        }
                        else
                        {
                            $actionType = BugInfo::ACTION_CLOSE_EDIT;
                        }
                        break;  
                    }
                    default: {
                        break;
                    }
                }
            }
        }
        
        $code = API::ERROR_NONE;
        $attachmentFile = CUploadedFile::getInstancesByName('attachment_file');
        $result = InfoService::saveInfo($lowerItem, $actionType, $basicInfo, $customInfo, $attachmentFile);
        $info = $result['detail'];
        if(CommonService::$ApiResult['FAIL'] === $result['status'])
        {
            $code = API::ERROR_SAVE_INFO;
        }
        
        return array($code, $info);
    }
    
    /**
     * get query
     * 
     * @param integer $queryId
     * @param boolean $compatible
     * @return array 
     */
    public function getQuery($queryId, $page = 1, $size = 100, $compatible = false)
    {        
        $code = API::ERROR_NONE;
        $info = '';
        if(empty($queryId))
        {
            $code = API::ERROR_QUERY_EMPTY;
            $info = Yii::t('API', 'query id empty error info');
        }
        $result = UserQueryService::getQueryConditionById($queryId);
        if(CommonService::$ApiResult['FAIL'] == $result['status'])
        {
            $code = API::ERROR_QUERY;
            $info = $result['detail'];
        }
        else
        {
            $savedSearchCondition = $result['detail'];
            $result = ExportService::getExportData($savedSearchCondition['query_type'],
                            $savedSearchCondition['search_condition'],
                            $savedSearchCondition['product_id'],
                            null, null, null, $size, $page);
            $detail = $result['detail'];
            if(CommonService::$ApiResult['FAIL'] == $result['status'])
            {
                $code = API::ERROR_QUERY;
                $info = $detail;
            }
            else
            {
                $lowerItem = strtolower($savedSearchCondition['query_type']);
                $itemList = ucfirst($lowerItem) . 'List';
                $idList = ucfirst($lowerItem) . 'IDs';
                $list = array();
                foreach($detail as $val)
                {
                    $id = $val['id'];
                    if($compatible)
                    {
                        foreach($val as $key => $field)
                        {
                            unset($val[$key]);
                            $key = $this->fieldNew2Old($key, $lowerItem);
                            $val[$key] = $field;
                        }
                    }
                    $list[$id] = $val;
                }
                
                $info[$itemList] = $list;
                $info[$idList] = join(',', array_keys($list));
                $info['page'] = $page;
                $info['size'] = $size;
                $info['type'] = $lowerItem;
            }
        }
        
        return array($code, $info);
    }
    
    /**
     * query
     * 
     * @param string $xml
     * @param string $schema
     * @return array 
     */
    public function query($xml, $schema, $productId, $compatible = false)
    {
        $code = API::ERROR_NONE;
        $info = '';
        if(empty($xml))
        {
            $code = API::ERROR_QUERY_EMPTY;
            $info = Yii::t('API', 'query empty error info');
        }
        else
        {
            Yii::app()->request->stripSlashes($xml);
            $xml = preg_replace("/>\s+</","><",$xml);
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            if($dom->schemaValidateSource($schema))
            {
                $query = $dom->getElementsByTagName('query');
                $query = $query->item(0);
                $infoType = strtolower($query->getAttribute('table'));
                $showFieldArr = $query->getAttribute('select') ? explode(',', $query->getAttribute('select')) : null;
                $order = $query->getAttribute('order') ? $query->getAttribute('order') : null;
                $isAsc = $query->getAttribute('asc') ? $query->getAttribute('asc') : false;
                $orderArr = $order ? array($order => $isAsc) : null;
                $currentPage = $query->getAttribute('page') ? $query->getAttribute('page') : 1;
                $pageSize = $query->getAttribute('size') ? $query->getAttribute('size') : 100;
                $arr = array();
                $searchRowArr = $this->getSearchRowArr($query->firstChild, $arr, $infoType, $compatible);
                $result = ExportService::getExportData($infoType, $searchRowArr, $productId, $showFieldArr, $orderArr, null, $pageSize, $currentPage);
                $detail = $result['detail'];
                if(CommonService::$ApiResult['FAIL'] == $result['status'])
                {
                    $code = API::ERROR_QUERY;
                    $info = $detail;
                }
                else
                {
                    $list = array();
                    if('count(*)' == $showFieldArr[0])
                    {
                        $list = $detail;
                    }
                    else
                    {
                        foreach($detail as $val)
                        {
                            $id = $val['id'];
                            if($compatible)
                            {
                                foreach($val as $key => $field)
                                {
                                    unset($val[$key]);
                                    $key = $this->fieldNew2Old($key, $infoType);
                                    $val[$key] = $field;
                                }
                            }
                            $list[$id] = $val;
                        }
                    }
                    
                    $info['QueryList'] = $list;
                    $info['size'] = $pageSize;
                    $info['page'] = $currentPage;
                }
            }
            else
            {
                $code = API::ERROR_QUERY_EMPTY;
                $info = Yii::t('API', 'query xml invalid info');
            }
        }
        
        return array($code, $info);
    }
    
    /**
     * find products
     * 
     * @param string $productId
     * @param string $productName
     * @return array 
     */
    public function findProducts($productId, $productName)
    {
        $code = API::ERROR_NONE;
        $accessProductIds = array();
        $accessProducts = TestUserService::getAccessableProduct(Yii::app()->user->id);
        foreach($accessProducts as $accessProduct)
        {
            $accessProductIds[] = $accessProduct['id'];
        }
        
        $condition = new CDbCriteria();
        $condition->compare('id', $productId);
        $condition->compare('name', $productName, true);
        $condition->addInCondition('id', $accessProductIds);
        $products = Product::model()->findAllByAttributes(array(), $condition);
        $list = array();
        foreach($products as $product)
        {
            $list[] = array('id' => $product->id, 'name' => $product->name);
        }
        $info['ProductList'] = $list;
        
        return array($code, $info);
    }
    
    
    /**
     * find modules
     * 
     * @param type $productId
     * @param type $moduleId
     * @param type $moduleName
     * @return type 
     */
    public function findModules($productId, $moduleId, $moduleName)
    {        
        $code = API::ERROR_NONE;
        $info = '';
        if(empty($productId))
        {
            $code = API::ERROR_PRODUCT_EMPTY;
            $info = Yii::t('API', 'product id empty error info');
        }
        else
        {
            $accessProductIds = array();
            $accessProducts = TestUserService::getAccessableProduct(Yii::app()->user->id);
            foreach($accessProducts as $accessProduct)
            {
                $accessProductIds[] = $accessProduct['id'];
            }

            $condition = new CDbCriteria();
            $condition->compare('id', $moduleId);
            $condition->compare('name', $moduleName, true);
            $condition->compare('product_id', $productId);
            $condition->addInCondition('product_id', $accessProductIds);
            $modules = ProductModule::model()->findAllByAttributes(array(), $condition);
            $list = array();
            foreach($modules as $module)
            {
                $list[] = array(
                    'id' => $module->id,
                    'name' => $module->name,
                    'product_id' => $module->product_id,
                    'grade' => $module->grade,
                    'parent_id' => $module->parent_id,
                    'full_path_name' => $module->full_path_name,
                );
            }
            $info['ModuleList'] = $list; 
        }

        return array($code, $info);
    }

    /**
     * find user by realname
     *
     * @param string $realName
     * @return array find user info array
     */
    public function findUser($realName)
    {
        $code = API::ERROR_NONE;
        $info = '';
        if(empty($realName))
        {
            $code = API::ERROR_USER_EMPTY;
            $info = Yii::t('API', 'user realname empty error info');
        }
        else
        {
            $userInfo = TestUserService::getUserInfoByRealname($realName);
            if($userInfo != null)
            {
                $info['userinfo'] = $userInfo->attributes;
                unset ($info['userinfo']['password']);
            }
            else
            {
                $info['userinfo'] = '';
            }
        }

        return array($code, $info);
    }
    
    /**
     * This is translate old field name to new one
     * 
     * @param stirng $info
     * @param string $infoType
     * @return string
     */
    public function fieldOld2New($info, $infoType = 'bug')
    {
        switch($infoType)
        {        
            case 'bug': {
                $info = preg_replace('/^BugSeverity$/', 'severity', $info);
                $info = preg_replace('/^BugPriority$/', 'priority', $info);
                $info = preg_replace('/^BugSubStatus$/', 'bug_substatus', $info);
                $info = preg_replace('/^ModulePath$/', 'module_name', $info);
                $info = preg_replace('/^ProjectName$/', 'product_name', $info);
                $info = preg_replace('/^BugID$/', 'id', $info);
                $info = preg_replace('/^ProjectID$/', 'product_id', $info);
                $info = preg_replace('/^ModuleID$/', 'productmodule_id', $info);
                $info = preg_replace('/^BugTitle$/', 'title', $info);
                $info = preg_replace('/^ReproSteps$/', 'repeat_step', $info);
                $info = preg_replace('/^BugStatus$/', 'bug_status', $info);
                $info = preg_replace('/^LinkID$/', 'related_bug', $info);
                $info = preg_replace('/^CaseID$/', 'related_case', $info);
                $info = preg_replace('/^ResultID$/', 'related_result', $info);
                $info = preg_replace('/^DuplicateID$/', 'duplicate_id', $info);
                $info = preg_replace('/^MailTo$/', 'mail_to', $info);
                $info = preg_replace('/^Resolution$/', 'solution', $info);
                $info = preg_replace('/^OpenedBy$/', 'created_by_name', $info);
                $info = preg_replace('/^OpenedByID$/', 'created_by', $info);
                $info = preg_replace('/^OpenedDate$/', 'created_at', $info);
                $info = preg_replace('/^LastEditedBy$/', 'updated_by_name', $info);
                $info = preg_replace('/^LastEditedByID$/', 'updated_by', $info);
                $info = preg_replace('/^LastEditedDate$/', 'updated_at', $info);
                $info = preg_replace('/^AssignedTo$/', 'assign_to_name', $info);
                $info = preg_replace('/^AssignedToID$/', 'assign_to', $info);
                $info = preg_replace('/^ResolvedBy$/', 'resolved_by_name', $info);
                $info = preg_replace('/^ResolvedByID$/', 'resolved_by', $info);
                $info = preg_replace('/^ResolvedDate$/', 'resolved_at', $info);
                $info = preg_replace('/^ClosedBy$/', 'closed_by_name', $info);
                $info = preg_replace('/^ClosedByID$/', 'closed_by', $info);
                $info = preg_replace('/^ClosedDate$/', 'closed_at', $info);
                $info = preg_replace('/^ModifiedBy$/', 'modified_by_name', $info);
                $info = preg_replace('/^ModifiedBy/', 'modified_by', $info);
                $info = preg_replace('/^ReplyNote$/', 'action_note', $info);
                break;
            }
            case 'case': {
                $info = preg_replace('/^CasePriority/', 'priority', $info);
                $info = preg_replace('/^ModulePath$/', 'module_name', $info);
                $info = preg_replace('/^ProjectName$/', 'product_name', $info);
                $info = preg_replace('/^CaseID$/', 'id', $info);
                $info = preg_replace('/^ProjectID$/', 'product_id', $info);
                $info = preg_replace('/^ModuleID$/', 'productmodule_id', $info);
                $info = preg_replace('/^CaseTitle$/', 'title', $info);
                $info = preg_replace('/^CaseStatus$/', 'case_status', $info);
                $info = preg_replace('/^CaseSteps$/', 'case_step', $info);
                $info = preg_replace('/^OpenedBy$/', 'created_by_name', $info);
                $info = preg_replace('/^OpenedByID$/', 'created_by', $info);
                $info = preg_replace('/^OpenedDate$/', 'created_at', $info);
                $info = preg_replace('/^AssignedTo$/', 'assign_to_name', $info);
                $info = preg_replace('/^AssignedToID$/', 'assign_to', $info);
                $info = preg_replace('/^LastEditedBy$/', 'updated_by_name', $info);
                $info = preg_replace('/^LastEditedByID$/', 'updated_by', $info);
                $info = preg_replace('/^LastEditedDate$/', 'updated_at', $info);
                $info = preg_replace('/^ModifiedBy$/', 'modified_by_name', $info);
                $info = preg_replace('/^ModifiedBy/', 'modified_by', $info);
                $info = preg_replace('/^MailTo$/', 'mail_to', $info);
                $info = preg_replace('/^LinkID$/', 'related_case', $info);
                $info = preg_replace('/^BugID$/', 'related_bug', $info);
                $info = preg_replace('/^ResultID$/', 'related_result', $info);
                $info = preg_replace('/^ReplyNote$/', 'action_note', $info);
                break;
            }
            case 'result': {            
                $info = preg_replace('/^ModulePath$/', 'module_name', $info);
                $info = preg_replace('/^ProjectName$/', 'product_name', $info);
                $info = preg_replace('/^ResultID$/', 'id', $info);
                $info = preg_replace('/^ProjectID$/', 'product_id', $info);
                $info = preg_replace('/^ModuleID$/', 'productmodule_id', $info);
                $info = preg_replace('/^ResultTitle$/', 'title', $info);
                $info = preg_replace('/^ResultValue$/', 'result_value', $info);
                $info = preg_replace('/^ResultStatus$/', 'result_status', $info);
                $info = preg_replace('/^ResultSteps$/', 'result_step', $info);
                $info = preg_replace('/^CaseID$/', 'related_case_id', $info);
                $info = preg_replace('/^BugID$/', 'related_bug', $info);
                $info = preg_replace('/^MailTo$/', 'mail_to', $info);
                $info = preg_replace('/^OpenedBy$/', 'created_by_name', $info);
                $info = preg_replace('/^OpenedByID$/', 'created_by', $info);
                $info = preg_replace('/^OpenedDate$/', 'created_at', $info);
                $info = preg_replace('/^LastEditedBy$/', 'updated_by_name', $info);
                $info = preg_replace('/^LastEditedByID$/', 'updated_by', $info);
                $info = preg_replace('/^LastEditedDate$/', 'updated_at', $info);
                $info = preg_replace('/^AssignedTo$/', 'assign_to_name', $info);
                $info = preg_replace('/^AssignedToID$/', 'assign_to', $info);
                $info = preg_replace('/^ModifiedBy$/', 'modified_by_name', $info);
                $info = preg_replace('/^ModifiedBy/', 'modified_by', $info);
                $info = preg_replace('/^ReplyNote$/', 'action_note', $info);
                break;
            }
            default: {
                break;
            }
        }
        
        return $info;
    }
    
    /**
     * This is translate new field name to old one
     * 
     * @param string $info
     * @param string $infoType
     * @return string
     */
    public function fieldNew2Old($info, $infoType = 'bug')
    {
        switch($infoType)
        {
            case 'bug': {
                $info = preg_replace('/[ ]{0,1}priority[ ]{0,1}/', 'BugPriority', $info);
                $info = preg_replace('/[ ]{0,1}severity[ ]{0,1}/', 'BugSeverity', $info);
                $info = preg_replace('/[ ]{0,1}module_name[ ]{0,1}/', 'ModulePath', $info);
                $info = preg_replace('/[ ]{0,1}module_name[ ]{0,1}/', 'ModulePath', $info);
                $info = preg_replace('/[ ]{0,1}product_name[ ]{0,1}/', 'ProjectName', $info);
                $info = preg_replace('/[ ]{0,1}product_id[ ]{0,1}/', 'ProjectID', $info);
                $info = preg_replace('/[ ]{0,1}productmodule_id[ ]{0,1}/', 'ModuleID', $info);
                $info = preg_replace('/[ ]{0,1}title[ ]{0,1}/', 'BugTitle', $info);
                $info = preg_replace('/[ ]{0,1}repeat_step[ ]{0,1}/', 'ReproSteps', $info);
                $info = preg_replace('/[ ]{0,1}bug_status[ ]{0,1}/', 'BugStatus', $info);
                $info = preg_replace('/[ ]{0,1}related_bug[ ]{0,1}/', 'LinkID', $info);
                $info = preg_replace('/[ ]{0,1}related_case[ ]{0,1}/', 'CaseID', $info);
                $info = preg_replace('/[ ]{0,1}related_result[ ]{0,1}/', 'ResultID', $info);
                $info = preg_replace('/[ ]{0,1}duplicate_id[ ]{0,1}/', 'DuplicateID', $info);
                $info = preg_replace('/[ ]{0,1}mail_to[ ]{0,1}/', 'MailTo', $info);
                $info = preg_replace('/[ ]{0,1}solution[ ]{0,1}/', 'Resolution', $info);
                $info = preg_replace('/[ ]{0,1}created_by_name[ ]{0,1}/', 'OpenedBy', $info);
                $info = preg_replace('/[ ]{0,1}created_by[ ]{0,1}/', 'OpenedByID', $info);
                $info = preg_replace('/[ ]{0,1}created_at[ ]{0,1}/', 'OpenedDate', $info);
                $info = preg_replace('/[ ]{0,1}updated_by_name[ ]{0,1}/', 'LastEditedBy', $info);
                $info = preg_replace('/[ ]{0,1}updated_by[ ]{0,1}/', 'LastEditedByID', $info);
                $info = preg_replace('/[ ]{0,1}updated_at[ ]{0,1}/', 'LastEditedDate', $info);
                $info = preg_replace('/[ ]{0,1}assign_to_name[ ]{0,1}/', 'AssignedTo', $info);
                $info = preg_replace('/[ ]{0,1}assign_to[ ]{0,1}/', 'AssignedToID', $info);
                $info = preg_replace('/[ ]{0,1}resolved_by_name[ ]{0,1}/', 'ResolvedBy', $info);
                $info = preg_replace('/[ ]{0,1}resolved_by[ ]{0,1}/', 'ResolvedByID', $info);
                $info = preg_replace('/[ ]{0,1}resolved_at[ ]{0,1}/', 'ResolvedDate', $info);
                $info = preg_replace('/[ ]{0,1}closed_by_name[ ]{0,1}/', 'ClosedBy', $info);
                $info = preg_replace('/[ ]{0,1}closed_by[ ]{0,1}/', 'ClosedByID', $info);
                $info = preg_replace('/[ ]{0,1}closed_at[ ]{0,1}/', 'ClosedDate', $info);
                $info = preg_replace('/[ ]{0,1}modified_by_name[ ]{0,1}/', 'ModifiedBy', $info);
                $info = preg_replace('/[ ]{0,1}modified_by[ ]{0,1}/', 'ModifiedByID', $info);
                $info = preg_replace('/[ ]{0,1}id[ ]{0,1}/', 'BugID', $info);
                break;
            }
            case 'case': {
                $info = preg_replace('/[ ]{0,1}priority[ ]{0,1}/', 'CasePriority', $info);
                $info = preg_replace('/[ ]{0,1}module_name[ ]{0,1}/', 'ModulePath', $info);
                $info = preg_replace('/[ ]{0,1}product_name[ ]{0,1}/', 'ProjectName', $info);
                $info = preg_replace('/[ ]{0,1}product_id[ ]{0,1}/', 'ProjectID', $info);
                $info = preg_replace('/[ ]{0,1}productmodule_id[ ]{0,1}/', 'ModuleID', $info);
                $info = preg_replace('/[ ]{0,1}title[ ]{0,1}/', 'CaseTitle', $info);
                $info = preg_replace('/[ ]{0,1}case_status[ ]{0,1}/', 'CaseStatus', $info);
                $info = preg_replace('/[ ]{0,1}case_step[ ]{0,1}/', 'CaseSteps', $info);
                $info = preg_replace('/[ ]{0,1}created_by_name[ ]{0,1}/', 'OpenedBy', $info);
                $info = preg_replace('/[ ]{0,1}created_by[ ]{0,1}/', 'OpenedByID', $info);
                $info = preg_replace('/[ ]{0,1}created_at[ ]{0,1}/', 'OpenedDate', $info);
                $info = preg_replace('/[ ]{0,1}assign_to_name[ ]{0,1}/', 'AssignedTo', $info);
                $info = preg_replace('/[ ]{0,1}assign_to[ ]{0,1}/', 'AssignedToID', $info);
                $info = preg_replace('/[ ]{0,1}updated_by_name[ ]{0,1}/', 'LastEditedBy', $info);
                $info = preg_replace('/[ ]{0,1}updated_by[ ]{0,1}/', 'LastEditedByID', $info);
                $info = preg_replace('/[ ]{0,1}updated_at[ ]{0,1}/', 'LastEditedDate', $info);
                $info = preg_replace('/[ ]{0,1}modified_by_name[ ]{0,1}/', 'ModifiedBy', $info);
                $info = preg_replace('/[ ]{0,1}modified_by[ ]{0,1}/', 'ModifiedByID', $info);
                $info = preg_replace('/[ ]{0,1}mail_to[ ]{0,1}/', 'MailTo', $info);
                $info = preg_replace('/[ ]{0,1}delete_flag[ ]{0,1}/', 'MarkForDeletion', $info);
                $info = preg_replace('/[ ]{0,1}related_case[ ]{0,1}/', 'LinkID', $info);
                $info = preg_replace('/[ ]{0,1}related_bug[ ]{0,1}/', 'BugID', $info);
                $info = preg_replace('/[ ]{0,1}related_result[ ]{0,1}/', 'ResultID', $info);
                $info = preg_replace('/[ ]{0,1}id[ ]{0,1}/', 'CaseID', $info);
                break;
            }
            case 'result': {              
                $info = preg_replace('/[ ]{0,1}module_name[ ]{0,1}/', 'ModulePath', $info);
                $info = preg_replace('/[ ]{0,1}product_name[ ]{0,1}/', 'ProjectName', $info);
                $info = preg_replace('/[ ]{0,1}product_id[ ]{0,1}/', 'ProjectID', $info);
                $info = preg_replace('/[ ]{0,1}productmodule_id[ ]{0,1}/', 'ModuleID', $info);
                $info = preg_replace('/[ ]{0,1}title[ ]{0,1}/', 'ResultTitle', $info);
                $info = preg_replace('/[ ]{0,1}result_value[ ]{0,1}/', 'ResultValue', $info);
                $info = preg_replace('/[ ]{0,1}result_status[ ]{0,1}/', 'ResultStatus', $info);
                $info = preg_replace('/[ ]{0,1}result_step[ ]{0,1}/', 'ResultSteps', $info);
                $info = preg_replace('/[ ]{0,1}related_case_id[ ]{0,1}/', 'CaseID', $info);
                $info = preg_replace('/[ ]{0,1}related_bug[ ]{0,1}/', 'BugID', $info);
                $info = preg_replace('/[ ]{0,1}mail_to[ ]{0,1}/', 'MailTo', $info);
                $info = preg_replace('/[ ]{0,1}created_by_name{0,1}/', 'OpenedBy', $info);
                $info = preg_replace('/[ ]{0,1}created_by[ ]{0,1}/', 'OpenedByID', $info);
                $info = preg_replace('/[ ]{0,1}created_at[ ]{0,1}/', 'OpenedDate', $info);
                $info = preg_replace('/[ ]{0,1}updated_by_name[ ]{0,1}/', 'LastEditedBy', $info);
                $info = preg_replace('/[ ]{0,1}updated_by[ ]{0,1}/', 'LastEditedByID', $info);
                $info = preg_replace('/[ ]{0,1}updated_at[ ]{0,1}/', 'LastEditedDate', $info);
                $info = preg_replace('/[ ]{0,1}assign_to_name[ ]{0,1}/', 'AssignedTo', $info);
                $info = preg_replace('/[ ]{0,1}assign_to[ ]{0,1}/', 'AssignedToID', $info);
                $info = preg_replace('/[ ]{0,1}modified_by_name[ ]{0,1}/', 'ModifiedBy', $info);
                $info = preg_replace('/[ ]{0,1}modified_by[ ]{0,1}/', 'ModifiedByID', $info);
                $info = preg_replace('/[ ]{0,1}id[ ]{0,1}/', 'ResultID', $info);
                break;
            }
            default: {
                break;
            }
        }
        
        return $info;
    }   
    

    public function getBugStatusChangeCount($moduleId, $beginDate, $endDate)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else
        {
            $resultInfo = Yii::app()->db->createCommand()
                            ->select('count(*) as status_change_count')
                            ->from(array('{{bug_history}}','{{bug_action}}','{{product_module}}','{{bug_info}}'))
                            ->where('{{bug_history}}.bugaction_id={{bug_action}}.id and {{bug_info}}.id={{bug_action}}.buginfo_id
                                and {{bug_info}}.productmodule_id={{product_module}}.id and (full_path_name = "'.
                                    $moduleInfo['full_path_name'].'" or full_path_name like "'.
                                    $moduleInfo['full_path_name'].'/%") and {{bug_action}}.created_at>="'.$beginDate.'" and
                                        {{bug_action}}.created_at<"'.$endDate.'" and action_field="bug_status"')
                            ->queryRow();
             $info = $resultInfo;
        }
        return array($code, $info);
    }

    public function getReopenBugCount($moduleId, $beginDate, $endDate)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else
        {
            $resultInfo = Yii::app()->db->createCommand()
                            ->select('count(*) as reopen_count')
                            ->from(array('{{bug_history}}','{{bug_action}}','{{product_module}}','{{bug_info}}'))
                            ->where('{{bug_history}}.bugaction_id={{bug_action}}.id and {{bug_info}}.id={{bug_action}}.buginfo_id
                                and {{bug_info}}.productmodule_id={{product_module}}.id and (full_path_name = "'.
                                    $moduleInfo['full_path_name'].'" or full_path_name like "'.
                                    $moduleInfo['full_path_name'].'/%") and {{bug_action}}.created_at>="'.$beginDate.'" and
                                        {{bug_action}}.created_at<"'.$endDate.'" and action_field="bug_status" and new_value="Active"')
                            ->queryRow();
             $info = $resultInfo;
        }
        return array($code, $info);
    }

    public function getBugCountByReopenNum($moduleId, $beginDate, $endDate, $reopenNum)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else if($reopenNum <= 0)
        {
            $code = API::ERROR_REOPEN_NUMBER;
            $info = Yii::t('API', 'reopen number wrong');
        }
        else
        {
            $reopenCountCondition = '';
            if($reopenNum>2)
            {
                $reopenCountCondition = 'reopen_count>2';
            }
            else
            {
                $reopenCountCondition = 'reopen_count='.(int)$reopenNum;
            }
            $sql = 'select COUNT(bug_id) as bug_count from (
              select buginfo_id as bug_id, COUNT(buginfo_id) as reopen_count
              from   {{bug_history}}, {{bug_action}}, {{bug_info}}, {{product_module}}
              where  {{bug_history}}.bugaction_id = {{bug_action}}.id and {{bug_info}}.id = {{bug_action}}.buginfo_id and
              {{bug_info}}.productmodule_id = {{product_module}}.id and (full_path_name = "'.
                                    $moduleInfo['full_path_name'].'" or full_path_name like "'.
                                    $moduleInfo['full_path_name'].'/%") and
                     {{bug_action}}.created_at>="'.$beginDate.'" and {{bug_action}}.created_at<"'.$endDate.'" and
                     action_field = "bug_status" and new_value = "Active"
              group by buginfo_id
            ) as t1 where '.$reopenCountCondition;

            $resultInfo = Yii::app()->db->createCommand()->setText($sql)->queryRow();
            $info = $resultInfo;
        }
        return array($code, $info);
    }

    public function getReopenBugDetail($moduleId, $beginDate, $endDate)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else
        {
            $sql = 'select buginfo_id as bug_id, COUNT(buginfo_id) as reopen_count
            from   {{bug_history}}, {{bug_action}}, {{bug_info}}, {{product_module}}
            where  {{bug_history}}.bugaction_id = {{bug_action}}.id and {{bug_info}}.id =
            {{bug_action}}.buginfo_id and {{bug_info}}.productmodule_id = {{product_module}}.id and (full_path_name = "' .
                    $moduleInfo['full_path_name'] . '" or full_path_name like "' .
                    $moduleInfo['full_path_name'] . '/%") and
                   {{bug_action}}.created_at>="' . $beginDate . '" and {{bug_action}}.created_at<"' . $endDate . '" and
                   action_field = "bug_status" and new_value = "Active"
            group by buginfo_id
            having COUNT(buginfo_id) > 0';
            $resultInfo = Yii::app()->db->createCommand()
                            ->setText($sql)->queryAll();
            $info['result_info'] = $resultInfo;
        }
        return array($code, $info);
    }

    public function getCloseBugDetail($moduleId, $beginDate, $endDate)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else
        {
            $sql = 'select {{bug_info}}.id,{{bug_info}}.created_at
                    from   {{bug_info}}, {{product_module}}
                    where  {{bug_info}}.productmodule_id = {{product_module}}.id and
                           (full_path_name = "' .
                    $moduleInfo['full_path_name'] . '" or full_path_name like "' .
                    $moduleInfo['full_path_name'] . '/%") and
                    closed_at>="' . $beginDate . '" and closed_at<"' . $endDate . '"';
            $resultInfo = Yii::app()->db->createCommand()
                            ->setText($sql)->queryAll();
            $info['result_info'] = $resultInfo;
        }
        return array($code, $info);
    }

    public function getBugStatusDetail($moduleId, $beginDate, $endDate)
    {
        $code = API::ERROR_NONE;
        $moduleInfo = ProductModule::model()->findByPk($moduleId);
        if(empty($moduleInfo))
        {
            $code = API::ERROR_MODULE_EMPTY;
            $info = Yii::t('API', 'module not found');
        }
        else if(!$this->validateDate($beginDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'beginDate ' . Yii::t('API', 'date format wrong');
        }
        else if(!$this->validateDate($endDate))
        {
            $code = API::ERROR_DATE_FORMAT;
            $info = 'endDate ' . Yii::t('API', 'date format wrong');
        }
        else
        {
            $sql = 'select buginfo_id, old_value, new_value, {{bug_action}}.created_at from {{bug_history}}, {{bug_action}}, {{bug_info}}
                   where {{bug_history}}.bugaction_id={{bug_action}}.id and {{bug_info}}.id={{bug_action}}.buginfo_id and
                   action_field = "bug_status" and buginfo_id in (select {{bug_info}}.id from {{bug_info}}, {{product_module}}
                     where  {{bug_info}}.productmodule_id = {{product_module}}.id and (full_path_name = "' .
                    $moduleInfo['full_path_name'] . '" or full_path_name like "' .
                    $moduleInfo['full_path_name'] . '/%") and closed_at>="' .
                    $beginDate . '" and closed_at<"' . $endDate . '") order by buginfo_id, {{bug_action}}.created_at';
       
            $resultInfo = Yii::app()->db->createCommand()
                            ->setText($sql)->queryAll();
            $info['result_info'] = $resultInfo;
        }
        return array($code, $info);
    }


    private function validateDate($date)
    {
        if(!preg_match("/^\d{4}-\d{2}-\d{2}$/s",$date))
        {
            return false;
        }
        if(false == strtotime($date))
        {
            return false;
        }
        return true;
    }

    /**
     * get real name by mail to
     *
     * @param string $mailTo
     * @return string 
     */
    private function getRealNamesByMailTo($mailTo)
    {
        $mailTo = trim($mailTo, ',');
        $realnames = array();
        $mailToArr = explode(',', $mailTo);
        foreach($mailToArr as $name)
        {
            $realnames[] = $this->getRealNameByName($name);
        }
        return join(',', $realnames);
    }
    
    /**
     * get realname by name
     *
     * @param string $name
     * @return string
     */
    private function getRealNameByName($name)
    {
        $user = TestUser::model()->findByAttributes(array('username' => $name));
        if(null !== $user)
        {
            $name = $user->realname;
        }
        return $name;
    }
    
    /**
     * get serach row array
     * 
     * @internal
     * @param string $node
     * @param array $arr
     * @param string $infoType
     * @return array
     */
    private function getSearchRowArr($node, $arr, $infoType, $compatible)
    {
        for($i = 0; $i < $node->childNodes->length; $i++)
        {
            $cnode = $node->childNodes->item($i);
            if(API::XML_FIELDS == $cnode->nodeName)
            {
                $arr += joinFields($cnode, $arr);
            }
            else
            {
                $field    = $cnode->attributes->getNamedItem(API::XML_NAME)->nodeValue;
                $operator = $cnode->attributes->getNamedItem(API::XML_OPERATOR)->nodeValue;
                $operator = isset(API::$OPERATOR[$operator]) ? API::$OPERATOR[$operator] : $operator;
                $value    = $cnode->attributes->getNamedItem('value')->nodeValue;
                $logic    = ucfirst(strtolower($node->getAttribute('logic')));
                if($compatible)
                {
                    if('MailTo' == $field || 'AssignedTo' ==  $field || preg_match('/^.*By$/', $field))
                    {
                        $value = $this->getRealNamesByMailTo($value);
                    }
                                
                    $field = $this->fieldOld2New($field, $infoType);
                }
                if(1 == (int)$field)
                {
                    $field = 1;
                    $operator = API::$OPERATOR['EQ'];
                    $value = 1;
                }
            }
            $row = array(
                'leftParenthesesName' => '',
                'field' => $field,
                'operator' => $operator,
                'value' => $value,
                'rightParenthesesName' => '',
                'andor' => $logic,
            );
            if(0 == $i)
            {
                $row['leftParenthesesName'] = '(';
            }
            if(($i + 1) == $node->childNodes->length)
            {
                $row['rightParenthesesName'] = ')';
            }
            $arr[] = $row;
        }
        
        return $arr;
    }
}
?>