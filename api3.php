<?php
$yii = dirname(__FILE__) . '/lib/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
$apiLocation = dirname(__FILE__) . '/protected/components/API.php';
require($apiLocation);
require($yii);
Yii::createWebApplication($config);

define('SUCCESS', 'success');
define('FAILED', 'failed');
define('DEFAULT_PAGE', 1);
define('DEFAULT_SIZE', 100);

// init message info
$message = array(
    'status' => SUCCESS,
    'code' => API::ERROR_NONE,
    'info' => Yii::t('API', 'success info')
);

$mode = '';

// validate input
if(!isset($_REQUEST['mode']))
{
    $message['status'] = FAILED;
    $message['code']   = API::ERROR_MODE_EMPTY;
    $message['info']   = Yii::t('API', 'mode empty error info');
}
else
{
    $mode = $_REQUEST['mode'];
}

// init the returned text's format
$format = 'json';
// init charset
$charset = 'utf8';

// set response header
@header("Content-Type: text/html; charset=$charset");
$sessionName = Yii::app()->getSession()->getSessionName();

$systemFields = array('mode', $sessionName, 'attachment_file', 'deleted_file_id');

if(SUCCESS == $message['status'])
{
    $api = new API();
    switch($mode)
    {
        case 'getsid': {
            list($sessionId, $rand) = $api->getApiRand();
            $message['sessionname'] = $sessionName;
            $message['sessionid'] = $sessionId;
            $message['rand'] = $rand;
            break;
        }
        
        case 'login': {
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            if(null === $sessionId)
            {
                $message['status'] = FAILED;
                $message['code']   = API::ERROR_SESSION_ID_EMPTY;
                $message['info']   = Yii::t('API', 'session id empty error info');
            }
            else
            {
                $username = Yii::app()->getRequest()->getParam('username');
                $auth = Yii::app()->getRequest()->getParam('auth');
                list($code, $info, $timeout) = $api->login($sessionId, $username, $auth);
                if(API::ERROR_NONE == $code)
                {
                    $message['timeout'] = $timeout;
                }
                else
                {
                    $message['status'] = FAILED;
                    $message['code']   = $code;
                    $message['info']   = $info;
                }
            }
            break;
        }
        
        case 'getbug': case 'getcase': case 'getresult': {
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            preg_match('/^get(bug|case|result)$/', $mode, $regs);
            $lowerItem = $regs[1];
            $item = ucfirst($lowerItem);
            $id = Yii::app()->getRequest()->getParam('id');
            list($code, $info) = $api->getInfo($lowerItem, $id);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                foreach($info as $key => $val)
                {
                    $message[$key] = $val;
                }
            }
            break;
        }
        
        case 'addbug': case 'addcase': case 'addresult': {
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            preg_match('/^add(bug|case|result)$/', $mode, $regs);
            $lowerItem = $regs[1];
            list($code, $info) = $api->saveInfo($lowerItem, $_REQUEST, $systemFields, Info::ACTION_OPEN);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $infoID = ucfirst($lowerItem) . 'ID';
                $message[$infoID] = $info['id'];
            }
            break;
        }
        
        case 'updatebug': case 'updatecase': case 'updateresult': {
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            preg_match('/^update(bug|case|result)$/', $mode, $regs);
            $lowerItem = $regs[1];
            list($code, $info) = $api->saveInfo($lowerItem, $_REQUEST, $systemFields, Info::ACTION_OPEN_EDIT);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $infoID = ucfirst($lowerItem) . 'ID';
                $message[$infoID] = $info['id'];
            }
            break;
        }
        
        case 'getquery': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $queryId = Yii::app()->getRequest()->getParam('QueryID');
            $page = Yii::app()->getRequest()->getParam('page', DEFAULT_PAGE);
            $size = Yii::app()->getRequest()->getParam('size', DEFAULT_SIZE);
            list($code, $info) = $api->getQuery($queryId, $page, $size);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            
            break;
        }
        
        case 'query': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $xml = Yii::app()->getRequest()->getParam('query');
            $productId = Yii::app()->getRequest()->getParam('product_id');
            $schema = file_get_contents('query.xsd');
            list($code, $info) = $api->query($xml, $schema, $productId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }
        
        case 'findproducts': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $productId = Yii::app()->request->getParam('id');
            $productName = Yii::app()->request->getParam('name');
            list($code, $info) = $api->findProducts($productId, $productName);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }
        
        case 'findmodules': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->request->getParam('id');
            $moduleName = Yii::app()->request->getParam('name');
            $productId = Yii::app()->request->getParam('product_id');
            list($code, $info) = $api->findModules($productId, $moduleId, $moduleName);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'finduser': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $realName = Yii::app()->request->getParam('realname');
            list($code, $info) = $api->findUser($realName);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'getBugStatusChangeCount': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            list($code, $info) = $api->getBugStatusChangeCount($moduleId, $beginDate, $endDate);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'getReopenCount': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            list($code, $info) = $api->getReopenBugCount($moduleId, $beginDate, $endDate);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'getReopenBugDetail': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            list($code, $info) = $api->getReopenBugDetail($moduleId, $beginDate, $endDate);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'getCloseBugDetail': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            list($code, $info) = $api->getCloseBugDetail($moduleId, $beginDate, $endDate);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }

        case 'getBugStatusDetail': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            list($code, $info) = $api->getBugStatusDetail($moduleId, $beginDate, $endDate);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }


        case 'getBugCountByReopenNum': {
            set_time_limit(0);
            $sessionId = Yii::app()->getRequest()->getParam($sessionName);
            list($code, $info) = $api->verify($sessionId);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
                break;
            }
            $moduleId = Yii::app()->getRequest()->getParam('module_id',0 );
            $beginDate = Yii::app()->getRequest()->getParam('begin_date', '');
            $endDate = Yii::app()->request->getParam('end_date','');
            $reopenNum = Yii::app()->getRequest()->getParam('reopen_num',0 );
            list($code, $info) = $api->getBugCountByReopenNum($moduleId, $beginDate, $endDate, $reopenNum);
            if(API::ERROR_NONE != $code)
            {
                $message['status'] = FAILED;
                $message['code']   = $code;
                $message['info']   = $info;
            }
            else
            {
                $message += $info;
            }
            break;
        }
        
        default: {
            $message['status'] = FAILED;
            $message['code']   = API::ERROR_MODE_NOT_FOUNT;
            $message['info']   = Yii::t('API', '{mode} mode not found error info', array('{mode}' => $mode));
            break;
        }
    }
}

// output
echo json_encode($message);
?>