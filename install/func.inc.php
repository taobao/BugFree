<?php

// 系统配置函数
$_CFG = require_once('config.inc.php');
define('BASEPATH', realpath(dirname(dirname(__FILE__))));
// 配置文件路径
define('CONFIG_FILE', BASEPATH . '/protected/config/main.php');

/*
 * 获取必需的设置检查
 * 
 * @return array array($label, $result, $local, $requirement) 结果数组
 * $label 必需的检查
 * $result 检查结果
 * $local 本地版本或支持
 * $requirement 版本或支持要求
 */

function getRequirements()
{
    return $requirements = array(
array(
    t('bugfree', 'PHP version'),
    version_compare(PHP_VERSION, "5.1.0", ">="),
    PHP_VERSION,
    '5.1.0+'),
 array(
    t('bugfree', 'MySQL version'),
    ($message = checkMysql()) !== t('bugfree', 'Not Install') && $message,
    ($message !== t('bugfree', 'Not Install')) ? mysql_get_client_info() : $message,
    '5.0+'),
 array(
    t('bugfree', '$_SERVER variable'),
    ($message = checkServerVar()) === t('bugfree', 'Supported'),
    $message,
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'Reflection extension'),
    ($flag = class_exists('Reflection', false)) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'PCRE extension'),
    ($flag = extension_loaded("pcre")) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'SPL extension'),
    ($flag = extension_loaded("SPL")) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'PDO extension'),
    ($flag = extension_loaded('pdo')) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'JSON extension'),
    ($flag = extension_loaded('json')) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
 array(
    t('bugfree', 'PDO MySQL extension'),
    ($flag = extension_loaded('pdo_mysql')) === true,
    $flag ? t('bugfree', 'Supported') : t('bugfree', 'Not Install'),
    t('bugfree', 'Supported')),
    );
}

/**
 * 检查MySQL数据库版本
 * 
 * @return boolean $result 检查结果
 */
function checkMysql()
{
    if(function_exists("mysql_get_client_info"))
    {
        $versionInfo = mysql_get_client_info();
        preg_match('/[^\d]*([\d\.]+)[^\d]*/', $versionInfo, $version);
        $version = isset($version[1]) ? $version[1] : $versionInfo;
        return version_compare($version, '5.0', '>=');
    }
    return t('bugfree', 'Not Install');
}

/**
 * 检查服务器变量
 * 
 * @return boolean $result 检查结果
 */
