<?php
require_once 'Upgrade.php';
class Up212to213 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestUserQuery table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUserQuery` ADD `LeftParentheses` TEXT DEFAULT NULL AFTER `ProjectID`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUserQuery` ADD `RightParentheses` TEXT DEFAULT NULL AFTER `ProjectID`';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 15 WHERE OptionName = "dbVersion"';
        $result = mysql_query($sql);
        
        if(!$result)
        {
            $info = mysql_errno();
        }
        else
        {
            $result = 0;
        }
        
        return array($result, $info);
    }
}
?>