<?php
require_once 'Upgrade.php';
class Up2103to211 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update TestUser table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUser` ADD `Wangwang` VARCHAR(20) NOT NULL DEFAULT \'\' AFTER `Email`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->oldpre . 'TestUserQuery` ADD `NoticeFlag` TINYINT NOT NULL DEFAULT 2 AFTER `Wangwang`';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 13 WHERE OptionName = "dbVersion"';
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