function checkServerVar()
{
    $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
    $missing = array();
    foreach($vars as $var)
    {
        if(!isset($_SERVER[$var]))
            $missing[] = $var;
    }
    if(!empty($missing))
        return t('bugfree', '$_SERVER does not have {vars}.', array('{vars}' => implode(', ', $missing)));

    if(!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
        return t('bugfree', 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');

    if(!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0)
        return t('bugfree', 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');

    return t('bugfree', 'Supported');
}

/**
 * 检查图形绘制库的支持
 * 
 * @return boolean $result 检查结果
 */
function checkGD()
{
    if(extension_loaded('gd'))
    {
        $gdinfo = gd_info();
        return $gdinfo['GD Version'];
    }
    return t('bugfree', 'Not Install');
}

/**
 * 获取目录权限
 * 
 * @param string $path 目录
 * @return array array($path, $result, $readable, $writeable) 权限结果数组
 * $path 路径
 * $result 权限结果
 * $readable 是否可读
 * $writeable 是否可写
 */
function getDirRights($paths)
{
    $dirRights = array();
    if(is_array($paths))
    {
        foreach($paths as $path)
        {
            list($readable, $writeable) = checkReadAndWrite($path);
            $dirRights[] = array($path, $readable && $writeable, $readable, $writeable);
        }
    }
    return $dirRights;
}

/**
 * 检查某路径下所有的子目录及子文件的读写权限
 * 
 * @param string $filename 文件名称
 * @param string $readable 是否可读，默认为true
 * @param boolean $writeable 是否可写，默认为true
 * @return array array($readable, $writeable)
 * $readable 是否可写
 * $writeable 是否可读
 */
function checkReadAndWrite($filename, $readable = true, $writeable = true)
{
    if(!empty($filename))
    {
        $readable = $readable && is_readable($filename);
        $writeable = $writeable && is_writable($filename);
        if(is_dir($filename))
        {
            if(($currentDir = opendir($filename)) !== false)
            {
                while(($file = readdir($currentDir)) !== false)
                {
                    if($file != '.' && $file != '..')
                    {
                        $filePath = $filename . '/' . $file;
                        list($readable, $writeable) = checkReadAndWrite($filePath, $readable, $writeable);
                    }
                }
            }
            closedir($currentDir);
        }
        return array($readable, $writeable);
    }
}

/**
 * 获取检查环境结果
 * 
 * @param array $requirements getRequirements()返回值
 * @param array $dirRights getDirRights()返回值
 * @return boolean $result 检查结果
 */
function getCheckResult($requirements, $dirRights)
{
    $result = true;
    foreach($requirements as $requirement)
    {
        $result = $requirement[1];
        if(!$result)
        {
            break;
        }
    }
    if($result)
    {
        foreach($dirRights as $dirRight)
        {
            $result = $dirRight[1];
            if(!$result)
            {
                break;
            }
        }
    }
    return $result;
}

/**
 * 从配置文件获取数据库配置
 * 
 * @param string $configFile 配置文件路径
 * @return string array($dbhost, $dbname, $dbuser, $dbpwd) 数据库配置数组
 * $dbhost 数据库主机
 * $dbname 数据库
 * $port 端口
 * $dbuser 数据库用户名
 * $dbpwd 数据库密码
 * $dbprefix 数据库表前缀
 */
function getDBConfig($configFile)
{
    $config = require($configFile);
    $dbconfig = $config['components']['db'];
    preg_match('/host=([^;]*)/', $dbconfig['connectionString'], $dbhosts);
    preg_match('/dbname=([^;]*)/', $dbconfig['connectionString'], $dbnames);
    preg_match('/port=([^;]*)/', $dbconfig['connectionString'], $ports);
    $dbhost = isset($dbhosts[1]) ? $dbhosts[1] : '';
    $dbname = isset($dbnames[1]) ? $dbnames[1] : '';
    $port = isset($ports[1]) ? $ports : '3306';
    $dbuser = isset($dbconfig['username']) ? $dbconfig['username'] : '';
    $dbpwd = isset($dbconfig['password']) ? $dbconfig['password'] : '';
    $dbprefix = isset($dbconfig['tablePrefix']) ? $dbconfig['tablePrefix'] : '';

    return array($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix);
}

/**
 * get language config
 *
 * @param string $configFile config file path
 * @return string language
 */
function getLanguageConfig($configFile)
{
    if(is_file($configFile))
    {
        $config = require($configFile);
        return $config['language'];
    }
    else
    {
        return '';
    }
}

/**
 * 将数据库配置写入到文件
 * 
 * @param string $dbhost 数据库主机
 * @param string $dbname 数据库名
 * @param string $port 端口
 * @param string $dbuser 数据库用户名
 * @param string $dbpwd 数据库密码
 * @param string $dbprefix 数据库表前缀
 * @param string $configSampleFile 配置样本文件路经
 * @param string $configFile 配置文件路径
 * @return boolean $result 写入结果 
 */
function setDBConfig($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix, $language, $configSampleFile, $configFile)
{
    $config = require($configSampleFile);
    $config['components']['db']['connectionString'] = 'mysql:host=' . $dbhost . ';dbname=' . $dbname . ';port=' . $port;
    $config['components']['db']['username'] = $dbuser;
    $config['components']['db']['password'] = $dbpwd;
    $config['components']['db']['tablePrefix'] = $dbprefix;
    $config['language'] = $language;
    return writeConfig($config, $configFile);
}

/**
 * 获取数据库连接
 * 
 * @param string $dbhost 数据库主机
 * @param string $port 端口
 * @param string $dbuser 数据库用户名
 * @param string $dbpwd 数据库密码
 * @return string array($result, $info, $target, $con)
 * $result 获取结果
 * $info 出错信息
 * $target 出错目标
 * $con 数据库连接
 */
function getDBCon($dbhost, $port, $dbuser, $dbpwd)
{
    $target = 'dbhost';
    $info = '';
    $result = 0;
    $dbhost .= ':' . $port;
    $con = mysql_connect($dbhost, $dbuser, $dbpwd);
    if(!$con)
    {
        $info = mysql_error();
        $result = 1;
    }
    return array($result, $info, $target, $con);
}

/**
 * 检测数据库
 * 
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名 
 * @param string $dbprefix
 * @return array array($result, $info, $target)
 * $result 检测结果，0：正常；1：数据库已存在；2：存在旧版本数据
 * $info 出错信息
 * $target 出错目标
 */
function checkDB($con, $dbname, $dbprefix)
{
    $result = 0;
    $info = '';
    $dbs = mysql_list_dbs($con);
    $target = '';
    while($row = mysql_fetch_array($dbs))
    {
        if($dbname == $row['Database'])
        {
            $result = 1;
            $version = getDBVersion($con, $dbname, $dbprefix);
            if($version > -1)
            {
                $result = 2;
            }
            break;
        }
    }
    return array($result, $info, $target);
}

/**
 * 创建数据库
 * 
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名
 * @param string $prefix 数据库表前缀，默认为''
 * @return array array($result, $info, $target)
 * $result 创建结果
 * $info 出错信息
 * $target 出错目标
 */
function createDB($con, $dbname, $checkDbResult, $prefix = '')
{
    if(0 == $checkDbResult)
    {
        $sql = 'CREATE DATABASE `' . $dbname . '`';
        $result = mysql_query($sql, $con);
    }
    else
    {
        $result = true;
    }
    $info = '';
    $target = 'alert';
    if($result)
    {
        mysql_select_db($dbname, $con);
        mysql_query('SET NAMES UTF8', $con);
        $installSql = require('schema.php');
        $installSql = preg_replace('/##PREFIX##/i', $prefix, $installSql);
        list($result, $infos) = executeSql($con, $installSql, $prefix);
        $info = implode("\n", $infos);
    }
    $result = $result ? 0 : 1;
    return array($result, $info, $target);
}

/**
 * 删除数据库
 *
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名
 * @return array array($result, $info, $target)
 * $result 创建结果
 * $info 出错信息
 * $target 出错目标
 */
function dropDB($con, $dbname)
{
    $result = true;
    $info = '';
    $target = 'alert';
    $sql = 'DROP DATABASE `' . $dbname . '`';
    list($result, $infos) = executeSql($con, $sql);
    $info = implode("\n", $infos);
    $result = $result ? 0 : 1;
    return array($result, $info, $target);
}

/**
 * 获取BugFree数据库版本信息
 * 
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名称
 * @return array
 */
function getDBVersion($con, $dbname, $dbprefix)
{
    mysql_query('USE ' . $dbname, $con);
    $result = mysql_query('SHOW TABLES', $con);
    $dbVersion = -1;
    $tableName = getConfigTableName($con, $dbname, $dbprefix);
    if('' != $tableName)
    {
        if(preg_match('/(.*)TestOptions/i', $tableName))
        {
            $sql = 'SELECT `OptionValue` FROM ' . $tableName . ' WHERE OptionName = "dbVersion"';
        }
        else if(preg_match('/(.*)test_option/i', $tableName))
        {
            $sql = 'SELECT `option_value` FROM ' . $tableName . ' WHERE option_name = "db_version"';
        }
        $result1 = mysql_query($sql, $con);
        $versions = mysql_fetch_row($result1);
        $dbVersion = $versions[0];
    }
    return $dbVersion;
}

/**
 * 获取BugFree数据库配置表名称
 * 3.0以下版本和3.0及以上版本的配置表名称有差别
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名称
 * @return string
 */
function getConfigTableName($con, $dbname, $dbprefix)
{
    mysql_query('USE ' . $dbname, $con);
    $result = mysql_query('SHOW TABLES', $con);
    $le3Table = '';
    $ge3Table = '';
    while($row = mysql_fetch_array($result))
    {
        $oldRegularStr = '/' . $dbprefix . 'TestOptions/i';
        $newRegularStr = '/' . $dbprefix . 'test_option/i';
        if(preg_match($oldRegularStr, $row[0]))
        {
            $le3Table = $row[0];
        }
        else if(preg_match($newRegularStr, $row[0]))
        {
            $ge3Table = $row[0];
        }
        if(('' != $le3Table) && ('' != $ge3Table))
        {
            break;
        }
    }
    if('' != $ge3Table)
    {
        return $ge3Table;
    }
    else if('' != $le3Table)
    {
        return $le3Table;
    }
    else
    {
        return '';
    }
}

/**
 * 获取BugFree版本信息
 * 
 * @param string $con 数据库连接
 * @param string $dbname 数据库名
 * @return string $version 版本信息 
 */
function getVersion($con, $dbname, $dbprefix)
{
    global $_CFG;
    $version = -1;
    $dbVersion = getDBVersion($con, $dbname, $dbprefix);
    if(isset($_CFG['versionMap'][$dbVersion]))
    {
        $version = $_CFG['versionMap'][$dbVersion];
    }
    return $version;
}

/**
 * 写配置文件
 * 
 * @param array $config 配置参数数组
 * @param string $file 配置文件路径
 * @return boolean $result 写配置文件结果
 */
function writeConfig($config, $file)
{
    $string = var_export($config, true);
    $string = "<?php\nreturn " . $string . "\n?>";
    $result = file_put_contents($file, $string);
    return $result;
}

/* * * 备份数据库 ** */

/**
 * 备份数据库
 * 
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名
 * @return string $dumpSql 数据库SQL
 */
function backupDB($con, $dbname, $dbprefix)
{
    mysql_query('USE ' . $dbname, $con);
    $result = mysql_query('SHOW TABLES', $con);
    $version = getVersion($con, $dbname, $dbprefix);
    $dumpSql = t('bugfree', 'BugFree backup header {version}', array('{version}' => $version));
    while($row = mysql_fetch_array($result))
    {
        $dumpSql .= table2sql($con, $row[0]);
    }
    return $dumpSql;
}

/**
 * 把表的结构和数据转换成为SQL
 * 
 * @param resource $con   数据库连接
 * @param string $table 要进行转换的表名
 * @param string $withData 是否转换数据，默认为true
 * @return string $tableDump 转换后的SQL
 */
function table2sql($con, $table, $withData = true)
{
    // create table sql
    $tableDump = "DROP TABLE IF EXISTS `$table`;\n";
    $createTable = mysql_query('SHOW CREATE TABLE `' . $table . '`', $con);
    $create = mysql_fetch_array($createTable);
    $tableDump .= $create[1] . ";\n\n";

    if($withData)
    {
        // create data sql
        mysql_query('SET NAMES UTF8;', $con);
        $result = mysql_query('SELECT * FROM `' . $table . '`', $con);
        while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $fields = '';
            $values = '';
            $comma = '';
            foreach($rows as $key => $val)
            {
                $fields .= $comma . '`' . $key . '`';
                $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                $comma = ',';
            }
            $tableDump .= "INSERT INTO $table ($fields) VALUES ($values);\n\n";
        }
    }

    return $tableDump;
}

/* * * 升级 ** */

/**
 * 升级
 * 
 * @param resource $con 数据库连接
 * @param string $dbname 数据库名
 * @param integer $dbVersion 数据库版本
 * @param integer $step 升级步骤
 * @param string $dbprefix 数据库表前缀
 * @return array array($result, $info, $target)
 * $result 创建结果
 * $info 出错信息
 * $target 出错目标
 */
function upgrade($con, $dbname, $dbprefix = '', $dbVersion = null, $step = 1)
{
    global $_CFG;

    if(!isset($dbVersion))
    {
        $dbVersion = getDBVersion($con, $dbname, $dbprefix);
    }
    $step = isset($step) ? $step : 1;
    mysql_query('USE ' . $dbname, $con);
    mysql_query('SET NAMES UTF8;', $con);
    $result = 0;
    $info = '';
    if(isset($_CFG['upgradeMap'][$dbVersion]))
    {
        $upgradeClazz = $_CFG['upgradeMap'][$dbVersion];
        require('upgrade/' . $upgradeClazz . '.php');
        $upgradeObj = new $upgradeClazz($con, $dbname, $dbprefix, $dbprefix);
        list($result, $info, $nstep) = $upgradeObj->upgrade($step);
        if(1 == $step)
        {
            $info = $_CFG['upgradeMsg'][$dbVersion] . "\n" . $info;
        }
        $step = $nstep;
        if(0 == $step)
        {
            $dbVersion++;
        }
    }
    else
    {
        $result = 2;
    }

    return array($result, $info, $dbVersion, $step);
}

/* * * 系统函数 ** */

/**
 * 执行SQL
 * 
 * @param resource $con 数据库连接
 * @param string $sql SQL
 * @return boolean $result 执行结果
 */
function executeSql($con, $sql)
{
    // Read the table structure definition sql.
    $sql = addslashes($sql);
    $sql = trim($sql);
    $sql = preg_replace("/#[^\n]*\n/", "", $sql);
    $sql = preg_replace("/--[^\n]*\n/", "", $sql);
    $buffer = array();
    $ret = array();
    $in_string = false;
    for($i = 0; $i < strlen($sql) - 1; $i++)
    {
        if($sql[$i] == ";" && !$in_string)
        {
            $ret[] = substr($sql, 0, $i);
            $sql = substr($sql, $i + 1);
            $i = 0;
        }

        if($in_string && ($sql[$i] == $in_string) && $buffer[0] != "\\")
        {
            $in_string = false;
        }
        elseif(!$in_string && ($sql[$i] == "\"" || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\"))
        {
            $in_string = $sql[$i];
        }
        if(isset($buffer[1]))
        {
            $buffer[0] = $buffer[1];
        }
        $buffer[1] = $sql[$i];
    }
    if(!empty($sql))
    {
        $ret[] = $sql;
    }

    $infos = array();
    $result = true;
    for($i = 0; $i < count($ret); $i++)
    {
        $ret[$i] = stripslashes(trim($ret[$i]));
        if(!empty($ret[$i]) && $ret[$i] != "#")
        {
            $result = $result && mysql_query($ret[$i]);
            if(!$result)
            {
                $infos[] = mysql_error();
            }
        }
    }
    return array($result, $infos);
}

/**
 * 翻译
 * 
 * @param string $category message文件
 * @param string $message message键
 * @param string $params 参数
 * 
 * @return string $result 翻译结果
 */
function t($category, $message, $params = array())
{
    static $messages;

    if($messages === null)
    {
        $messages = array();
        if(($lang = getPreferredLanguage()) !== false)
        {
            $file = dirname(__FILE__) . "/messages/$lang/$category.php";
            if(is_file($file))
                $messages = include($file);
        }
    }

    if(empty($message))
        return $message;

    if(isset($messages[$message]) && $messages[$message] !== '')
        $message = $messages[$message];

    return $params !== array() ? strtr($message, $params) : $message;
}

/**
 * 获取本地语言
 * 
 * @return string $language 本地语言
 */
function getPreferredLanguage()
{
    $language = getLanguageConfig(CONFIG_FILE);
    if(!empty($language))
    {
        return $language;
    }
    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ($n = preg_match_all('/([\w\-]+)\s*(;\s*q\s*=\s*(\d*\.\d*))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches)) > 0)
    {
        $languages = array();
        for($i = 0; $i < $n; ++$i)
            $languages[$matches[1][$i]] = empty($matches[3][$i]) ? 1.0 : floatval($matches[3][$i]);
        arsort($languages);
        foreach($languages as $language => $pref)
            return strtolower(str_replace('-', '_', $language));
    }
    return false;
}

/*
 * 渲染页面
 * 
 * @param string $file 被渲染的页面
 * @param string $params 参数
 */

function renderFile($file, $params = array())
{
    global $_CFG;
    extract($params);
    require($file);
}

?>