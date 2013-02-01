<?php
require_once('func.inc.php');
set_time_limit(0);
error_reporting(E_ERROR);
// 基本路径
define('BASEPATH', realpath(dirname(dirname(__FILE__))));
// upload path
define('UPLOADPATH', realpath(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'BugFile');
// 配置样本文件路径
define('CONFIG_SAMPLE_FILE', BASEPATH . '/protected/config/main.sample.php');
// 配置文件路径
define('CONFIG_FILE', BASEPATH . '/protected/config/main.php');
// 需要检查读写权限的目录
$paths = array(
    UPLOADPATH,
    BASEPATH . DIRECTORY_SEPARATOR . 'assets',
    BASEPATH . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'runtime',
    BASEPATH . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'config',
    BASEPATH . DIRECTORY_SEPARATOR . 'install'
);
// 检查环境动作参数
define('CHECK', 'check');
// 升级动作参数
define('UPGRADE', 'upgrade');
// 升级完成
define('UPGRADED', 'upgraded');
// 备份数据
define('BACKUP', 'backup');
// 配置动作参数
define('CONFIG', 'config');
// 安装动作参数
define('INSTALL', 'install');
// 安装成功
define('INSTALLED', 'installed');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : CHECK;
if(is_file("install.lock") && $action != UPGRADED && $action != INSTALLED)
{
    header("location: ../index.php");
}

$lang = getPreferredLanguage();
$viewDir =  dirname(__FILE__) . '/views/' . $lang . '/';
if(!is_dir($viewDir))
{
    $viewDir = dirname(__FILE__) . '/views/zh_cn/';
}

switch($action)
{
    case CHECK: {
        $viewFile = $viewDir . 'check.php';
        $requirements = getRequirements();
        $dirRights = getDirRights($paths);
        renderFile($viewFile, array(
            'requirements' => $requirements,
            'dirRights' => $dirRights,
            'checkResult' => getCheckResult($requirements, $dirRights),
        ));
        break;
    }
    case BACKUP: {
        $viewFile = $viewDir . 'upgrade.php';
        list($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix) = getDBConfig(CONFIG_FILE);
        list($result, $info, $target, $con) = getDBCon($dbhost, $port, $dbuser, $dbpwd);

        $version = getDBVersion($con, $dbname, $dbprefix);
        if($_CFG['version'] == $_CFG['versionMap'][$version])
        {
            @mysql_close($con);
            header('Location: index.php?action=upgraded');
        }

        if(isset($_GET['download']))
        {
            if(!$result)
            {
                $dumpSql = backupDB($con, $dbname, $dbprefix);
                $compressSql = gzencode($dumpSql, 9);
                header('Content-type:application/text');
                header('Content-Disposition:attachment;filename=bugfree.sql.gz');
                echo $compressSql;
                break;
            }
        }
        renderFile($viewFile, array(
            'version' => $version
        ));
        @mysql_close($con);
        break;
    }
    case UPGRADE: {
        list($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix) = getDBConfig(CONFIG_FILE);
        list($result, $info, $target, $con) = getDBCon($dbhost, $port, $dbuser, $dbpwd);
        if(!$result)
        {
            $dbVersion = isset($_GET['dbversion']) ? $_GET['dbversion'] : null;
            $step = isset($_GET['step']) ? $_GET['step'] : 1;
            list($result, $info, $dbVersion, $step) = upgrade($con, $dbname, $dbprefix, $dbVersion, $step);
        }
        @mysql_close($con);
        echo json_encode(array('result' => $result, 'info' => $info, 'dbversion' => $dbVersion, 'step' => $step));
        break;
    }
    case UPGRADED: {
        $viewFile = $viewDir . 'upgraded.php';
        file_put_contents('install.lock', '');
        renderFile($viewFile);
        break;    
    }
    case CONFIG: {        
        $viewFile = $viewDir . 'config.php';
        renderFile($viewFile);
        break;
    }
    case INSTALL: {
        $dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
        $dbuser = isset($_POST['dbuser']) ? $_POST['dbuser'] : '';
        $port = isset($_POST['port']) ? $_POST['port'] : '3306';
        $dbpwd = isset($_POST['dbpwd']) ? $_POST['dbpwd'] : '';
        $dbhost = isset($_POST['dbhost']) ? $_POST['dbhost'] : '';
        $dbprefix = isset($_POST['dbprefix']) ? $_POST['dbprefix'] : '';
        $language = isset($_POST['language']) ? $_POST['language'] : 'zh_cn';
       
        $accept = isset($_POST['accept']) ? true : false;
        
        if(!$accept)
        {
            $result = -1;
            $target = 'alert';
            $info = t('bugfree', 'don\'t accept the license');
        }
        else
        {
            list($result, $info, $target, $con) = getDBCon($dbhost, $port, $dbuser, $dbpwd);
            // db con check
            if(!$result)
            {
                list($result, $info, $target) = checkDB($con, $dbname, $dbprefix);
                // db check 
                if(0 == $result || 1 == $result)
                {
                    if(setDBConfig($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix,$language, CONFIG_SAMPLE_FILE, CONFIG_FILE))
                    {
                        list($result, $info, $target) = createDB($con, $dbname,$result, $dbprefix);
                        if(!$result)
                        {
                            file_put_contents('install.lock', '');
                        }
                    }
                }
                // 升级 
                else if(2 == $result)
                {
                    setDBConfig($dbhost, $dbname, $port, $dbuser, $dbpwd, $dbprefix, $language, CONFIG_SAMPLE_FILE, CONFIG_FILE);
                }
                @mysql_close($con);
            }
        }
       
        echo json_encode(array('result' => $result, 'info' => $info, 'target' => $target));
        break;
    }
    case INSTALLED:{
        $viewFile = $viewDir . 'install.php';
        renderFile($viewFile);
        break;
    }
    default: {
        echo "Error URL";
        exit;
    }
}
?>