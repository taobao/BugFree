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
 * @version 3.0
 */
require_once 'Upgrade.php';
require_once dirname(dirname(dirname(__FILE__))) . '/protected/service/PinyinService.php';

class Up300to301 extends Upgrade
{
    const PAGE_SIZE = 100;

    /**
     * get file's edit info
     * add user name's full_pin and first_pinyin for user search use
     */
    function upgrade1()
    {
        $result = true;
        $info = 'Update test_user table successfully';
        $sql = 'ALTER TABLE `' . $this->newpre . 'test_user` ADD `full_pinyin` VARCHAR( 45 ) NULL AFTER `lock_version`';
        $result = mysql_query($sql);
        $sql = 'ALTER TABLE `' . $this->newpre . 'test_user` ADD `first_pinyin` VARCHAR( 45 ) NULL AFTER `full_pinyin`';
        $result = mysql_query($sql);
        $sql = 'UPDATE `' . $this->newpre . 'test_option` SET option_value = 17 WHERE option_name = "db_version"';
        $result = mysql_query($sql);
        $countSql = 'SELECT max(`id`) as count FROM `' . $this->newpre . 'test_user`';
        $countResult = mysql_query($countSql, $this->con);
        $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
        $count = $countArr['count'];
        $start = -3; //special user,closed id -2, active id -1
        while($start < $count)
        {
            $arr = array();
            for($i = 0; $i < self::PAGE_SIZE; $i++)
            {
                $arr[] = $start++;
            }

            $sql = 'SELECT id,realname FROM ' . $this->newpre . 'test_user WHERE id IN(' . join(',', $arr) . ')';
            $userResult = mysql_query($sql, $this->con);

            while($userResult && $row = mysql_fetch_array($userResult, MYSQL_ASSOC))
            {
                if(!empty($row['realname']))
                {
                    $realname = $row['realname'];
                    $pinyin = PinyinService::pinyin(strtolower($realname));
                    $sql = 'UPDATE ' . $this->newpre . "test_user SET full_pinyin = '" . $pinyin[0] . "', first_pinyin = '" . $pinyin[1] . "' WHERE id = " . $row['id'];
                    $result = mysql_query($sql, $this->con);
                    if(!$result)
                    {
                        $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
                        $info = implode("\n", $infos);
                        return array(1, $info);
                    }
                }
            }
        }
        return array(0, $info);
    }

    /**
     * add system config option
     */
    function upgrade2()
    {
        $result = true;
        $sql = 'INSERT INTO `test_option` (`option_name`, `option_value`, `created_at`, `created_by`, `updated_at`, `updated_by`, `lock_version`) VALUES'
                . '("DEFAULT_PAGESIZE", 20, NOW(), 0, NOW(), 0, 1),' .
                '("MAX_FILE_SIZE", 2097152, NOW(), 0, NOW(), 0, 1),' .
                '("QUERY_FIELD_NUMBER", 8, NOW(), 0, NOW(), 0, 1)';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'test_option successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    /**
     * rename addon table name
     */
    function upgrade3()
    {
        $result = true;

        $ettExisteSql = "SHOW TABLES LIKE '" . $this->newpre . "etton%'";
        $ettResult = mysql_query($ettExisteSql, $this->con);
        if(!$ettResult)
        {
            $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
            $info = implode("\n", $infos);
            return array(1, $info);
        }
        else
        {
            $ettTableNum = mysql_num_rows($ettResult);
            if($ettTableNum > 0)
            {
                return array(0, 'Rename addon table to etton table successfully.');
            }
        }
        $sql = "SHOW TABLES LIKE '" . $this->newpre . "addon%'";
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
            if(false !== strpos($tableName, $this->newpre . 'addon'))
            {
                $newAddonTableName = str_replace('addon', 'etton', $tableName);
                $renameSql = 'RENAME TABLE  `' . $this->dbname . '`.`' . $tableName . '` TO  `' . $this->dbname . '`.`' . $newAddonTableName . '` ';
                $renameResult = mysql_query($renameSql, $this->con);
                if(!$renameResult)
                {
                    $infos[] = (($this->con) ? mysql_error($this->con) : mysql_error());
                    $info = implode("\n", $infos);
                    return array(1, $info);
                }
            }
        }
        return array(0, 'Rename addon table to etton table successfully.');
    }

}

?>