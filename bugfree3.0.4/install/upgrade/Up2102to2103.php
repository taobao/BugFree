<?php
require_once 'Upgrade.php';
class Up2102to2103 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestUserQuery table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUserQuery` ADD `FieldsToShow` TEXT';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 12 WHERE OptionName = "dbVersion"';
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