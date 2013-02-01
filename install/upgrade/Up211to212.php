<?php
require_once 'Upgrade.php';
class Up211to212 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestProject table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestProject` ADD `FieldSet` TEXT AFTER `NotifyEmail`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUserQuery` ADD `ProjectID` int(11) DEFAULT NULL AFTER `FieldsToShow`';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 14 WHERE OptionName = "dbVersion"';
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