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
 * @version 3.0.2
 */
require_once 'Upgrade.php';

class Up301to302 extends Upgrade
{

    /**
     * set product's bug_severity,bug_priority,case_priority value
     */
    function upgrade1()
    {
        $result = true;
        $info = 'Update product table successfully.';
        $sql = 'ALTER TABLE `' . $this->newpre . 'product` ADD `bug_severity` VARCHAR( 255 ) NOT NULL AFTER `case_step_template`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->newpre . 'product` ADD `bug_priority` VARCHAR( 255 ) NOT NULL AFTER `bug_severity`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->newpre . 'product` ADD `case_priority` VARCHAR( 255 ) NOT NULL AFTER `bug_priority`';
        $result = mysql_query($sql);

        $sql = 'UPDATE ' . $this->newpre . "product SET bug_severity = '1,2,3,4', bug_priority = '1,2,3,4', case_priority = '1,2,3,4'";
        $result = mysql_query($sql, $this->con);

        if(!$result)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        return array(0, $info);
    }

    /**
     * etton table index modify
     */
    function upgrade2()
    {
        $infoTypeArr = array('bug', 'case', 'result');

        foreach($infoTypeArr as $infoType)
        {
            $sql = "SHOW TABLES LIKE '" . $this->newpre . "etton{$infoType}%'";
            $tableResult = mysql_query($sql, $this->con);
            if(!$tableResult)
            {
                $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
                $info = implode("\n", $infos);
                return array(1, $info);
            }
            while($tableResult && $row = mysql_fetch_array($tableResult, MYSQL_ASSOC))
            {
                $valueRow = array_values($row);
                $tableName = $valueRow[0];
                if(false !== strpos($tableName, $this->newpre . "etton{$infoType}"))
                {
                    $createdIndexSql = 'CREATE INDEX  `' . $this->newpre .
                            '_idx_' . $infoType . '_id` ON `' . $this->dbname . '`.`' . $tableName . '` (' . $infoType . '_id)';
                    $indexResult = mysql_query($createdIndexSql, $this->con);
                }
            }
        }
        return array(0, 'modify etton table index setting successfully.');
    }

    /**
     * modify bug_info index
     */
    function upgrade3()
    {
        //used by mark by me query
        $createdIndexSql = 'CREATE INDEX  `' . $this->newpre .
                '_idx_productid_id` ON `' . $this->dbname . '`.`' . $this->newpre . 'bug_info` (product_id,id)';
        $indexResult = mysql_query($createdIndexSql, $this->con);
        if(!$indexResult)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }

        //used by created by me query
        $createdIndexSql = 'CREATE INDEX  `' . $this->newpre .
                '_idx_created_by` ON `' . $this->dbname . '`.`' . $this->newpre . 'bug_info` (created_by)';
        $indexResult = mysql_query($createdIndexSql, $this->con);
        if(!$indexResult)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        return array(0, 'modify ' . $this->newpre . 'bug_info table index setting successfully.');
    }

    /**
     * modify case_info index
     */
    function upgrade4()
    {
        //used by mark by me query
        $createdIndexSql = 'CREATE INDEX  `' . $this->newpre .
                '_idx_productid_id` ON `' . $this->dbname . '`.`' . $this->newpre . 'case_info` (product_id,id)';
        $indexResult = mysql_query($createdIndexSql, $this->con);
        if(!$indexResult)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        return array(0, 'modify ' . $this->newpre . 'case_info table index setting successfully.');
    }

    /**
     * modify result_info index
     */
    function upgrade5()
    {
        //used by mark by me query
        $createdIndexSql = 'CREATE INDEX  `' . $this->newpre .
                '_idx_productid_id` ON `' . $this->dbname . '`.`' . $this->newpre . 'result_info` (product_id,id)';
        $indexResult = mysql_query($createdIndexSql, $this->con);
        if(!$indexResult)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        return array(0, 'modify ' . $this->newpre . 'result_info table index setting successfully.');
    }

    /**
     * modify db_version
     */
    function upgrade6()
    {
        $sql = 'UPDATE `' . $this->newpre . 'test_option` SET option_value = 18 WHERE option_name = "db_version"';
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