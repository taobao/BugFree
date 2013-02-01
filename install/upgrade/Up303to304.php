<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ActionHistoryService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0.4
 */
require_once 'Upgrade.php';

class Up303to304 extends Upgrade
{

    /**
     * modify db_version
     */
    function upgrade1()
    {
        $sql = 'UPDATE `' . $this->newpre . 'test_option` SET option_value = 20 WHERE option_name = "db_version"';
        $result = mysql_query($sql);
        if(!$result)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        return array(0, 'update db_version setting successfully.');
    }

}

?>