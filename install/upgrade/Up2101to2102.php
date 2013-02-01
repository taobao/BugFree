<?php
require_once 'Upgrade.php';
class Up2101to2102 extends Upgrade
{
    function upgrade1()
    {
        $result = true;
        $info = 'Update BugInfo table successfully';
        $sql = 'ALTER TABLE `' . $this->oldpre . 'BugInfo` CHANGE `OpenedBuild` `OpenedBuild` VARCHAR(255) NOT NULL DEFAULT \'\'';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->oldpre . 'BugInfo` CHANGE `ResolvedBuild` `ResolvedBuild` VARCHAR(255) NOT NULL DEFAULT \'\'';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->oldpre . 'TestOptions` SET OptionValue = 11 WHERE OptionName = "dbVersion"';
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