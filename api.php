<?php
$yii = dirname(__FILE__) . '/lib/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
$apiLocation = dirname(__FILE__) . '/protected/components/API.php';
require($apiLocation);
require($yii);
Yii::createWebApplication($config);

define('SUCCESS', 'success');
define('FAILED', 'failed');
define('NEWLINE', chr(0x03));
define('DEVIDER', chr(0x04));
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

// init the returned text's format, csv is default
$format = isset($_REQUEST['format']) && ($_REQUEST['format'] == 'json') ? 'json' : 'csv';
// init charset, utf8 is default
$charset = isset($_REQUEST['charset']) ? strtolower($_REQUEST['charset']) : 'utf8';
if($charset != 'utf8')
{
    $format = 'csv';
}

// set response header
@header("Content-Type: text/html; charset=$charset");
$sessionName = Yii::app()->getSession()->getSessionName();

$systemFields = array('mode', 'format', 'charset', $sessionName, 'attachment_file', 'deleted_file_id');

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
            $id = Yii::app()->getRequest()->getParam($item . 'ID');
            list($code, $info) = $api->getInfo($lowerItem, $id, true);
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
            list($code, $info) = $api->saveInfo($lowerItem, $_REQUEST, $systemFields, Info::ACTION_OPEN, true);
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
            list($code, $info) = $api->saveInfo($lowerItem, $_REQUEST, $systemFields, Info::ACTION_OPEN_EDIT, true);
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
            list($code, $info) = $api->getQuery($queryId, $page, $size, true);
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
            list($code, $info) = $api->query($xml, $schema, $productId, true);
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
        
        default: {
            $message['status'] = FAILED;
            $message['code']   = API::ERROR_MODE_NOT_FOUNT;
            $message['info']   = Yii::t('API', '{mode} mode not found error info', array('{mode}' => $mode));
            break;
        }
    }
}

// output
if('json' == $format)
{
    echo json_encode($message);
}
else
{
    if('utf8' == strtolower($charset))
    {
        $charset = 'UTF-8';
    }
    
    if('getquery' == $mode && isset($message['type']))
    {
        $type = ucfirst($message['type']);
        $csvList = '';
        if(!empty($message[$type . 'List']))
        {
            $list = $message[$type . 'List'];
            $current = current($list);
            $csvList = "\n" . join(',', array_keys($current)) . "\n";
            foreach($list as $item)
            {
                $csvList .= '"' . join('","', $item) . '"' . "\n";
            }
        }
        $message[$type . 'List'] = $csvList;
    }
    else if('query' == $mode)
    {
        $csvList = '';
        if(!empty($message['QueryList']))
        {
            $list = $message['QueryList'];
            $current = current($list);
            $csvList = "\n" . join(',', array_keys($current)) . "\n";
            foreach($list as $item)
            {
                $csvList .= '"' . join('","', $item) . '"' . "\n";
            }
        }
        $message['QueryList'] = $csvList;
    }
    else if('findproducts' == $mode)
    {
        $csvList = '';
        if(!empty($message['ProductList']))
        {
            $list = $message['ProductList'];
            $current = current($list);
            $csvList = "\n" . join(',', array_keys($current)) . "\n";
            foreach($list as $item)
            {
                $csvList .= '"' . join('","', $item) . '"' . "\n";
            }
        }
        $message['ProductList'] = $csvList;
    }
    else if('findmodules' == $mode)
    {
        $csvList = '';
        if(!empty($message['ModuleList']))
        {
            $list = $message['ModuleList'];
            $current = current($list);
            $csvList = "\n" . join(',', array_keys($current)) . "\n";
            foreach($list as $item)
            {
                $csvList .= '"' . join('","', $item) . '"' . "\n";
            }
        }
        $message['ModuleList'] = $csvList;
    }
    
    $status = $message['status'];
    $csv = $status . NEWLINE;
    unset($message['status']);
    $keyList = array();
    $valList = array();
    foreach($message as $key => $val)
    {
        $keyList[] = $key;
        $valList[] = bfIconv('UTF-8', $charset, $val);
    }
    
    $csv .= csvJoin(DEVIDER, $keyList) . NEWLINE;
    $csv .= csvJoin(DEVIDER, $valList);
    $csv .= NEWLINE . md5($csv);
    echo $csv;
}


/**
 * csv join
 * 
 * @param string $glue
 * @param array $pieces
 * @return string
 */
function csvJoin($glue, $pieces)
{
    foreach($pieces as $key => $piece)
    {
        if(is_array($piece))
        {
            $pieces[$key] = csvJoin($glue, $piece);
        }
    }
    return join($glue, $pieces);
}

/**
 * convert string to requested character encoding
 * 
 * @param string $inCharset
 * @param string $outCharset
 * @param string $str
 * @return string 
 */
function bfIconv($inCharset, $outCharset, $str)
{
    if(empty($inCharset) || empty($outCharset))
    {
        return $str;
    }
    if(strtolower($inCharset) == strtolower($outCharset))
    {
        return $str;
    }
    else
    {
        if(function_exists('iconv'))
        {
            return iconv($inCharset, $outCharset, $str);
        }
        elseif(function_exists('mb_convert_encoding'))
        {
            return mb_convert_encoding($str, $outCharset, $inCharset);
        }
        else
        {
            return $str;
        }
    }
}
?>