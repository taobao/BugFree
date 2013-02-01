<?php
require_once 'Upgrade.php';
class Up2042to21 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestUserQuery table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUser` CHANGE `UserName` `UserName` VARCHAR(30) NOT NULL DEFAULT \'\'';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 9 WHERE OptionName = "dbVersion"';
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