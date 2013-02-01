<?php
require_once 'Upgrade.php';
class Up21to2101 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestUserQuery table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUser` ADD `AuthMode` VARCHAR(30) NOT NULL DEFAULT \'DB\'';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUser` SET `AuthMode` = "DB"';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 10 WHERE OptionName = "dbVersion"';
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