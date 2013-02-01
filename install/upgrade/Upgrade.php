<?php
abstract class Upgrade
{
    public $con;
    public $dbname;
    public $newpre;
    public $oldpre;
    
    function __construct($con, $dbname, $newpre, $oldpre)
    {
        $this->con = $con;
        $this->dbname = $dbname;
        $this->newpre = $newpre;
        $this->oldpre = $oldpre;
        $sql = 'USE ' . $dbname . ';SET NAMES UTF8;';
        $this->executeSQL($sql);
    }
    
    function upgrade($step = 1)
    {
        $fn = "upgrade{$step}";
        list($result, $info) = $this->{$fn}();
        $step++;
        $fn = "upgrade{$step}";
        if(!method_exists($this, $fn))
        {
            $step = 0;
        }
        return array($result, $info, $step);
    }
    
    /**
     * 执行SQL
     * 
     * @param String $sql SQL语句
     * @param String $prefix 表前缀，默认为''
     * @return array($result, $infos)
     * $result 执行结果，true 成功，false 失败
     * $infos 错误信息数组
     */
    protected function executeSQL($sql, $prefix = '')
    {
        $sql = addslashes($sql);
        $sql = trim($sql);
        $sql = preg_replace("/#[^\n]*\n/", "", $sql);
        $sql = preg_replace("/--[^\n]*\n/", "", $sql);
        $sql = preg_replace("/CREATE[ ]*TABLE.*`([a-z_0-9]{1,})`/i", "CREATE  TABLE `{$prefix}\\1`", $sql);
        $sql = preg_replace("/ALTER[ ]*TABLE.*`([a-z_0-9]{1,})`/i", "ALTER TABLE `{$prefix}\\1`", $sql);
        $sql = preg_replace("/DROP[ ]*TABLE.*`([a-z_0-9]{1,})`/i", "DROP TABLE IF EXISTS `{$prefix}\\1`", $sql);
        
        $buffer = array();
        $ret    = array();
        $in_string = false;
        for($i = 0; $i < strlen($sql)-1; $i++)
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
                $result = $result && mysql_query($ret[$i], $this->con);
                if(!$result)
                {
                    $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
                }
            }
        }
        return array($result, $infos);
    }
    
    protected function executeDataSQL($sql, $prefix = '')
    {
        $sql = trim($sql);
        $sql = preg_replace("/#[^\n]*\n/", "", $sql);
        $sql = preg_replace("/--[^\n]*\n/", "", $sql);
        $sql = preg_replace("/UPDATE[ ]*`([a-z_0-9]{1,})`/i", "UPDATE `{$prefix}\\1`", $sql);
        $sql = preg_replace("/INSERT[ ]*INTO[ ]*`([a-z_0-9]{1,})`/i", "INSERT INTO `{$prefix}\\1`", $sql);
        $sql = preg_replace("/REPLACE[ ]*INTO[ ]*`([a-z_0-9]{1,})`/i", "REPLACE INTO `{$prefix}\\1`", $sql);

        $infos = array();
        $result = mysql_query($sql, $this->con);
        
        if(!$result)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
        }
        
        return array($result, $infos);
    }
}
?>