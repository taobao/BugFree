<?php
require_once 'Upgrade.php';
class Up213to3 extends Upgrade
{
    const PAGE_SIZE = 100;

    static $BUG_TYPE = array(
        ''             => '',
        'CodeError'    => '代码错误',
        'Interface'    => '用户界面',
        'DesignChange' => '需求变动',
        'NewFeature'   => '新增需求',
        'SpecDefect'   => '需求文档',
        'DesignDefect' => '设计文档',
        'Config'       => '配置相关',
        'Install'      => '安装部署',
        'Security'     => '安全相关',
        'Performance'  => '性能压力',
        'Standard'     => '标准规范',
        'Automation'   => '测试脚本',
        'TrackThings'  => '事务跟踪',
        'BadCase'      => 'Bad Case',
        'Others'       => '其他'
    );

    static $OS = array(
        ''        => '',
        'All'     => '全部',
        'Win7'    => 'Windows 7',
        'WinVista'=> 'Windows Vista',
        'WinXP'   => 'Windows XP',
        'Win2000' => 'Windows 2000',
        'Linux'   => 'Linux',
        'FreeBSD' => 'FreeBSD',
        'Unix'    => 'Unix',
        'MacOS'   => 'Mac OS',
        'Others'  => '其他',
    );

    static $BROWSER = array(
        ''           => '',
        'All'        => '全部',
        'IE8'        => 'IE 8.0',
        'IE7'        => 'IE 7.0',
        'IE6'        => 'IE 6.0',
        'FireFox4.0' => 'FireFox 4.0',
        'FireFox3.0' => 'FireFox 3.0',
        'FireFox2.0' => 'FireFox 2.0',
        'Chrome'     => 'Chrome',
        'Safari'     => 'Safari',
        'Opera'      => 'Opera',
        'Others'     => '其他',
    );

    static $BUG_SUB_STATUS = array(
        ''              => '',
        'Hold'          => 'Hold',
        'LocalFix'      => 'Local Fix',
        'CheckedIn'     => 'Checked In',
        'CannotRegress' => 'Can\'t Regress'
    );

    static $HOW_FOUND = array(
        ''             => '',
        'FuncTest'     => '功能测试',
        'UnitTest'     => '单元测试',
        'BVT'          => '版本验证测试',
        'Integrate'    => '集成测试',
        'System'       => '系统测试',
        'SmokeTest'    => '冒烟测试',
        'Acceptance'   => '验收测试',
        'BugBash'      => 'Bug Bash',
        'AdHoc'        => '随机测试',
        'Regression'   => '回归测试',
        'SpecReview'   => '需求检查',
        'DesignReview' => '设计检查',
        'CodeReview'   => '代码检查',
        'PostRTW'      => '上线遗漏',
        'Customer'     => '客户反馈',
        'Partner'      => '合作伙伴',
        'Other'        => '其他',
    );

    static $CASE_TYPE = array(
        ''              => '',
        'Functional'    => '功能',
        'Configuration' => '配置相关',
        'Setup'         => '安装部署',
        'Security'      => '安全相关',
        'Performance'   => '性能压力',
        'Other'         => '其他',
    );

    static $CASE_METHOD = array(
        ''           => '',
        'Manual'     => '手动执行',
        'Automation' => '自动化脚本'
    );

    static $CASE_PLAN = array(
        ''           => '',
        'Function'   => '功能测试',
        'UnitTest'   => '单元测试',
        'BVT'        => '版本验证测试',
        'Intergrate' => '集成测试',
        'System'     => '系统测试',
        'Smoke'      => '冒烟测试',
        'Acceptance' => '验收测试',
    );

    static $SCRIPT_STATUS = array(
        ''              => '',
        'NotPlanned'    => '未计划',
        'Planning'      => '计划',
        'Blocked'       => '被阻止',
        'Coding'        => '正在编写',
        'CodingDone'    => '已完成',
        'Reviewed'      => '已评审',
    );

    static $BUG_RESOLUTIONS = array(
        ''             => '',
        'By Design'    => 'By Design',
        'Duplicate'    => 'Duplicate',
        'External'     => 'External',
        'Fixed'        => 'Fixed',
        'Not Repro'    => 'Not Repro',
        'Postponed'    => 'Postponed',
        'Will not Fix' => "Won't Fix"
    );

    static $CASE_STATUS = array(
        'Active'      => 'Active',
        'Blocked'     => 'Blocked',
        'Investigate' => 'Investigating',
        'Reviewed'    => 'Reviewed',
    );

    static $MARK_FOR_DELETION = array(
        '0' => '否',
        '1' => '是'
    );

    static $RESULT_STATUS = array(
        'Completed'   => 'Completed',
        'Investigate' => 'Investigating',
        'Resolved'    => 'Resolved',
    );

    static $RESULT_VALUE = array(
        ''     => '',
        'Pass' => 'Passed',
        'Fail' => 'Failed',
    );

    static $BUG_FIELD_TRANSLATE = array(
        'ProjectID' => 'product_id',
        'ModuleID' => 'productmodule_id',
        'ModulePath' => 'productmodule_id',
        'BugTitle' => 'title',
        'Resolution' => 'solution',
        'ReproSteps' => 'repeat_step',
        'BugStatus' => 'bug_status',
        'DuplicateID' => 'duplicate_id',
        'MailTo' => 'mail_to',
        'AssignedTo' => 'assign_to',
        'LinkID' => 'related_bug',
        'CaseID' => 'related_case',
        'ResultID' => 'related_result',
    );

    static $CASE_FIELD_TRANSLATE = array(
        'ProjectID' => 'product_id',
        'ModuleID' => 'productmodule_id',
        'ModulePath' => 'productmodule_id',
        'CaseTitle' => 'title',
        'CaseSteps' => 'case_step',
        'CaseStatus' => 'case_status',
        'MailTo' => 'mail_to',
        'AssignedTo' => 'assign_to',
        'LinkID' => 'related_case',
        'BugID' => 'related_bug',
        'ResultID' => 'related_result',
        'MarkForDeletion' => 'delete_flag',
    );

    static $RESULT_FIELD_TRANSLATE = array(
        'ProjectID' => 'product_id',
        'ModuleID' => 'productmodule_id',
        'ResultTitle' => 'title',
        'ResultValue' => 'result_value',
        'ResultStatus' => 'result_status',
        'ResultSteps' => 'result_step',
        'CaseID' => 'related_case_id',
        'BugID' => 'related_bug',
        'MailTo' => 'mail_to',
        'AssignedTo' => 'assign_to',
        'ResultOS' => 'BugOS',
        'ResultBuild' => 'OpenedBuild',
        'ResultBrowser' => 'BugBrowser',
        'ResultMachine' => 'BugMachine',
    );

    function upgrade1()
    {
        $this->beforeUpgrade();
        // create table test_option
        $createSql = 'DROP TABLE IF EXISTS `user_log`;
                      DROP TABLE IF EXISTS `test_option`;
                      DROP TABLE IF EXISTS `user_query`;
                      DROP TABLE IF EXISTS `user_template`;
                      DROP TABLE IF EXISTS `map_user_group`;
                      DROP TABLE IF EXISTS `map_product_group`;
                      DROP TABLE IF EXISTS `map_product_user`;
                      DROP TABLE IF EXISTS `map_user_bug`;
                      DROP TABLE IF EXISTS `map_user_case`;
                      DROP TABLE IF EXISTS `map_user_result`;
                      DROP TABLE IF EXISTS `bug_history`;
                      DROP TABLE IF EXISTS `bug_action`;
                      DROP TABLE IF EXISTS `result_history`;
                      DROP TABLE IF EXISTS `result_action`;
                      DROP TABLE IF EXISTS `case_history`;
                      DROP TABLE IF EXISTS `case_action`;
                      DROP TABLE IF EXISTS `result_info`;
                      DROP TABLE IF EXISTS `case_info`;
                      DROP TABLE IF EXISTS `bug_info`;
                      DROP TABLE IF EXISTS `field_config`;
                      DROP TABLE IF EXISTS `test_file`;
                      DROP TABLE IF EXISTS `product_module`;
                      DROP TABLE IF EXISTS `product`;
                      DROP TABLE IF EXISTS `user_group`;
                      DROP TABLE IF EXISTS `admin_history`;
                      DROP TABLE IF EXISTS `admin_action`;
                      DROP TABLE IF EXISTS `test_user`;
                      CREATE TABLE `test_option` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `option_name` VARCHAR(45) NOT NULL ,
                          `option_value` TEXT NOT NULL ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          PRIMARY KEY (`id`) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if(!isset($_REQUEST['admin']))
        {
            $result = 1;
            $info = t('bugfree', 'admin is required');
        }
        else
        {
            if($result)
            {
                $sql = 'INSERT INTO `test_option` (`option_name`, `option_value`, `created_at`, `created_by`, `updated_at`, `updated_by`, `lock_version`) VALUES'
                     . '("db_version", 16, NOW(), 0, NOW(), 0, 1),("SYSTEM_ADMIN", "' . mysql_real_escape_string($_REQUEST['admin']) .'", NOW(), 0, NOW(), 0, 1)';
                list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
            }
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
        }

        return array($result, $info);
    }

    function upgrade2()
    {
        // create table test_user
        $createSql = 'DROP TABLE IF EXISTS `test_user`;
              CREATE TABLE `test_user` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `username` VARCHAR(45) BINARY NOT NULL ,
                          `password` VARCHAR(45) NOT NULL ,
                          `realname` VARCHAR(45) NOT NULL ,
                          `email` VARCHAR(45) NOT NULL ,
                          `wangwang` VARCHAR(45) NULL ,
                          `email_flag` ENUM(\'0\',\'1\') NOT NULL ,
                          `wangwang_flag` ENUM(\'0\',\'1\') NOT NULL ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `is_dropped` ENUM(\'0\',\'1\') NOT NULL ,
                          `authmode` ENUM(\'ldap\',\'internal\') NOT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          UNIQUE INDEX `'.$this->newpre.'name_UNIQUE` (`username` ASC) ,
                          UNIQUE INDEX `'.$this->newpre.'realname_UNIQUE` (`realname` ASC) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        $insertSql = 'INSERT INTO `test_user` (`id`, `username`, `password`, `realname`, `email`, `wangwang`,'
                   . '`email_flag`, `wangwang_flag`, `created_by`, `created_at`, `updated_by`, `updated_at`, `is_dropped`, `authmode`, `lock_version`) VALUES ';
        $addDefaultSql = '(-1, "Active", "Active", "Active", "", "", "0", "0", 0, NOW(), 0, NOW(), "0", "ldap", 1),'
                       . '(-2, "Closed", "Closed", "Closed", "", "", "0", "0", 0, NOW(), 0, NOW(), "0", "ldap", 1)';
        $this->executeDataSQL($insertSql . $addDefaultSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`UserID`) as count FROM `' . $this->oldpre . 'TestUser`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestUser, the datas insert into test_user
                $fetchSql = 'SELECT `raw`.`UserID` as `id`,
                            `raw`.`UserName` as `username`,
                            `raw`.`UserPassword` as `password`,
                            `raw`.`RealName` as `realname`,
                            `raw`.`Email` as `email`,
                            `raw`.`wangwang` as `wangwang`,
                            `raw`.`NoticeFlag` as `email_flag`,
                            `raw`.`NoticeFlag` as `wangwang_flag`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`AddDate` as `created_at`,
                            `uu`.`UserID` as `updated_by`,
                            `raw`.`LastDate` as `updated_at`,
                            `raw`.`IsDroped` as `is_dropped`,
                            `raw`.`AuthMode` as `authmode`
                            FROM `' . $this->oldpre . 'TestUser` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`AddedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `raw`.`LastEditedBy`)
                            WHERE `raw`.`UserID` IN (' . join(',', $arr) . ')';
                $userResult = mysql_query($fetchSql, $this->con);
                $valueArr = array();
                $insertSql = 'REPLACE INTO `test_user` (`id`, `username`, `password`, `realname`, `email`, `wangwang`,'
                           . '`email_flag`, `wangwang_flag`, `created_by`, `created_at`, `updated_by`, `updated_at`, `is_dropped`, `authmode`, `lock_version`) VALUES ';
                while($userResult && $rows = mysql_fetch_array($userResult, MYSQL_ASSOC))
                {
                    $values = '';
                    $comma = '';
                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    if(($rows['email_flag'] & 2) != 2)
                    {
                        $rows['email_flag'] = '0';
                    }
                    else
                    {
                        $rows['email_flag'] = '1';
                    }

                    if(($rows['wangwang_flag'] & 1) != 1)
                    {
                        $rows['wangwang_flag'] = '0';
                    }
                    else
                    {
                        $rows['wangwang_flag'] = '1';
                    }

                    if('ldap' == strtolower($rows['authmode']))
                    {
                        $rows['authmode'] = 'ldap';
                    }
                    else
                    {
                        $rows['authmode'] = 'internal';
                    }

                    $rows['created_by'] = $rows['created_by'] + 0;
                    $rows['updated_by'] = (int)$rows['updated_by'];

                    $judgeUserSql = 'SELECT * FROM `' . $this->oldpre . 'TestUser` WHERE `RealName` = "'
                             . $rows['realname'] . '" AND `UserID` != "' . $rows['id'] . '"';
                    $judgeResult = mysql_query($judgeUserSql, $this->con);
                    if($judgeResult && mysql_fetch_array($judgeResult))
                    {
                        $rows['realname'] .= '(' . $rows['username'] . ')';
                    }

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }

                    $valueArr[] = '(' . $values . ', 1)';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'test_user successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade3()
    {
        // create table user_group
        $createSql = 'DROP TABLE IF EXISTS `user_group`;
                      CREATE TABLE `user_group` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `name` VARCHAR(255) NOT NULL ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `is_dropped` ENUM(\'0\',\'1\') NOT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          PRIMARY KEY (`id`))
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`GroupID`) as count FROM `' . $this->oldpre . 'TestGroup`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                $fetchSql = 'SELECT `GroupID` as `id`,
                    `GroupName` as `name`,
                    `tg`.`AddDate` as `created_at`,
                    `cu`.`UserID` as `created_by`,
                    `tg`.`LastDate` as `updated_at`,
                    `uu`.`UserID` as `updated_by`
                    FROM `' . $this->oldpre . 'TestGroup` `tg`
                    LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `tg`.`AddedBy`)
                    LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `tg`.`LastEditedBy`)
                    WHERE `GroupID` IN (' . join(',', $arr) . ')';
                $groupResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'REPLACE INTO `user_group` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`,'
                           . '`is_dropped`, `lock_version`) VALUES ';
                $valueArr = array();
                while($groupResult && $rows = mysql_fetch_array($groupResult, MYSQL_ASSOC))
                {
                    $values = '';
                    $comma = '';

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['created_by'] = $rows['created_by'] + 0;
                    $rows['updated_by'] = $rows['updated_by'] + 0;

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }

                    $valueArr[] = '(' . $values . ', "0", 1)';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'user_group successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade4()
    {
        // create table product
        $createSql = 'DROP TABLE IF EXISTS `bug_info`;
                      DROP TABLE IF EXISTS `case_info`;
                      DROP TABLE IF EXISTS `result_info`;
                      DROP TABLE IF EXISTS `product_module`;
                      DROP TABLE IF EXISTS `field_config`;
                      DROP TABLE IF EXISTS `product`;
                      CREATE TABLE `product` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `name` VARCHAR(255) NOT NULL ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `is_dropped` ENUM(\'0\',\'1\') NOT NULL ,
                          `solution_value` TEXT NULL ,
                          `display_order` SMALLINT NOT NULL ,
                          `bug_step_template` TEXT NULL ,
                          `case_step_template` TEXT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          UNIQUE INDEX `'.$this->newpre.'name_UNIQUE` (`name` ASC) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ProjectID`) as count FROM `' . $this->oldpre . 'TestProject`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                $fetchSql = 'SELECT `ProjectID` as `id`,
                    `ProjectName` as `name`,
                    `product`.`AddDate` as `created_at`,
                    `cu`.`UserID` as `created_by`,
                    `product`.`LastDate` as `updated_at`,
                    `uu`.`UserID` as `updated_by`,
                    `product`.`IsDroped` as `is_dropped`,
                    `DisplayOrder` as `display_order`
                    FROM `' . $this->oldpre . 'TestProject` `product`
                    LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `product`.`AddedBy`)
                    LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `product`.`LastEditedBy`)
                    WHERE `ProjectID` IN (' . join(',', $arr) . ')';
                $productResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'REPLACE INTO `product` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`,'
                           . '`is_dropped`, `display_order`, '
                           . '`solution_value`, `lock_version`, `bug_step_template`, `case_step_template`) VALUES ';
                $valueArr = array();
                while($productResult && $rows = mysql_fetch_array($productResult, MYSQL_ASSOC))
                {
                    $values = '';
                    $comma = '';

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['name'] = str_replace('/', '-', $rows['name']);
                    $rows['created_by'] = $rows['created_by'] + 0;
                    $rows['updated_by'] = (int)$rows['updated_by'];

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }

                    $valueArr[] = '(' . $values . ', '
                            . '"By Design,Duplicate,External,Fixed,Not Repro,Postponed,Won\'t Fix", 1,'
                            . '"' . t('bugfree', 'Bug step template')
                            . '", "' . t('bugfree', 'Case step template') . '")';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'product successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade5()
    {
        // create table map_product_user
        $createSql = 'DROP TABLE IF EXISTS `map_product_user`;
                      CREATE TABLE `map_product_user` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `test_user_id` INT NOT NULL ,
                          `product_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_product_user_test_user1` (`test_user_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_product_user_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_product_user_test_user1`
                            FOREIGN KEY (`test_user_id` )
                            REFERENCES `' . $this->newpre . 'test_user` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_product_user_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre .'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ProjectID`) as count FROM `' . $this->oldpre . 'TestProject`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                $fetchSql = 'SELECT `ProjectID` as `product_id`, `ProjectManagers` as `usernames` FROM `' . $this->oldpre . 'TestProject` WHERE `ProjectID` IN (' . join(',', $arr) . ')';
                $projectResult = mysql_query($fetchSql, $this->con);
                while($projectResult && $rows = mysql_fetch_array($projectResult, MYSQL_ASSOC))
                {
                    $insertSql = 'INSERT INTO `map_product_user` (`product_id`, `test_user_id`) VALUES ';
                    $valueArr = array();
                    $userIds = $this->getUserIdsByName($rows['usernames']);
                    foreach($userIds as $userId)
                    {
                        $valueArr[] = '('. $rows['product_id'] . ',' . $userId . ')';
                    }

                    if(!empty($valueArr))
                    {
                        $insertSql .= join(',', $valueArr);
                        list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                        if(!$subResult)
                        {
                            $result = $subResult;
                            $infos += $subInfos;
                        }
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'map_product_user successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade6()
    {
        // create table map_product_user
        $createSql = 'DROP TABLE IF EXISTS `map_product_group`;
                      CREATE TABLE `map_product_group` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `user_group_id` INT NOT NULL ,
                          `product_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_product_group_user_group1` (`user_group_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_product_group_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_product_group_user_group1`
                            FOREIGN KEY (`user_group_id` )
                            REFERENCES `' . $this->newpre . 'user_group` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_product_group_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ProjectID`) as count FROM `' . $this->oldpre . 'TestProject`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                $fetchSql = 'SELECT `ProjectID` as `product_id`, `ProjectGroupIDs` as `group_ids` FROM `' . $this->oldpre . 'TestProject` WHERE `ProjectID` IN (' . join(',', $arr) . ')';
                $projectResult = mysql_query($fetchSql, $this->con);
                while($projectResult && $rows = mysql_fetch_array($projectResult, MYSQL_ASSOC))
                {
                    $insertSql = 'REPLACE INTO `map_product_group` (`product_id`, `user_group_id`) VALUES ';
                    $valueArr = array();
                    $group_ids = array();
                    if($rows['group_ids'])
                    {
                        $group_ids = explode(',', trim($rows['group_ids'], ','));
                    }
                    foreach($group_ids as $group_id)
                    {
                        $judgeSql = 'SELECT * FROM `' . $this->newpre . 'user_group` WHERE `id` = ' . $group_id;
                        $judgeResult = mysql_query($judgeSql);
                        if(!$judgeResult || !mysql_fetch_array($judgeResult))
                        {
                            continue;
                        }
                        $valueArr[] = '(' . $rows['product_id'] . ',' . $group_id . ')';
                    }

                    if(!empty($valueArr))
                    {
                        $insertSql .= join(',', $valueArr);
                        list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                        if(!$subResult)
                        {
                            $result = $subResult;
                            $infos += $subInfos;
                        }
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'map_product_group successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade7()
    {
        // create table map_user_group
        $createSql = 'DROP TABLE IF EXISTS `map_user_group`;
                      CREATE TABLE `map_user_group` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `test_user_id` INT NOT NULL ,
                          `user_group_id` INT NOT NULL ,
                          `is_admin` ENUM(\'0\',\'1\') NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_user_group_test_user1` (`test_user_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_user_group_user_group1` (`user_group_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_group_test_user1`
                            FOREIGN KEY (`test_user_id` )
                            REFERENCES `' . $this->newpre . 'test_user` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_group_user_group1`
                            FOREIGN KEY (`user_group_id` )
                            REFERENCES `' . $this->newpre . 'user_group` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`GroupID`) as count FROM `' . $this->oldpre . 'TestGroup`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                $fetchSql = 'SELECT `GroupID` as `group_id`, `GroupManagers` as `group_admins`, `GroupUser` as `group_users` FROM `' . $this->oldpre . 'TestGroup` WHERE `GroupID` IN (' . join(',', $arr) . ')';
                $groupResult = mysql_query($fetchSql, $this->con);
                while($groupResult && $groupRow = mysql_fetch_array($groupResult, MYSQL_ASSOC))
                {
                    $insertSql = 'INSERT INTO `map_user_group` (`test_user_id`, `user_group_id`, `is_admin`) VALUES ';
                    $valueArr = array();
                    $admins = empty($groupRow['group_admins']) ? array() : explode(',', trim($groupRow['group_admins'], ','));
                    $users = empty($groupRow['group_users']) ? array() : explode(',', trim($groupRow['group_users'], ','));
                    $users = array_diff($users, $admins);
                    foreach($users as $user)
                    {
                        $fetchUserSql = 'SELECT `UserID` as `user_id` FROM `' . $this->oldpre
                                . 'TestUser` WHERE `UserName` = "' . mysql_real_escape_string($user). '"';
                        $userResult = mysql_query($fetchUserSql, $this->con);
                        $userRow = mysql_fetch_array($userResult, MYSQL_ASSOC);
                        if(!empty($userRow['user_id']))
                        {
                            $valueArr[] = '(' . $userRow['user_id'] . ',' . $groupRow['group_id'] . ', "0")';
                        }
                    }

                    foreach($admins as $admin)
                    {
                        $fetchUserSql = 'SELECT `UserID` as `user_id` FROM `' . $this->oldpre
                                . 'TestUser` WHERE `UserName` = "' . mysql_real_escape_string($admin). '"';
                        $userResult = mysql_query($fetchUserSql, $this->con);
                        $userRow = mysql_fetch_array($userResult, MYSQL_ASSOC);
                        if(!empty($userRow['user_id']))
                        {
                            $valueArr[] = '(' . $userRow['user_id'] . ',' . $groupRow['group_id'] . ', "1")';
                        }
                    }

                    if(!empty($valueArr))
                    {
                        $insertSql .= join(',', $valueArr);
                        list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                        if(!$subResult)
                        {
                            $result = $subResult;
                            $infos += $subInfos;
                        }
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'map_user_group successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade8()
    {
        // create table user_template
        $createSql = 'DROP TABLE IF EXISTS `user_template`;
                      CREATE TABLE `user_template` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `type` ENUM(\'bug\',\'case\',\'result\') NOT NULL ,
                          `template_content` TEXT NULL ,
                          `title` VARCHAR(45) NOT NULL ,
                          `product_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_user_template_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_user_template_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'user_template successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade9()
    {
        // create table user_query
        $createSql = 'DROP TABLE IF EXISTS `user_query`;
                      CREATE TABLE `user_query` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `query_type` ENUM(\'bug\',\'case\',\'result\') NOT NULL ,
                          `query_string` TEXT NULL ,
                          `andorlist` TEXT NULL ,
                          `fieldlist` TEXT NULL ,
                          `operatorlist` TEXT NULL ,
                          `left_parentheses` TEXT NULL ,
                          `right_parentheses` TEXT NULL ,
                          `product_id` INT NOT NULL ,
                          `title` VARCHAR(100) NOT NULL ,
                          `valuelist` TEXT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_USERQUERY_PRODUCT1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_USERQUERY_PRODUCT1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'user_query successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade10()
    {
        // create table admin_action
        $createSql = 'DROP TABLE IF EXISTS `admin_history`;
                      DROP TABLE IF EXISTS `admin_action`;
                      CREATE TABLE `admin_action` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `action_type` VARCHAR(255) NOT NULL ,
                          `target_table` VARCHAR(45) NOT NULL ,
                          `target_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'admin_action successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade11()
    {
        // create table admin_history
        $createSql = 'DROP TABLE IF EXISTS `admin_history`;
                      CREATE TABLE `admin_history` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `action_field` VARCHAR(45) NOT NULL ,
                          `old_value` TEXT NULL ,
                          `new_value` TEXT NULL ,
                          `adminaction_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_RESULTHISTORY_RESULTACTION1` (`adminaction_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_RESULTHISTORY_RESULTACTION10`
                            FOREIGN KEY (`adminaction_id` )
                            REFERENCES `' . $this->newpre . 'admin_action` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'admin_history successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade12()
    {
        // create table user_log
        $createSql = 'DROP TABLE IF EXISTS `user_log`;
                      CREATE TABLE `user_log` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `ip` VARCHAR(45) NOT NULL ,
                          PRIMARY KEY (`id`) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`LogID`) as count FROM `' . $this->oldpre . 'TestUserLog`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestUserLog, the datas insert into user_log
                $fetchSql = 'SELECT `raw`.`LogID` as `id`,
                            `raw`.`LoginTime` as `created_at`,
                            `raw`.`LoginIP` as `ip`,
                            `cu`.`UserID` as `created_by`
                            FROM `' . $this->oldpre . 'TestUserLog` `raw`
                            JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`UserName`)
                            WHERE `raw`.`LogID` IN (' . join(',', $arr) . ')';
                $logResult = mysql_query($fetchSql, $this->con);

                $insertSql = 'REPLACE INTO `user_log` (`id`, `created_at`, `ip`, `created_by`) VALUES ';
                $valueArr = array();
                while($logResult && $rows = mysql_fetch_array($logResult, MYSQL_ASSOC))
                {
                    $values = '';
                    $comma = '';

                    $rows['created_by'] = $rows['created_by'] + 0;
                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'user_log successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade13()
    {
        // create table test_file
        $createSql = 'DROP TABLE IF EXISTS `test_file`;
                      CREATE TABLE `test_file` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `file_title` VARCHAR(255) NOT NULL ,
                          `file_location` TEXT NOT NULL ,
                          `file_type` VARCHAR(45) NULL ,
                          `file_size` VARCHAR(45) NOT NULL ,
                          `is_dropped` ENUM(\'0\',\'1\') NOT NULL ,
                          `target_id` INT NOT NULL ,
                          `target_type` ENUM(\'bug\',\'case\',\'result\') NOT NULL ,
                          `add_action_id` INT NOT NULL ,
                          `delete_action_id` INT NULL ,
                          PRIMARY KEY (`id`) )
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`FileID`) as count FROM `' . $this->oldpre . 'TestFile`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestFile, the datas insert into test_file
                $fetchSql = 'SELECT `raw`.`FileID` as `id`,
                            `raw`.`ActionID` as `add_action_id`,
                            `raw`.`FileTitle` as `file_title`,
                            `raw`.`FileName` as `file_location`,
                            `raw`.`FileType` as `file_type`,
                            `raw`.`FileSize` as `file_size`,
                            `raw`.`IsDroped` as `is_dropped`,
                            `ta`.`IdValue` as `target_id`,
                            `ta`.`ActionTarget` as `target_type`
                            FROM `' . $this->oldpre . 'TestFile` `raw`
                            JOIN `' . $this->oldpre . 'TestAction` `ta` ON (`ta`.`ActionID` = `raw`.`ActionID`)
                            WHERE `raw`.`FileID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'REPLACE INTO `test_file` (`id`, `add_action_id`, `file_title`, `file_location`, `file_type`,'
                           . ' `file_size`, `is_dropped`, `target_id`, `target_type`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $values = '';
                    $comma = '';
                    $rows['target_type'] = strtolower($rows['target_type']);
                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }

                    $valueArr[] = '(' . $values . ')';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'test_file successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade14()
    {
        // create table product_module
        $createSql = 'DROP TABLE IF EXISTS `product_module`;
                      CREATE TABLE `product_module` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `name` VARCHAR(45) NOT NULL ,
                          `grade` SMALLINT NOT NULL ,
                          `owner` INT NULL ,
                          `display_order` SMALLINT NOT NULL ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `full_path_name` TEXT NOT NULL ,
                          `product_id` INT NOT NULL ,
                          `parent_id` INT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_PRODUCTMODULE_PRODUCT1` (`product_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_PRODUCTMODULE_PRODUCTMODULE1` (`parent_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_PRODUCTMODULE_PRODUCT1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT count(*) as count FROM `' . $this->oldpre . 'TestModule`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $idSql = 'SELECT `ModuleID` FROM `' . $this->oldpre
                       . 'TestModule` ORDER BY `ModuleGrade` ASC,`ModuleID` LIMIT ' . $start . ',' . self::PAGE_SIZE;
                $idResult = mysql_query($idSql, $this->con);
                $ids = array();
                while($idResult && $idRow = mysql_fetch_array($idResult))
                {
                    $ids[] = $idRow['ModuleID'];
                }

                // fetch datas from TestModule, the datas insert into product_module
                $fetchSql = 'SELECT `raw`.`ModuleID` as `id`,
                                `raw`.`ModuleName` as `name`,
                                `raw`.`ModuleType` as `type`,
                                `ow`.`UserID` as `owner`,
                                `raw`.`ModuleGrade` as `grade`,
                                `raw`.`DisplayOrder` as `display_order`,
                                `raw`.`AddDate` as `created_at`,
                                `raw`.`LastDate` as `updated_at`,
                                `raw`.`ProjectID` as `product_id`,
                                `raw`.`ParentID` as `parent_id`,
                                `raw`.`ModulePath` as `full_path_name`
                                FROM `' . $this->oldpre . 'TestModule` `raw`
                                LEFT JOIN `' . $this->oldpre . 'TestUser` `ow` ON (`ow`.`UserName` = `raw`.`ModuleOwner`)
                                WHERE `raw`.`ModuleID` IN (' . join(',', $ids) . ') ORDER BY grade ASC';
                $start += self::PAGE_SIZE;
                $moduleResult = mysql_query($fetchSql, $this->con);
                while($moduleResult && $rows = mysql_fetch_array($moduleResult, MYSQL_ASSOC))
                {
                    $fields = '';
                    $values = '';
                    $comma = '';

                    // validate data
                    $judgeSql = 'SELECT * FROM `' . $this->newpre . 'product` WHERE `id` = ' . $rows['product_id'];
                    $judgeResult = mysql_query($judgeSql);
                    if(!$judgeResult || !mysql_fetch_array($judgeResult))
                    {
                        continue;
                    }

                    if(0 != $rows['parent_id'])
                    {
                        $judgeSql = 'SELECT * FROM `' . $this->newpre . 'product_module` WHERE `id` = ' . $rows['parent_id'];
                        $judgeResult = mysql_query($judgeSql);
                        if(!$judgeResult || !mysql_fetch_array($judgeResult))
                        {
                            $arr = explode('/' . $rows['name'], $rows['full_path_name']);
                            if(isset($arr[0]))
                            {
                                $bugModuleSql = 'SELECT * FROM `' . $this->newpre . 'product_module` WHERE `full_path_name` = "' . $arr[0] . '"'
                                       . ' AND `product_id` = ' . $rows['product_id'];
                                $bugModuleResult = mysql_query($bugModuleSql);
                                $module = mysql_fetch_array($bugModuleResult);
                                if(null !== $module)
                                {
                                    $rows['parent_id'] = $module['id'];
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            else
                            {
                                continue;
                            }
                        }
                    }

                    if('Case' == $rows['type'])
                    {
                        $caseSql = 'SELECT * FROM `' . $this->oldpre . 'TestModule` WHERE `ModulePath` = "' . $rows['full_path_name']
                                  . '" AND `ModuleType` = "Bug" AND `ProjectID` = ' . $rows['product_id'];
                        $caseResult = mysql_query($caseSql);
                        if(mysql_fetch_array($caseResult))
                        {
                            continue;
                        }
                    }

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['name'] = str_replace('/', '-', $rows['name']);

                    foreach($rows as $key => $val)
                    {
                        if('type' == $key)
                        {
                            continue;
                        }
                        $fields .= $comma . '`' . $key . '`';
                        if(('parent_id' == $key && 0 == $val) || ('owner' == $key && empty($val)))
                        {
                            $values .= $comma . 'NULL';
                            continue;
                        }
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }

                    $fields .= $comma . '`lock_version`';
                    $values .= $comma . '1';

                    $sql = "INSERT INTO `product_module` ($fields) VALUES ($values);";

                    list($subResult, $subInfos) = $this->executeDataSQL($sql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'product_module successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade15()
    {
        // create table field_config
        $createSql = 'DROP TABLE IF EXISTS `field_config`;
                      CREATE TABLE `field_config` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `field_name` VARCHAR(45) NOT NULL ,
                          `field_type` VARCHAR(45) NOT NULL ,
                          `field_value` TEXT NULL ,
                          `default_value` TEXT NULL ,
                          `is_dropped` ENUM(\'0\',\'1\') NOT NULL ,
                          `field_label` VARCHAR(45) NOT NULL ,
                          `type` ENUM(\'bug\',\'case\',\'result\') NOT NULL ,
                          `belong_group` VARCHAR(45) NOT NULL ,
                          `display_order` SMALLINT NOT NULL ,
                          `editable_action` VARCHAR(255) NULL ,
                          `validate_rule` VARCHAR(45) NOT NULL ,
                          `match_expression` VARCHAR(255) NULL ,
                          `product_id` INT NOT NULL ,
                          `edit_in_result` ENUM(\'0\',\'1\') NULL DEFAULT \'0\' ,
                          `result_group` VARCHAR(45) NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          `is_required` ENUM(\'0\',\'1\') NOT NULL DEFAULT \'0\' ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_FIELDCONFIG_PRODUCT1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_FIELDCONFIG_PRODUCT1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ProjectID`) as count FROM `' . $this->oldpre . 'TestProject`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from field_config, the datas insert into field_config
                $fetchProjectSql = 'SELECT `ProjectID`, `FieldSet` FROM `' . $this->oldpre . 'TestProject` WHERE `ProjectID` IN (' . join(',', $arr) . ')';
                $projectResult = mysql_query($fetchProjectSql, $this->con);
                while($projectResult && $project = mysql_fetch_array($projectResult, MYSQL_ASSOC))
                {
                    $hasBugCustomField = false;
                    $hasCaseCustomField = false;
                    if(!empty($project['FieldSet']))
                    {
                        $xml = simplexml_load_string($project['FieldSet']);
                        $fieldXml = $xml->xpath('/fieldset/fields[@type="Bug"]/field');
                        $fields = $this->fieldXmlToArr($fieldXml);
                        if(!empty($fields))
                        {
                            $hasBugCustomField = true;
                            list($result, $infos) = $this->updateBugCustomField($project['ProjectID'], $fields);
                        }
                        if($result)
                        {
                            $fieldXml = $xml->xpath('/fieldset/fields[@type="Case"]/field');
                            $fields = $this->fieldXmlToArr($fieldXml);
                            if(!empty($fields))
                            {
                                $hasCaseCustomField = true;
                                list($result, $infos) = $this->updateCaseCustomField($project['ProjectID'], $fields);
                            }
                        }
                    }

                    if($result && !$hasBugCustomField)
                    {
                        list($result, $infos) = $this->createBugCustomField($project['ProjectID']);
                    }

                    if($result && !$hasCaseCustomField)
                    {
                        list($result, $infos) = $this->createCaseCustomField($project['ProjectID']);
                    }

                    if($result)
                    {
                        list($result, $infos) = $this->createResultCustomField($project['ProjectID']);
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'field_config successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade16()
    {
        // create table bug_info
        $createSql = 'DROP TABLE IF EXISTS `bug_history`;
                      DROP TABLE IF EXISTS `bug_action`;
                      DROP TABLE IF EXISTS `bug_info`;
                      CREATE TABLE `bug_info` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `bug_status` VARCHAR(45) NOT NULL ,
                          `assign_to` INT NULL ,
                          `title` VARCHAR(255) NOT NULL ,
                          `mail_to` TEXT NULL ,
                          `repeat_step` TEXT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          `resolved_at` DATETIME NULL ,
                          `resolved_by` INT NULL ,
                          `closed_at` DATETIME NULL ,
                          `closed_by` INT NULL ,
                          `related_bug` VARCHAR(255) NULL ,
                          `related_case` VARCHAR(255) NULL ,
                          `related_result` VARCHAR(255) NULL ,
                          `productmodule_id` INT NULL ,
                          `modified_by` TEXT NOT NULL ,
                          `solution` VARCHAR(45) NULL ,
                          `duplicate_id` VARCHAR(255) NULL ,
                          `product_id` INT NOT NULL ,
                          `reopen_count` INT NOT NULL ,
                          `priority` TINYINT(4) NULL,
                          `severity` TINYINT(4) NOT NULL,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'bug_status` (`bug_status` ASC) ,
                          INDEX `'.$this->newpre.'assign_to` (`assign_to` ASC) ,
                          INDEX `'.$this->newpre.'title` (`title` ASC) ,
                          INDEX `'.$this->newpre.'resolved_by` (`resolved_by` ASC) ,
                          INDEX `'.$this->newpre.'closed_by` (`closed_by` ASC) ,
                          INDEX `'.$this->newpre.'updated_by` (`updated_by` ASC) ,
                          INDEX `'.$this->newpre.'fk_bug_info_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_bug_info_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`BugID`) as count FROM `' . $this->oldpre . 'BugInfo`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestUser, the datas insert into test_user
                $fetchSql = 'SELECT `raw`.`BugID` as `id`,
                            `raw`.`ProjectID` as `product_id`,
                            `raw`.`ModuleID` as `productmodule_id`,
                            `raw`.`BugTitle` as `title`,
                            `raw`.`ReproSteps` as `repeat_step`,
                            `raw`.`BugStatus` as `bug_status`,
                            `raw`.`LinkID` as `related_bug`,
                            `raw`.`CaseID` as `related_case`,
                            `raw`.`ResultID` as `related_result`,
                            `raw`.`DuplicateID` as `duplicate_id`,
                            `raw`.`MailTo` as `mail_to`,
                            `raw`.`Resolution` as `solution`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`OpenedDate` as `created_at`,
                            `uu`.`UserID` as `updated_by`,
                            `raw`.`LastEditedDate` as `updated_at`,
                            `as`.`UserID` as `assign_to`,
                            `re`.`UserID` as `resolved_by`,
                            `raw`.`ResolvedDate` as `resolved_at`,
                            `cl`.`UserID` as `closed_by`,
                            `raw`.`ClosedDate` as `closed_at`,
                            `raw`.`BugSeverity` as `severity`,
                            `raw`.`BugPriority` as `priority`,
                            `raw`.`AssignedTo`,
                            `raw`.`ModifiedBy`,
                            `raw`.`BugType`,
                            `raw`.`HowFound`,
                            `raw`.`BugBrowser`,
                            `raw`.`BugOS`,
                            `raw`.`OpenedBuild`,
                            `raw`.`ResolvedBuild`,
                            `raw`.`BugSubStatus`,
                            `raw`.`BugMachine`,
                            `raw`.`BugKeyword`
                            FROM `' . $this->oldpre . 'BugInfo` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`OpenedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `raw`.`LastEditedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `as` ON (`as`.`UserName` = `raw`.`AssignedTo`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `re` ON (`re`.`UserName` = `raw`.`ResolvedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cl` ON (`cl`.`UserName` = `raw`.`ClosedBy`)
                            WHERE `raw`.`BugID` IN (' . join(',', $arr) . ')';
                $bugResult = mysql_query($fetchSql, $this->con);
                $customFieldArr = array('BugType', 'HowFound', 'BugBrowser', 'BugOS', 'OpenedBuild', 'ResolvedBuild', 'BugSubStatus', 'BugMachine', 'BugKeyword');
                $insertSql = 'INSERT INTO `bug_info` (`id`, `product_id`, `productmodule_id`, `title`, `repeat_step`, `bug_status`,'
                           . '`related_bug`, `related_case`, `related_result`, `duplicate_id`, `mail_to`, `solution`, `created_by`, '
                           . '`created_at`, `updated_by`, `updated_at`, `assign_to`, `resolved_by`, `resolved_at`, `closed_by`, `closed_at`, `severity`, `priority`, `modified_by`, `lock_version`, `reopen_count`) VALUES ';
                $valueArr = array();
                $sqlArr = array();
                $idArr = array();
                $productIdArr = array();
                while($bugResult && $rows = mysql_fetch_array($bugResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $judgeSql = 'SELECT * FROM `' . $this->newpre . 'product_module` WHERE `id` = ' . $rows['productmodule_id'];
                    $judgeResult = mysql_query($judgeSql);
                    if(!$judgeResult || !mysql_fetch_array($judgeResult))
                    {
                        $rows['productmodule_id'] = null;
                    }
                    $sql = '`bug_id` = "' . $rows['id'] . '"';
                    $values = '';
                    $comma = '';

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['BugType'] = t('bugfree', $rows['BugType']);
                    $rows['HowFound'] = t('bugfree', $rows['HowFound']);
                    $rows['BugBrowser'] = t('bugfree', $rows['BugBrowser']);
                    $rows['BugOS'] = t('bugfree', $rows['BugOS']);
                    $rows['BugSubStatus'] = t('bugfree', $rows['BugSubStatus']);
                    $rows['solution'] = t('bugfree', $rows['solution']);

                    $rows['mail_to'] = $this->getRealnamesByMailTo($rows['mail_to']);
                    $rows['repeat_step'] = $this->bbcode2html($rows['repeat_step']);
                    if('Active' == $rows['AssignedTo'])
                    {
                        $rows['assign_to'] = -1;
                    }
                    else if('Closed' == $rows['AssignedTo'])
                    {
                        $rows['assign_to'] = -2;
                    }
                    unset($rows['AssignedTo']);

                    foreach($rows as $key => $val)
                    {
                        if(in_array($key, $customFieldArr))
                        {
                            if(is_null($val) || '' == $val)
                            {
                                $sql .= ',`' . $key .'` = NULL';
                            }
                            else
                            {
                                $sql .= ',`' . $key .'` = "' . mysql_real_escape_string($val) . '"';
                            }
                            continue;
                        }

                        if('ModifiedBy' == $key)
                        {
                            continue;
                        }

                        if('0000-00-00 00:00:00' == $val)
                        {
                            $values .= $comma . 'NULL';
                            continue;
                        }

                        if(is_null($val) || '' == $val)
                        {
                            $values .= $comma . 'NULL';
                        }
                        else
                        {
                            $val = trim($val, ',');
                            $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        }
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ',"' . join(',', $this->getUserIdsByName($rows['ModifiedBy'])) . '", 1, "' . $this->getActivatedCount($rows['id']) . '")';
                    $idArr[] = $rows['id'];
                    $sqlArr[] = $sql;
                    $productIdArr[] = $rows['product_id'];
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if($subResult)
                    {
                        foreach($sqlArr as $key => $sql)
                        {
                            list($subResult, $subInfos) = $this->insertCustemField($productIdArr[$key], 'bug', $idArr[$key], $sql);
                            if(!$subResult)
                            {
                                $infos += $subInfos;
                            }
                        }
                    }
                    else
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'bug_info successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade17()
    {
        // create table bug_action
        $createSql = 'DROP TABLE IF EXISTS `bug_history`;
                      DROP TABLE IF EXISTS `bug_action`;
                      CREATE TABLE `bug_action` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `action_type` VARCHAR(255) NOT NULL ,
                          `action_note` TEXT NULL ,
                          `buginfo_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_BUGACTION_BUGINFO1` (`buginfo_id` ASC) ,
                          INDEX `'.$this->newpre.'action_type` (`action_type` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_BUGACTION_BUGINFO1`
                            FOREIGN KEY (`buginfo_id` )
                            REFERENCES `' . $this->newpre . 'bug_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ActionID`) as count FROM `' . $this->oldpre . 'TestAction` WHERE `ActionTarget` = "Bug"';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT `raw`.`ActionID` as `id`,
                            `raw`.`IdValue` as `buginfo_id`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`ActionDate` as `created_at`,
                            `raw`.`ActionNote` as `action_note`,
                            `raw`.`ActionType` as `action_type`
                            FROM `' . $this->oldpre . 'TestAction` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`ActionUser`)
                            RIGHT JOIN `' . $this->oldpre . 'BugInfo` `bi` ON (`bi`.`BugID` = `raw`.`IdValue`)
                            WHERE `ActionTarget` = "Bug"
                            AND `raw`.`ActionID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `bug_action` (`id`, `buginfo_id`, `created_by`, `created_at`, `action_note`, `action_type`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $values = '';
                    $comma = '';
                    $rows['action_type'] = strtolower($rows['action_type']);
                    $rows['action_note'] = $this->bbcode2html($rows['action_note']);
                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }
                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'bug_action successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade18()
    {
        // create table bug_action
        $createSql = 'DROP TABLE IF EXISTS `bug_history`;
                      CREATE TABLE `bug_history` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `action_field` VARCHAR(45) NOT NULL ,
                          `old_value` TEXT NULL ,
                          `new_value` TEXT NULL ,
                          `bugaction_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_BUGHISTORY_BUGACTION1` (`bugaction_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_BUGHISTORY_BUGACTION1`
                            FOREIGN KEY (`bugaction_id` )
                            REFERENCES `' . $this->newpre . 'bug_action` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`raw`.`HistoryID`) as count FROM `' . $this->oldpre . 'TestHistory` `raw`
                         RIGHT JOIN `' . $this->newpre .'bug_action` `action` ON (`action`.`id` = `raw`.`ActionID`)';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT
                            `raw`.`HistoryID` as `id`,
                            `raw`.`ActionID` as `bugaction_id`,
                            `raw`.`ActionField` as `action_field`,
                            `raw`.`OldValue` as `old_value`,
                            `raw`.`NewValue` as `new_value`
                            FROM `' . $this->oldpre . 'TestHistory` `raw`
                            RIGHT JOIN `' . $this->newpre .'bug_action` `action` ON (`action`.`id` = `raw`.`ActionID`)
                            WHERE `raw`.`HistoryID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `bug_history` (`id`, `bugaction_id`, `action_field`, `old_value`, `new_value`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $values = '';
                    $comma = '';

                    if(isset(self::$BUG_FIELD_TRANSLATE[$rows['action_field']]))
                    {
                        $rows['action_field'] = self::$BUG_FIELD_TRANSLATE[$rows['action_field']];
                    }

                    if('repeat_step' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->bbcode2html($rows['old_value']);
                        $rows['new_value'] = $this->bbcode2html($rows['new_value']);
                    }

                    if('assign_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealNameByName($rows['old_value']);
                        $rows['new_value'] = $this->getRealNameByName($rows['new_value']);
                    }

                    if('mail_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealnamesByMailTo($rows['old_value']);
                        $rows['new_value'] = $this->getRealnamesByMailTo($rows['new_value']);
                    }

                    if('BugType' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('HowFound' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('BugBrowser' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('BugOS' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('BugSubStatus' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('solution' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'bug_history successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade19()
    {
        // create table case_info
        $createSql = 'DROP TABLE IF EXISTS `case_info`;
                      CREATE TABLE `case_info` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `case_status` VARCHAR(45) NOT NULL ,
                          `assign_to` INT NULL ,
                          `title` VARCHAR(255) NOT NULL ,
                          `mail_to` TEXT NULL ,
                          `case_step` TEXT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          `related_bug` VARCHAR(255) NULL ,
                          `related_case` VARCHAR(255) NULL ,
                          `related_result` VARCHAR(255) NULL ,
                          `productmodule_id` INT NULL ,
                          `modified_by` TEXT NOT NULL ,
                          `delete_flag` ENUM(\'0\',\'1\') NOT NULL ,
                          `product_id` INT NOT NULL ,
                          `priority` TINYINT(4) NULL,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'created_by` (`created_by` ASC) ,
                          INDEX `'.$this->newpre.'updated_by` (`updated_by` ASC) ,
                          INDEX `'.$this->newpre.'assign_to` (`assign_to` ASC) ,
                          INDEX `'.$this->newpre.'title` (`title` ASC) ,
                          INDEX `'.$this->newpre.'fk_case_info_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_case_info_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`CaseID`) as count FROM `' . $this->oldpre . 'CaseInfo`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestUser, the datas insert into test_user
                $fetchSql = 'SELECT `raw`.`CaseID` as `id`,
                            `raw`.`ProjectID` as `product_id`,
                            `raw`.`ModuleID` as `productmodule_id`,
                            `raw`.`CaseTitle` as `title`,
                            `raw`.`CaseSteps` as `case_step`,
                            `raw`.`CaseStatus` as `case_status`,
                            `raw`.`LinkID` as `related_case`,
                            `raw`.`BugID` as `related_bug`,
                            `raw`.`ResultID` as `related_result`,
                            `raw`.`MailTo` as `mail_to`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`OpenedDate` as `created_at`,
                            `uu`.`UserID` as `updated_by`,
                            `raw`.`LastEditedDate` as `updated_at`,
                            `as`.`UserID` as `assign_to`,
                            `raw`.`MarkForDeletion` as `delete_flag`,
                            `raw`.`CasePriority` as `priority`,
                            `raw`.`AssignedTo`,
                            `raw`.`CaseType`,
                            `raw`.`CaseMethod`,
                            `raw`.`CasePlan`,
                            `raw`.`ScriptStatus`,
                            `raw`.`ScriptedBy`,
                            `raw`.`ScriptedDate`,
                            `raw`.`ScriptLocation`,
                            `raw`.`CaseKeyword`,
                            `raw`.`DisplayOrder`,
                            `raw`.`ModifiedBy`
                            FROM `' . $this->oldpre . 'CaseInfo` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`OpenedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `raw`.`LastEditedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `as` ON (`as`.`UserName` = `raw`.`AssignedTo`)
                            WHERE `raw`.`CaseID` IN (' . join(',', $arr) . ')';
                $caseResult = mysql_query($fetchSql, $this->con);
                $customFieldArr = array('CaseType', 'CaseMethod', 'CasePlan', 'ScriptStatus', 'ScriptedBy', 'ScriptedDate', 'ScriptLocation', 'MarkForDeletion', 'CaseKeyword', 'DisplayOrder');
                $insertSql = 'INSERT INTO `case_info` (`id`, `product_id`, `productmodule_id`, `title`, `case_step`, `case_status`,'
                           . '`related_case`, `related_bug`, `related_result`, `mail_to`, `created_by`, `created_at`, `updated_by`, '
                           . '`updated_at`, `assign_to`, `delete_flag`, `priority`, `modified_by`, `lock_version`) VALUES ';
                $valueArr = array();
                $sqlArr = array();
                $idArr = array();
                $productIdArr = array();
                while($caseResult && $rows = mysql_fetch_array($caseResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $judgeSql = 'SELECT * FROM `' . $this->newpre . 'product_module` WHERE `id` = ' . $rows['productmodule_id'];
                    $judgeResult = mysql_query($judgeSql);
                    if(!$judgeResult || !mysql_fetch_array($judgeResult))
                    {
                        $bugModuleSql = 'SELECT a.* FROM `' . $this->newpre . 'product_module` `a`, `'
                                       . $this->oldpre . 'TestModule` `b` WHERE `a`.`name` = `b`.`ModuleName` AND '
                                       . '`a`.`grade` = `b`.`ModuleGrade` AND `b`.`ModuleID` = ' . $rows['productmodule_id']
                                       . ' AND `a`.`product_id` = `b`.`ProjectID`';
                        $bugModuleResult = mysql_query($bugModuleSql);
                        $module = mysql_fetch_array($bugModuleResult);
                        if(null !== $module)
                        {
                            $rows['productmodule_id'] = $module['id'];
                        }
                        else
                        {
                            continue;
                        }
                    }
                    $sql = '`case_id` = "' . $rows['id'] . '"';
                    $values = '';
                    $comma = '';

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['ScriptedBy'] = $this->getRealNameByName($rows['ScriptedBy']);
                    $rows['CaseType'] = t('bugfree', $rows['CaseType']);
                    $rows['CaseMethod'] = t('bugfree', $rows['CaseMethod']);
                    $rows['CasePlan'] = t('bugfree', $rows['CasePlan']);
                    $rows['ScriptStatus'] = t('bugfree', $rows['ScriptStatus']);
                    $rows['case_status'] = t('bugfree', $rows['case_status']);

                    $rows['mail_to'] = $this->getRealnamesByMailTo($rows['mail_to']);
                    $rows['case_step'] = $this->bbcode2html($rows['case_step']);

                    if('Active' == $rows['AssignedTo'])
                    {
                        $rows['assign_to'] = -1;
                    }
                    unset($rows['AssignedTo']);

                    foreach($rows as $key => $val)
                    {
                        if(in_array($key, $customFieldArr))
                        {
                            if(is_null($val) || '' == $val)
                            {
                                $sql .= ',`' . $key .'` = NULL';
                            }
                            else if(('0000-00-00' == $val) || (empty($val) && 'ScriptedDate' == $key))
                            {
                                $sql .= ',`' . $key .'` = NULL';
                            }
                            else
                            {
                                $sql .= ',`' . $key .'` = "' . mysql_real_escape_string($val) . '"';
                            }
                            continue;
                        }

                        if('ModifiedBy' == $key)
                        {
                            continue;
                        }

                        if(is_null($val) || '' == $val)
                        {
                            $values .= $comma . 'NULL';
                        }
                        else
                        {
                            $val = trim($val, ',');
                            $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        }

                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ',"' . join(',', $this->getUserIdsByName($rows['ModifiedBy'])) . '", 1)';
                    $idArr[] = $rows['id'];
                    $sqlArr[] = $sql;
                    $productIdArr[] = $rows['product_id'];
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if($subResult)
                    {
                        foreach($sqlArr as $key => $sql)
                        {
                            list($subResult, $subInfos) = $this->insertCustemField($productIdArr[$key], 'case', $idArr[$key], $sql);
                            if(!$subResult)
                            {
                                $infos += $subInfos;
                            }
                        }
                    }
                    else
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'case_info successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade20()
    {
        // create table bug_action
        $createSql = 'DROP TABLE IF EXISTS `case_history`;
                      DROP TABLE IF EXISTS `case_action`;
                      CREATE TABLE `case_action` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `action_type` VARCHAR(255) NOT NULL ,
                          `action_note` TEXT NULL ,
                          `caseinfo_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_CASEACTION_CASEINFO1` (`caseinfo_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_CASEACTION_CASEINFO1`
                            FOREIGN KEY (`caseinfo_id` )
                            REFERENCES `' . $this->newpre . 'case_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ActionID`) as count FROM `' . $this->oldpre . 'TestAction` WHERE `ActionTarget` = "Case"';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT `raw`.`ActionID` as `id`,
                            `raw`.`IdValue` as `caseinfo_id`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`ActionDate` as `created_at`,
                            `raw`.`ActionNote` as `action_note`,
                            `raw`.`ActionType` as `action_type`
                            FROM `' . $this->oldpre . 'TestAction` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`ActionUser`)
                            RIGHT JOIN `' . $this->oldpre . 'CaseInfo` `ci` ON (`ci`.`CaseID` = `raw`.`IdValue`)
                            WHERE `ActionTarget` = "Case"
                            AND `raw`.`ActionID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `case_action` (`id`, `caseinfo_id`, `created_by`, `created_at`, `action_note`, `action_type`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);

                    $values = '';
                    $comma = '';
                    $rows['action_type'] = strtolower($rows['action_type']);
                    $rows['action_note'] = $this->bbcode2html($rows['action_note']);
                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }
                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'case_action successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade21()
    {
        // create table bug_history
        $createSql = 'DROP TABLE IF EXISTS `case_history`;
                      CREATE TABLE `case_history` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `action_field` VARCHAR(45) NOT NULL ,
                          `old_value` TEXT NULL ,
                          `new_value` TEXT NULL ,
                          `caseaction_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_CASEHISTORY_CASEACTION` (`caseaction_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_CASEHISTORY_CASEACTION`
                            FOREIGN KEY (`caseaction_id` )
                            REFERENCES `' . $this->newpre . 'case_action` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`raw`.`HistoryID`) as count FROM `' . $this->oldpre . 'TestHistory` `raw`
                         RIGHT JOIN `' . $this->newpre .'case_action` `action` ON (`action`.`id` = `raw`.`ActionID`)';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT
                            `raw`.`HistoryID` as `id`,
                            `raw`.`ActionID` as `caseaction_id`,
                            `raw`.`ActionField` as `action_field`,
                            `raw`.`OldValue` as `old_value`,
                            `raw`.`NewValue` as `new_value`
                            FROM `' . $this->oldpre . 'TestHistory` `raw`
                            RIGHT JOIN `' . $this->newpre .'case_action` `action` ON (`action`.`id` = `raw`.`ActionID`)
                            WHERE `raw`.`HistoryID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `case_history` (`id`, `caseaction_id`, `action_field`, `old_value`, `new_value`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $values = '';
                    $comma = '';

                    if(isset(self::$CASE_FIELD_TRANSLATE[$rows['action_field']]))
                    {
                        $rows['action_field'] = self::$CASE_FIELD_TRANSLATE[$rows['action_field']];
                    }

                    if('case_step' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->bbcode2html($rows['old_value']);
                        $rows['new_value'] = $this->bbcode2html($rows['new_value']);
                    }

                    if('assign_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealNameByName($rows['old_value']);
                        $rows['new_value'] = $this->getRealNameByName($rows['new_value']);
                    }

                    if('mail_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealnamesByMailTo($rows['old_value']);
                        $rows['new_value'] = $this->getRealnamesByMailTo($rows['new_value']);
                    }

                    if('CaseType' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('CaseMethod' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('CasePlan' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('ScriptStatus' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('case_status' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('MarkForDeletion' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'case_history successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade22()
    {
        // create table case_info
        $createSql = 'DROP TABLE IF EXISTS `result_info`;
                      CREATE TABLE `result_info` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `updated_at` DATETIME NOT NULL ,
                          `updated_by` INT NOT NULL ,
                          `result_status` VARCHAR(45) NOT NULL ,
                          `assign_to` INT NULL ,
                          `result_value` VARCHAR(45) NOT NULL ,
                          `mail_to` TEXT NULL ,
                          `result_step` TEXT NULL ,
                          `lock_version` SMALLINT NOT NULL ,
                          `related_bug` VARCHAR(255) NULL ,
                          `productmodule_id` INT NULL ,
                          `modified_by` TEXT NOT NULL ,
                          `title` VARCHAR(255) NOT NULL ,
                          `related_case_id` INT NOT NULL ,
                          `product_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'created_by` (`created_by` ASC) ,
                          INDEX `'.$this->newpre.'updated_by` (`updated_by` ASC) ,
                          INDEX `'.$this->newpre.'fk_result_info_case_info1` (`related_case_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_result_info_product1` (`product_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_result_info_case_info1`
                            FOREIGN KEY (`related_case_id` )
                            REFERENCES `' . $this->newpre . 'case_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_result_info_product1`
                            FOREIGN KEY (`product_id` )
                            REFERENCES `' . $this->newpre . 'product` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ResultID`) as count FROM `' . $this->oldpre . 'ResultInfo`';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestUser, the datas insert into test_user
                $fetchSql = 'SELECT `raw`.`ResultID` as `id`,
                            `raw`.`ProjectID` as `product_id`,
                            `raw`.`ModuleID` as `productmodule_id`,
                            `raw`.`ResultTitle` as `title`,
                            `raw`.`ResultValue` as `result_value`,
                            `raw`.`ResultStatus` as `result_status`,
                            `raw`.`ResultSteps` as `result_step`,
                            `raw`.`CaseID` as `related_case_id`,
                            `raw`.`BugID` as `related_bug`,
                            `raw`.`MailTo` as `mail_to`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`OpenedDate` as `created_at`,
                            `uu`.`UserID` as `updated_by`,
                            `raw`.`LastEditedDate` as `updated_at`,
                            `as`.`UserID` as `assign_to`,
                            `raw`.`ModifiedBy`,
                            `raw`.`ResultBuild` as `OpenedBuild`,
                            `raw`.`ResultOS` as `BugOS`,
                            `raw`.`ResultBrowser` as `BugBrowser`,
                            `raw`.`ResultMachine` as `BugMachine`,
                            `raw`.`ResultKeyword`
                            FROM `' . $this->oldpre . 'ResultInfo` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`OpenedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `uu` ON (`uu`.`UserName` = `raw`.`LastEditedBy`)
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `as` ON (`as`.`UserName` = `raw`.`AssignedTo`)
                            RIGHT JOIN `' . $this->oldpre . 'CaseInfo` `ci` ON (`ci`.`CaseID` = `raw`.`CaseID`)
                            WHERE `raw`.`ResultID` IN (' . join(',', $arr) . ')';
                $resultResult = mysql_query($fetchSql, $this->con);
                $customFieldArr = array('OpenedBuild', 'BugOS', 'BugBrowser', 'BugMachine', 'ResultKeyword');
                $insertSql = 'INSERT INTO `result_info` (`id`, `product_id`, `productmodule_id`, `title`, `result_value`, `result_status`,'
                           . '`result_step`, `related_case_id`, `related_bug`, `mail_to`, `created_by`, `created_at`, `updated_by`, '
                           . '`updated_at`, `assign_to`, `modified_by`, `lock_version`) VALUES ';
                $valueArr = array();
                $sqlArr = array();
                $idArr = array();
                $productIdArr = array();
                while($resultResult && $rows = mysql_fetch_array($resultResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);

                    $judgeSql = 'SELECT * FROM `' . $this->newpre . 'product_module` WHERE `id` = ' . $rows['productmodule_id'];
                    $judgeResult = mysql_query($judgeSql);
                    if(!$judgeResult || !mysql_fetch_array($judgeResult))
                    {
                        $bugModuleSql = 'SELECT a.* FROM `' . $this->newpre . 'product_module` `a`, `'
                                       . $this->oldpre . 'TestModule` `b` WHERE `a`.`name` = `b`.`ModuleName` AND '
                                       . '`a`.`grade` = `b`.`ModuleGrade` AND `b`.`ModuleID` = ' . $rows['productmodule_id']
                                       . ' AND `a`.`product_id` = `b`.`ProjectID`';
                        $bugModuleResult = mysql_query($bugModuleSql);
                        $module = mysql_fetch_array($bugModuleResult);
                        if(null !== $module)
                        {
                            $rows['productmodule_id'] = $module['id'];
                        }
                        else
                        {
                            $rows['productmodule_id'] = null;
                        }
                    }
                    $sql = '`result_id` = "' . $rows['id'] . '"';

                    $values = '';
                    $comma = '';

                    if('0000-00-00 00:00:00' == $rows['created_at']
                            || '0000-00-00 00:00:00' == $rows['updated_at']
                            || empty($rows['created_at'])
                            || empty($rows['updated_at']))
                    {
                        if('0000-00-00 00:00:00' != $rows['created_at'] && !empty($rows['created_at']))
                        {
                            $rows['updated_at'] = $rows['created_at'];
                        }
                        else if('0000-00-00 00:00:00' != $rows['updated_at']  && !empty($rows['updated_at']))
                        {
                            $rows['created_at'] = $rows['updated_at'];
                        }
                        else
                        {
                            $rows['created_at'] = $rows['updated_at'] = date('Y-m-d G:i:s');
                        }
                    }

                    $rows['BugBrowser'] = t('bugfree', $rows['BugBrowser']);
                    $rows['BugOS'] = t('bugfree', $rows['BugOS']);
                    $rows['result_value'] = t('bugfree', $rows['result_value']);
                    $rows['result_status'] = t('bugfree', $rows['result_status']);

                    $rows['mail_to'] = $this->getRealnamesByMailTo($rows['mail_to']);
                    $rows['result_step'] = $this->bbcode2html($rows['result_step']);

                    foreach($rows as $key => $val)
                    {
                        if(in_array($key, $customFieldArr))
                        {
                            if(is_null($val) || '' == $val)
                            {
                                $sql .= ',`' . $key .'` = NULL';
                            }
                            else
                            {
                                $sql .= ',`' . $key .'` = "' . mysql_real_escape_string($val) . '"';
                            }
                            continue;
                        }

                        if('ModifiedBy' == $key)
                        {
                            continue;
                        }

                        if(is_null($val) || '' == $val)
                        {
                            $val = trim($val, ',');
                            $values .= $comma . 'NULL';
                        }
                        else
                        {
                            $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        }
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ',"' . join(',', $this->getUserIdsByName($rows['ModifiedBy'])) . '", 1)';
                    $idArr[] = $rows['id'];
                    $sqlArr[] = $sql;
                    $productIdArr[] = $rows['product_id'];
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if($subResult)
                    {
                        foreach($sqlArr as $key => $sql)
                        {
                            list($subResult, $subInfos) = $this->insertCustemField($productIdArr[$key], 'result', $idArr[$key], $sql);
                            if(!$subResult)
                            {
                                $infos += $subInfos;
                            }
                        }
                    }
                    else
                    {

                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'result_info successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade23()
    {
        // create table bug_action
        $createSql = 'DROP TABLE IF EXISTS `result_history`;
                      DROP TABLE IF EXISTS `result_action`;
                      CREATE TABLE `result_action` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `created_at` DATETIME NOT NULL ,
                          `created_by` INT NOT NULL ,
                          `action_type` VARCHAR(255) NOT NULL ,
                          `action_note` TEXT NULL ,
                          `resultinfo_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_RESULTACTION_RESULTINFO1` (`resultinfo_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_RESULTACTION_RESULTINFO1`
                            FOREIGN KEY (`resultinfo_id` )
                            REFERENCES `' . $this->newpre . 'result_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`ActionID`) as count FROM `' . $this->oldpre . 'TestAction` WHERE `ActionTarget` = "Result"';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT `raw`.`ActionID` as `id`,
                            `raw`.`IdValue` as `resultinfo_id`,
                            `cu`.`UserID` as `created_by`,
                            `raw`.`ActionDate` as `created_at`,
                            `raw`.`ActionNote` as `action_note`,
                            `raw`.`ActionType` as `action_type`
                            FROM `' . $this->oldpre . 'TestAction` `raw`
                            LEFT JOIN `' . $this->oldpre . 'TestUser` `cu` ON (`cu`.`UserName` = `raw`.`ActionUser`)
                            RIGHT JOIN `' . $this->oldpre . 'ResultInfo` `ri` ON (`ri`.`ResultID` = `raw`.`IdValue`)
                            WHERE `ActionTarget` = "Result"
                            AND `raw`.`ActionID` IN (' . join(',', $arr) . ')';
                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `result_action` (`id`, `resultinfo_id`, `created_by`, `created_at`, `action_note`, `action_type`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);
                    $values = '';
                    $comma = '';
                    $rows['action_type'] = strtolower($rows['action_type']);
                    $rows['action_note'] = $this->bbcode2html($rows['action_note']);
                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }
                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'result_action successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade24()
    {
        // create table bug_history
        $createSql = 'DROP TABLE IF EXISTS `result_history`;
                      CREATE TABLE `result_history` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `action_field` VARCHAR(45) NOT NULL ,
                          `old_value` TEXT NULL ,
                          `new_value` TEXT NULL ,
                          `resultaction_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_RESULTHISTORY_RESULTACTION1` (`resultaction_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_RESULTHISTORY_RESULTACTION1`
                            FOREIGN KEY (`resultaction_id` )
                            REFERENCES `' . $this->newpre . 'result_action` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $countSql = 'SELECT max(`raw`.`HistoryID`) as count FROM `' . $this->oldpre . 'TestHistory`  `raw`
                         RIGHT JOIN `' . $this->newpre .'result_action` `action` ON (`action`.`id` = `raw`.`ActionID`)';
            $countResult = mysql_query($countSql, $this->con);
            $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
            $count = $countArr['count'];
            $start = 0;
            while($start < $count)
            {
                $arr = array();
                for($i = 0; $i < self::PAGE_SIZE; $i++)
                {
                    $arr[] = ++$start;
                }
                // fetch datas from TestAction, the datas insert into bug_action
                $fetchSql = 'SELECT
                            `raw`.`HistoryID` as `id`,
                            `raw`.`ActionID` as `resultaction_id`,
                            `raw`.`ActionField` as `action_field`,
                            `raw`.`OldValue` as `old_value`,
                            `raw`.`NewValue` as `new_value`
                            FROM `' . $this->oldpre . 'TestHistory` `raw`
                            RIGHT JOIN `' . $this->newpre .'result_action` `action` ON (`action`.`id` = `raw`.`ActionID`)
                            WHERE `raw`.`HistoryID` IN (' . join(',', $arr) . ')';

                $actionResult = mysql_query($fetchSql, $this->con);
                $insertSql = 'INSERT INTO `result_history` (`id`, `resultaction_id`, `action_field`, `old_value`, `new_value`) VALUES ';
                $valueArr = array();
                while($actionResult && $rows = mysql_fetch_array($actionResult, MYSQL_ASSOC))
                {
                    $rows = $this->htmldecode($rows);

                    $values = '';
                    $comma = '';

                    if(isset(self::$RESULT_FIELD_TRANSLATE[$rows['action_field']]))
                    {
                        $rows['action_field'] = self::$RESULT_FIELD_TRANSLATE[$rows['action_field']];
                    }

                    if('result_step' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->bbcode2html($rows['old_value']);
                        $rows['new_value'] = $this->bbcode2html($rows['new_value']);
                    }

                    if('assign_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealNameByName($rows['old_value']);
                        $rows['new_value'] = $this->getRealNameByName($rows['new_value']);
                    }

                    if('mail_to' == $rows['action_field'])
                    {
                        $rows['old_value'] = $this->getRealnamesByMailTo($rows['old_value']);
                        $rows['new_value'] = $this->getRealnamesByMailTo($rows['new_value']);
                    }

                    if('BugBrowser' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('BugOS' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('result_value' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    if('result_status' == $rows['action_field'])
                    {
                        $rows['old_value'] = t('bugfree', $rows['old_value']);
                        $rows['new_value'] = t('bugfree', $rows['new_value']);
                    }

                    foreach($rows as $val)
                    {
                        $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                        $comma = ',';
                    }
                    $valueArr[] = '(' . $values . ')';
                }

                if(!empty($valueArr))
                {
                    $insertSql .= join(',', $valueArr);
                    list($subResult, $subInfos) = $this->executeDataSQL($insertSql, $this->newpre);
                    if(!$subResult)
                    {
                        $result = $subResult;
                        $infos += $subInfos;
                    }
                }
            }
        }
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'result_history successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    function upgrade25()
    {
        // create table bug_history
        $createSql = 'DROP TABLE IF EXISTS `map_user_bug`;
                      CREATE TABLE `map_user_bug` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `test_user_id` INT NOT NULL ,
                          `info_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_user_case_test_user1` (`test_user_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_user_bug_bug_info1` (`info_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_case_test_user10`
                            FOREIGN KEY (`test_user_id` )
                            REFERENCES `' . $this->newpre . 'test_user` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_bug_bug_info1`
                            FOREIGN KEY (`info_id` )
                            REFERENCES `' . $this->newpre . 'bug_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;
                      DROP TABLE IF EXISTS `map_user_case`;
                      CREATE TABLE `map_user_case` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `test_user_id` INT NOT NULL ,
                          `info_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_user_case_test_user1` (`test_user_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_user_case_case_info1` (`info_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_case_test_user1`
                            FOREIGN KEY (`test_user_id` )
                            REFERENCES `' . $this->newpre . 'test_user` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_case_case_info1`
                            FOREIGN KEY (`info_id` )
                            REFERENCES `' . $this->newpre . 'case_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;
                      DROP TABLE IF EXISTS `map_user_result`;
                      CREATE TABLE `map_user_result` (
                          `id` INT NOT NULL AUTO_INCREMENT ,
                          `test_user_id` INT NOT NULL ,
                          `info_id` INT NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `'.$this->newpre.'fk_map_user_case_test_user1` (`test_user_id` ASC) ,
                          INDEX `'.$this->newpre.'fk_map_user_result_result_info1` (`info_id` ASC) ,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_case_test_user100`
                            FOREIGN KEY (`test_user_id` )
                            REFERENCES `' . $this->newpre . 'test_user` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                          CONSTRAINT `'.$this->newpre.'fk_map_user_result_result_info1`
                            FOREIGN KEY (`info_id` )
                            REFERENCES `' . $this->newpre . 'result_info` (`id` )
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8
                        COLLATE = utf8_general_ci;
                        DROP VIEW IF EXISTS `' . $this->newpre . 'bugview`;
                        DROP TABLE IF EXISTS `bugview`;
                        CREATE OR REPLACE VIEW `' . $this->newpre . 'bugview` AS SElECT
                            `' . $this->newpre . 'bug_info`.*,
                            `' . $this->newpre . 'product`.`name` AS `product_name`,
                            CONCAT_WS("/", `' . $this->newpre . 'product`.`name`, `' . $this->newpre . 'product_module`.`full_path_name`) AS `module_name`,
                            `uc`.`realname` AS `created_by_name`,
                            `uu`.`realname` AS `updated_by_name`,
                            `ur`.`realname` AS `resolved_by_name`,
                            `uclo`.`realname` AS `closed_by_name`,
                            `ua`.`realname` AS `assign_to_name`
                        FROM
                        `' . $this->newpre . 'bug_info`
                        LEFT JOIN `' . $this->newpre . 'test_user` `uc` ON (`' . $this->newpre . 'bug_info`.`created_by` = `uc`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `uu` ON (`' . $this->newpre . 'bug_info`.`updated_by` = `uu`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `ur` ON (`' . $this->newpre . 'bug_info`.`resolved_by` = `ur`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `uclo` ON (`' . $this->newpre . 'bug_info`.`closed_by` = `uclo`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `ua` ON (`' . $this->newpre . 'bug_info`.`assign_to` = `ua`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product` ON (`' . $this->newpre . 'bug_info`.`product_id` = `' . $this->newpre . 'product`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product_module` ON (`' . $this->newpre . 'bug_info`.`productmodule_id` = `' . $this->newpre . 'product_module`.`id`);
                        DROP VIEW IF EXISTS `' . $this->newpre . 'caseview` ;
                        DROP TABLE IF EXISTS `caseview`;
                        CREATE  OR REPLACE VIEW `' . $this->newpre . 'caseview` AS SElECT
                            `' . $this->newpre . 'case_info`.*,
                            `' . $this->newpre . 'product`.`name` AS `product_name`,
                            CONCAT_WS("/", `' . $this->newpre . 'product`.`name`, `' . $this->newpre . 'product_module`.`full_path_name`) AS `module_name`,
                            `uc`.`realname` AS `created_by_name`,
                            `uu`.`realname` AS `updated_by_name`,
                            `ua`.`realname` AS `assign_to_name`
                        FROM
                        `' . $this->newpre . 'case_info`
                        LEFT JOIN `' . $this->newpre . 'test_user` `uc` ON (`' . $this->newpre . 'case_info`.`created_by` = `uc`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `uu` ON (`' . $this->newpre . 'case_info`.`updated_by` = `uu`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `ua` ON (`' . $this->newpre . 'case_info`.`assign_to` = `ua`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product` ON (`' . $this->newpre . 'case_info`.`product_id` = `' . $this->newpre . 'product`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product_module` ON (`' . $this->newpre . 'case_info`.`productmodule_id` = `' . $this->newpre . 'product_module`.`id`);
                        DROP VIEW IF EXISTS `' . $this->newpre . 'resultview` ;
                        DROP TABLE IF EXISTS `' . $this->newpre . 'resultview`;
                        CREATE OR REPLACE VIEW `' . $this->newpre . 'resultview` AS SElECT
                            `' . $this->newpre . 'result_info`.*,
                            `' . $this->newpre . 'product`.`name` AS `product_name`,
                            CONCAT_WS("/", `' . $this->newpre . 'product`.`name`, `' . $this->newpre . 'product_module`.`full_path_name`) AS `module_name`,
                            `uc`.`realname` AS `created_by_name`,
                            `uu`.`realname` AS `updated_by_name`,
                            `ua`.`realname` AS `assign_to_name`
                        FROM
                        `' . $this->newpre . 'result_info`
                        LEFT JOIN `' . $this->newpre . 'test_user` `uc` ON (`' . $this->newpre . 'result_info`.`created_by` = `uc`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `uu` ON (`' . $this->newpre . 'result_info`.`updated_by` = `uu`.`id`)
                        LEFT JOIN `' . $this->newpre . 'test_user` `ua` ON (`' . $this->newpre . 'result_info`.`assign_to` = `ua`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product` ON (`' . $this->newpre . 'result_info`.`product_id` = `' . $this->newpre . 'product`.`id`)
                        LEFT JOIN `' . $this->newpre . 'product_module` ON (`' . $this->newpre . 'result_info`.`productmodule_id` = `' . $this->newpre . 'product_module`.`id`);';
        list($result, $infos) = $this->executeSQL($createSql, $this->newpre);
        if($result)
        {
            $result = 0;
            $info = 'Upgraded table ' . $this->newpre . 'map_user_bug successfully.<br/>Upgraded table ' . $this->newpre
                  . 'map_user_case successfully.<br/>Upgraded table ' . $this->newpre . 'map_user_result successfully.';
        }
        else
        {
            $result = 1;
            $info = implode("\n", $infos);
        }
        return array($result, $info);
    }

    private function createBugCustomField($projectId)
    {
        $sql = 'INSERT INTO `field_config` (`lock_version`, `created_at`, `created_by`, `updated_at`, `updated_by`,
                `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`,
                `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`,
                `result_group`, `is_required`) VALUES (1, NOW(), 0, NOW(), 0, "BugType", "single select", "' . t('bugfree', 'CodeError') . ',' . t('bugfree', 'Interface') . ',' . t('bugfree', 'DesignChange') . ',' . t('bugfree', 'NewFeature') . ',' . t('bugfree', 'SpecDefect') . ',' . t('bugfree', 'DesignDefect') . ',' . t('bugfree', 'Config') . ',' . t('bugfree', 'Install') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Standard') . ',' . t('bugfree', 'Automation') . ',' . t('bugfree', 'TrackThings') . ',' . t('bugfree', 'BadCase') . ',' . t('bugfree', 'Others') . '", "", "0", "' . t('bugfree', 'Bug type') . '", "bug", "bug_status", 8,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "HowFound", "single select", "' . t('bugfree', 'FuncTest') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Integrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'SmokeTest') . ',' . t('bugfree', 'Acceptance') . ',' . t('bugfree', 'BugBash') . ',' . t('bugfree', 'AdHoc') . ',' . t('bugfree', 'Regression') . ',' . t('bugfree', 'SpecReview') . ',' . t('bugfree', 'DesignReview') . ',' . t('bugfree', 'CodeReview') . ',' . t('bugfree', 'PostRTW') . ',' . t('bugfree', 'Customer') . ',' . t('bugfree', 'Partner') . ',' . t('bugfree', 'Other') .'", "", "0", "' . t('bugfree', 'Bug how found') . '", "bug", "bug_status", 7,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "BugOS", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'Win7') . ',' . t('bugfree', 'WinVista') . ',' . t('bugfree', 'WinXP') . ',' . t('bugfree', 'Win2000') . ',' . t('bugfree', 'Linux') . ',' . t('bugfree', 'FreeBSD') . ',' . t('bugfree', 'Unix') . ',' . t('bugfree', 'MacOS') . ',' . t('bugfree', 'Others') .'", "", "0", "' . t('bugfree', 'OS') . '", "bug", "bug_status", 6,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "0"),
                (1, NOW(), 0, NOW(), 0, "BugBrowser", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'IE8') . ',' . t('bugfree', 'IE7') . ',' . t('bugfree', 'IE6') . ',' . t('bugfree', 'FireFox4.0') . ',' . t('bugfree', 'FireFox3.0') . ',' . t('bugfree', 'FireFox2.0') . ',' . t('bugfree', 'Chrome') . ',' . t('bugfree', 'Safari') . ',' . t('bugfree', 'Opera') . ',' . t('bugfree', 'Others') .'", "", "0", "' . t('bugfree', 'Browser') . '", "bug", "bug_status", 5,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "0"),
                (1, NOW(), 0, NOW(), 0, "OpenedBuild", "text", "", "", "0", "' . t('bugfree', 'Open build') . '", "bug", "bug_open", 5,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "1"),
                (1, NOW(), 0, NOW(), 0, "ResolvedBuild", "text", "", "", "0", "' . t('bugfree', 'Resolve build') . '", "bug", "bug_resolve", 5,
                "resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "BugSubStatus", "single select", "' . join(',', self::$BUG_SUB_STATUS) . '", "", "0", "' . t('bugfree', 'Bug sub status') . '", "bug", "bug_other", 10,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "BugMachine", "text", "", "", "0", "' . t('bugfree', 'Machine') . '", "bug", "bug_other", 9,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "BugKeyword", "text", "", "", "0", "' . t('bugfree', 'Keyword') . '", "bug", "bug_other", 8,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0");';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $sql = 'DROP TABLE IF EXISTS `ettonbug_' . $projectId . '`;
                    CREATE TABLE `ettonbug_' . $projectId . '`(
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `bug_id` INT(11) NOT NULL,
                      `BugType` VARCHAR(255) DEFAULT NULL,
                      `HowFound` VARCHAR(255) DEFAULT NULL,
                      `BugBrowser` VARCHAR(255) DEFAULT NULL,
                      `BugOS` VARCHAR(255) DEFAULT NULL,
                      `OpenedBuild` VARCHAR(255) DEFAULT NULL,
                      `ResolvedBuild` VARCHAR(255) DEFAULT NULL,
                      `BugSubStatus` VARCHAR(255) DEFAULT NULL,
                      `BugMachine` VARCHAR(255) DEFAULT NULL,
                      `BugKeyword` VARCHAR(255) DEFAULT NULL,
                      INDEX `'.$this->newpre.'idx_bug_id` (`bug_id`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            list($result, $infos) = $this->executeSQL($sql, $this->newpre);
        }
        return array($result, $infos);
    }

    private function createCaseCustomField($projectId)
    {
        $sql = 'INSERT INTO `field_config` (`lock_version`, `created_at`, `created_by`, `updated_at`, `updated_by`,
                `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`,
                `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`,
                `result_group`, `is_required`) VALUES (1, NOW(), 0, NOW(), 0, "CaseType", "single select", "' . t('bugfree', 'Functional') . ',' . t('bugfree', 'Configuration') . ',' . t('bugfree', 'Setup') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Other'). '", "", "0", "' . t('bugfree', 'Case type') . '", "case", "case_status", 8,
                "", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "CaseMethod", "single select", "' .  t('bugfree', 'Manual') . ',' . t('bugfree', 'Automation') .'", "", "0", "' . t('bugfree', 'Case method') . '", "case", "case_status", 7,
                "", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "CasePlan", "single select", "' .  t('bugfree', 'Function') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Intergrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'Smoke') . ',' . t('bugfree', 'Acceptance') .'", "", "0", "' . t('bugfree', 'Case plan') . '", "case", "case_status", 6,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptStatus", "single select", "' . t('bugfree', 'NotPlanned') . ',' . t('bugfree', 'Planning') . ',' . t('bugfree', 'Blocked') . ',' . t('bugfree', 'Coding') . ',' . t('bugfree', 'CodingDone') . ',' . t('bugfree', 'Reviewed') .'", "", "0", "' . t('bugfree', 'Script status') . '", "case", "case_script", 5,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptedBy", "single user", "", "", "0", "' . t('bugfree', 'Scripted by') . '", "case", "case_script", 4,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptedDate", "date", "", "", "0", "' . t('bugfree', 'Scripted Date') . '", "case", "case_script", 3,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptLocation", "text", "", "", "0", "' . t('bugfree', 'Script location') . '", "case", "case_script", 2,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "CaseKeyword", "text", "", "", "0", "' . t('bugfree', 'Keyword') . '", "case", "case_other", 8,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "DisplayOrder", "text", "", "0", "0", "' . t('bugfree', 'Display order') . '", "case", "case_other", 7,
                "", "no", "", ' . $projectId . ', "0", "", "0");';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $sql = 'DROP TABLE IF EXISTS `ettoncase_' . $projectId . '`;
                    CREATE TABLE `ettoncase_' . $projectId . '`(
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `case_id` INT(11) NOT NULL,
                      `CaseType` VARCHAR(255) DEFAULT NULL,
                      `CaseMethod` VARCHAR(255) DEFAULT NULL,
                      `CasePlan` VARCHAR(255) DEFAULT NULL,
                      `ScriptStatus` VARCHAR(255) DEFAULT NULL,
                      `ScriptedBy` VARCHAR(255) DEFAULT NULL,
                      `ScriptedDate` DATE DEFAULT NULL,
                      `ScriptLocation` VARCHAR(255) DEFAULT NULL,
                      `CaseKeyword` VARCHAR(255) DEFAULT NULL,
                      `DisplayOrder` VARCHAR(255) DEFAULT NULL,
                      INDEX `'.$this->newpre.'idx_case_id` (`case_id`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            list($result, $infos) = $this->executeSQL($sql, $this->newpre);
        }
        return array($result, $infos);
    }

    private function createResultCustomField($projectId)
    {
        $sql = 'INSERT INTO `field_config` (`lock_version`, `created_at`, `created_by`, `updated_at`, `updated_by`,
                `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`,
                `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`,
                `result_group`, `is_required`) VALUES (1, NOW(), 1, NOW(), 1, "OpenedBuild", "text", "", "", "0", "' . t('bugfree', 'Run build') . '", "result", "result_environment", 9,
                "", "no", "", ' . $projectId . ', "1", "bug_open", "1"),
                (1, NOW(), 0, NOW(), 0, "BugOS", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'Win7') . ',' . t('bugfree', 'WinVista') . ',' . t('bugfree', 'WinXP') . ',' . t('bugfree', 'Win2000') . ',' . t('bugfree', 'Linux') . ',' . t('bugfree', 'FreeBSD') . ',' . t('bugfree', 'Unix') . ',' . t('bugfree', 'MacOS') . ',' . t('bugfree', 'Others'). '", "", "0", "' . t('bugfree', 'OS') . '", "result", "result_environment", 8,
                "", "no", "", ' . $projectId . ', "1", "bug_status", "0"),
                (1, NOW(), 0, NOW(), 0, "BugBrowser", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'IE8') . ',' . t('bugfree', 'IE7') . ',' . t('bugfree', 'IE6') . ',' . t('bugfree', 'FireFox4.0') . ',' . t('bugfree', 'FireFox3.0') . ',' . t('bugfree', 'FireFox2.0') . ',' . t('bugfree', 'Chrome') . ',' . t('bugfree', 'Safari') . ',' . t('bugfree', 'Opera') . ',' . t('bugfree', 'Others') .'", "", "0", "' . t('bugfree', 'Browser') . '", "result", "result_environment", 7,
                "", "no", "", ' . $projectId . ', "1", "bug_status", "0"),
                (1, NOW(), 0, NOW(), 0, "BugMachine", "text", "", "", "0", "' . t('bugfree', 'Machine') . '", "result", "result_other", 6,
                "", "no", "", ' . $projectId . ', "1", "bug_other", "0"),
                (1, NOW(), 0, NOW(), 0, "ResultKeyword", "text", "", "", "0", "' . t('bugfree', 'Keyword') . '", "result", "result_other", 5,
                "", "no", "", ' . $projectId . ', "0", "", "0");';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $sql = 'DROP TABLE IF EXISTS `ettonresult_' . $projectId . '`;
                    CREATE TABLE `ettonresult_' . $projectId . '`(
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `result_id` INT(11) NOT NULL,
                      `OpenedBuild` VARCHAR(255) DEFAULT NULL,
                      `BugOS` VARCHAR(255) DEFAULT NULL,
                      `BugBrowser` VARCHAR(255) DEFAULT NULL,
                      `BugMachine` VARCHAR(255) DEFAULT NULL,
                      `ResultKeyword` VARCHAR(255) DEFAULT NULL,
                      INDEX `'.$this->newpre.'idx_result_id` (`result_id`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            list($result, $infos) = $this->executeSQL($sql, $this->newpre);
        }
        return array($result, $infos);
    }

    private function updateBugCustomField($projectId, $fields)
    {
        $sql = 'INSERT INTO `field_config` (`lock_version`, `created_at`, `created_by`, `updated_at`, `updated_by`,
                `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`,
                `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`,
                `result_group`, `is_required`) VALUES ';
        $createCutomFieldSql = '';
        foreach($fields as $field)
        {
            $sql .= '(0, NOW(), 1, NOW(), 1, "' . $field['name'] . '", "' . $this->fieldTypeTranslate($field['type']). '",
                      "' . trim($field['value'], ',') . '", "", "' . $this->fieldStatusTranslate($field['status']) . '",
                      "' . $field['text'] . '", "bug", "bug_other", "0", "opened,resolved,closed", "no", "", "' . $projectId . '", "0", "", "' . $this->fieldOptionTranslate($field['option']) . '"),';
            if('date' == $field['type'])
            {
                $createCutomFieldSql .= '`' . $field['name'] . '` DATE DEFAULT NULL,';
            }
            else
            {
                $createCutomFieldSql .= '`' . $field['name'] . '` VARCHAR(255) DEFAULT NULL,';
            }
        }
        $sql .= '(1, NOW(), 0, NOW(), 0, "BugType", "single select", "' . t('bugfree', 'CodeError') . ',' . t('bugfree', 'Interface') . ',' . t('bugfree', 'DesignChange') . ',' . t('bugfree', 'NewFeature') . ',' . t('bugfree', 'SpecDefect') . ',' . t('bugfree', 'DesignDefect') . ',' . t('bugfree', 'Config') . ',' . t('bugfree', 'Install') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Standard') . ',' . t('bugfree', 'Automation') . ',' . t('bugfree', 'TrackThings') . ',' . t('bugfree', 'BadCase') . ',' . t('bugfree', 'Others'). '", "", "0", "' . t('bugfree', 'Bug type') . '", "bug", "bug_status", 8,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "HowFound", "single select", "' . t('bugfree', 'FuncTest') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Integrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'SmokeTest') . ',' . t('bugfree', 'Acceptance') . ',' . t('bugfree', 'BugBash') . ',' . t('bugfree', 'AdHoc') . ',' . t('bugfree', 'Regression') . ',' . t('bugfree', 'SpecReview') . ',' . t('bugfree', 'DesignReview') . ',' . t('bugfree', 'CodeReview') . ',' . t('bugfree', 'PostRTW') . ',' . t('bugfree', 'Customer') . ',' . t('bugfree', 'Partner') . ',' . t('bugfree', 'Other') .'", "", "0", "' . t('bugfree', 'Bug how found') . '", "bug", "bug_status", 7,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "BugOS", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'Win7') . ',' . t('bugfree', 'WinVista') . ',' . t('bugfree', 'WinXP') . ',' . t('bugfree', 'Win2000') . ',' . t('bugfree', 'Linux') . ',' . t('bugfree', 'FreeBSD') . ',' . t('bugfree', 'Unix') . ',' . t('bugfree', 'MacOS') . ',' . t('bugfree', 'Others') .'", "", "0", "' . t('bugfree', 'OS') . '", "bug", "bug_status", 6,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "0"),
                (1, NOW(), 0, NOW(), 0, "BugBrowser", "single select", "' . t('bugfree', 'All') . ',' . t('bugfree', 'IE8') . ',' . t('bugfree', 'IE7') . ',' . t('bugfree', 'IE6') . ',' . t('bugfree', 'FireFox4.0') . ',' . t('bugfree', 'FireFox3.0') . ',' . t('bugfree', 'FireFox2.0') . ',' . t('bugfree', 'Chrome') . ',' . t('bugfree', 'Safari') . ',' . t('bugfree', 'Opera') . ',' . t('bugfree', 'Others') .'", "", "0", "' . t('bugfree', 'Browser') . '", "bug", "bug_status", 5,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "0"),
                (1, NOW(), 0, NOW(), 0, "OpenedBuild", "text", "", "", "0", "' . t('bugfree', 'Open build') . '", "bug", "bug_open", 5,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "1", "result_environment", "1"),
                (1, NOW(), 0, NOW(), 0, "ResolvedBuild", "text", "", "", "0", "' . t('bugfree', 'Resolve build') . '", "bug", "bug_resolve", 5,
                "resolved,closed", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "BugSubStatus", "single select", "' . join(',', self::$BUG_SUB_STATUS) . '", "", "0", "' . t('bugfree', 'Bug sub status') . '", "bug", "bug_other", 10,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "BugMachine", "text", "", "", "0", "' . t('bugfree', 'Machine') . '", "bug", "bug_other", 9,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "BugKeyword", "text", "", "", "0", "' . t('bugfree', 'Keyword') . '", "bug", "bug_other", 8,
                "opened,resolved,closed", "no", "", ' . $projectId . ', "0", "", "0");';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $sql = 'DROP TABLE IF EXISTS `ettonbug_' . $projectId . '`;
                    CREATE TABLE `ettonbug_' . $projectId . '`(
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `bug_id` INT(11) NOT NULL,
                      `BugType` VARCHAR(255) DEFAULT NULL,
                      `HowFound` VARCHAR(255) DEFAULT NULL,
                      `BugBrowser` VARCHAR(255) DEFAULT NULL,
                      `BugOS` VARCHAR(255) DEFAULT NULL,
                      `OpenedBuild` VARCHAR(255) DEFAULT NULL,
                      `ResolvedBuild` VARCHAR(255) DEFAULT NULL,
                      `BugSubStatus` VARCHAR(255) DEFAULT NULL,
                      `BugMachine` VARCHAR(255) DEFAULT NULL,
                      `BugKeyword` VARCHAR(255) DEFAULT NULL,
                      ' . $createCutomFieldSql . '
                      INDEX `'.$this->newpre.'idx_bug_id` (`bug_id`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            list($result, $infos) = $this->executeSQL($sql, $this->newpre);
            if($result)
            {
                $countSql = 'SELECT max(`FieldID`) as count FROM `' . $this->oldpre . 'addonbug_' . $projectId. '`';
                $countResult = mysql_query($countSql, $this->con);
                $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
                $count = $countArr['count'];
                $start = 0;
                while($start < $count)
                {
                    $arr = array();
                    for($i = 0; $i < self::PAGE_SIZE; $i++)
                    {
                        $arr[] = ++$start;
                    }
                    $fetchCustomFieldSql = 'SELECT * FROM `' . $this->oldpre . 'addonbug_' . $projectId. '` WHERE `FieldID` IN (' . join(',', $arr) . ')';
                    $customFieldResult = mysql_query($fetchCustomFieldSql, $this->con);
                    $sql = "INSERT INTO `ettonbug_$projectId` ";
                    $field = '';
                    $value = array();
                    while($customFieldRow = mysql_fetch_array($customFieldResult, MYSQL_ASSOC))
                    {
                        $fields = '';
                        $values = '';
                        $comma = '';
                        foreach($customFieldRow as $key => $val)
                        {
                            if('FieldID' == $key)
                            {
                                $key = 'bug_id';
                            }
                            $fields .= $comma . '`' . $key . '`';
                            if(empty($val))
                            {
                                $values .= $comma . ' NULL';
                            }
                            else
                            {
                                $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                            }
                            $comma = ',';
                        }
                        $field = $fields;
                        $value[] = '(' . $values . ')';
                    }
                    if(!empty($field))
                    {
                        $sql .= "($field) VALUES " . join(',', $value);
                        list($subResult, $subInfos) = $this->executeDataSQL($sql, $this->newpre);
                        if(!$subResult)
                        {
                            $result = $subResult;
                            $infos += $subInfos;
                        }
                    }
                }
            }
        }
        return array($result, $infos);
    }

    private function updateCaseCustomField($projectId, $fields)
    {
        $sql = 'INSERT INTO `field_config` (`lock_version`, `created_at`, `created_by`, `updated_at`, `updated_by`,
                `field_name`, `field_type`, `field_value`, `default_value`, `is_dropped`, `field_label`, `type`, `belong_group`,
                `display_order`, `editable_action`, `validate_rule`, `match_expression`, `product_id`, `edit_in_result`,
                `result_group`, `is_required`) VALUES ';
        $createCutomFieldSql = '';
        foreach($fields as $field)
        {
            $sql .= '(1, NOW(), 0, NOW(), 0, "' . $field['name'] . '", "' . $this->fieldTypeTranslate($field['type']). '",
                      "' . trim($field['value'], ',') . '", "", "' . $this->fieldStatusTranslate($field['status']) . '",
                      "' . $field['text'] . '", "case", "case_other", "0", "", "no", "", "' . $projectId . '", "0", "", "' . $this->fieldOptionTranslate($field['option']) . '"),';
            if('date' == $field['type'])
            {
                $createCutomFieldSql .= '`' . $field['name'] . '` DATE DEFAULT NULL,';
            }
            else
            {
                $createCutomFieldSql .= '`' . $field['name'] . '` VARCHAR(255) DEFAULT NULL,';
            }
        }
        $sql .= '(1, NOW(), 0, NOW(), 0, "CaseType", "single select", "' . t('bugfree', 'Functional') . ',' . t('bugfree', 'Configuration') . ',' . t('bugfree', 'Setup') . ',' . t('bugfree', 'Security') . ',' . t('bugfree', 'Performance') . ',' . t('bugfree', 'Other'). '", "", "0", "' . t('bugfree', 'Case type') . '", "case", "case_status", 8,
                "", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "CaseMethod", "single select", "' .  t('bugfree', 'Manual') . ',' . t('bugfree', 'Automation') .'", "", "0", "' . t('bugfree', 'Case method') . '", "case", "case_status", 7,
                "", "no", "", ' . $projectId . ', "0", "", "1"),
                (1, NOW(), 0, NOW(), 0, "CasePlan", "single select", "' .  t('bugfree', 'Function') . ',' . t('bugfree', 'UnitTest') . ',' . t('bugfree', 'BVT') . ',' . t('bugfree', 'Intergrate') . ',' . t('bugfree', 'System') . ',' . t('bugfree', 'Smoke') . ',' . t('bugfree', 'Acceptance') .'", "", "0", "' . t('bugfree', 'Case plan') . '", "case", "case_status", 6,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptStatus", "single select", "' . t('bugfree', 'NotPlanned') . ',' . t('bugfree', 'Planning') . ',' . t('bugfree', 'Blocked') . ',' . t('bugfree', 'Coding') . ',' . t('bugfree', 'CodingDone') . ',' . t('bugfree', 'Reviewed') .'", "", "0", "' . t('bugfree', 'Script status') . '", "case", "case_script", 5,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptedBy", "single user", "", "", "0", "' . t('bugfree', 'Scripted by') . '", "case", "case_script", 4,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptedDate", "date", "", "", "0", "' . t('bugfree', 'Scripted Date') . '", "case", "case_script", 3,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "ScriptLocation", "text", "", "", "0", "' . t('bugfree', 'Script location') . '", "case", "case_script", 2,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "CaseKeyword", "text", "", "", "0", "' . t('bugfree', 'Keyword') . '", "case", "case_other", 8,
                "", "no", "", ' . $projectId . ', "0", "", "0"),
                (1, NOW(), 0, NOW(), 0, "DisplayOrder", "text", "", "0", "0", "' . t('bugfree', 'Display order') . '", "case", "case_other", 7,
                "", "no", "", ' . $projectId . ', "0", "", "0");';
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        if($result)
        {
            $sql = 'DROP TABLE IF EXISTS `ettoncase_' . $projectId . '`;
                    CREATE TABLE `ettoncase_' . $projectId . '`(
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `case_id` INT(11) NOT NULL,
                      `CaseType` VARCHAR(255) DEFAULT NULL,
                      `CaseMethod` VARCHAR(255) DEFAULT NULL,
                      `CasePlan` VARCHAR(255) DEFAULT NULL,
                      `ScriptStatus` VARCHAR(255) DEFAULT NULL,
                      `ScriptedBy` VARCHAR(255) DEFAULT NULL,
                      `ScriptedDate` DATE DEFAULT NULL,
                      `ScriptLocation` VARCHAR(255) DEFAULT NULL,
                      `CaseKeyword` VARCHAR(255) DEFAULT NULL,
                      `DisplayOrder` VARCHAR(255) DEFAULT NULL,
                      ' . $createCutomFieldSql . '
                      INDEX `'.$this->newpre.'idx_case_id` (`case_id`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            list($result, $infos) = $this->executeSQL($sql, $this->newpre);
            if($result)
            {
                $countSql = 'SELECT max(`FieldID`) as count FROM `' . $this->oldpre . 'addoncase_' . $projectId. '`';
                $countResult = mysql_query($countSql, $this->con);
                $countArr = mysql_fetch_array($countResult, MYSQL_ASSOC);
                $count = $countArr['count'];
                $start = 0;
                while($start < $count)
                {
                    $arr = array();
                    for($i = 0; $i < self::PAGE_SIZE; $i++)
                    {
                        $arr[] = ++$start;
                    }
                    $fetchCustomFieldSql = 'SELECT * FROM `' . $this->oldpre . 'addoncase_' . $projectId. '` WHERE `FieldID` IN (' . join(',', $arr) . ')';
                    $customFieldResult = mysql_query($fetchCustomFieldSql, $this->con);
                    $sql = "INSERT INTO `ettoncase_$projectId` ";
                    $field = '';
                    $value = array();
                    while($customFieldRow = mysql_fetch_array($customFieldResult, MYSQL_ASSOC))
                    {
                        $fields = '';
                        $values = '';
                        $comma = '';
                        foreach($customFieldRow as $key => $val)
                        {
                            if('FieldID' == $key)
                            {
                                $key = 'case_id';
                            }
                            $fields .= $comma . '`' . $key . '`';
                            if(empty($val))
                            {
                                $values .= $comma . ' NULL';
                            }
                            else
                            {
                                $values .= $comma . '"' . mysql_real_escape_string($val) . '"';
                            }
                            $comma = ',';
                        }
                        $field = $fields;
                        $value[] = '(' . $values . ')';
                    }
                    if(!empty($field))
                    {
                        $sql .= "($field) VALUES " . join(',', $value);
                        list($subResult, $subInfos) = $this->executeDataSQL($sql, $this->newpre);
                        if(!$subResult)
                        {
                            $result = $subResult;
                            $infos += $subInfos;
                        }
                    }
                }
            }
        }
        return array($result, $infos);
    }

    private function fieldXmlToArr($xmlObj)
    {
        $fieldArr = array();
        if(!empty($xmlObj))
        {
            foreach($xmlObj as $xml)
            {
                $field = array();
                $field['name'] = mysql_real_escape_string((string)$xml->name[0]);
                $field['type'] = mysql_real_escape_string((string)$xml->type[0]);
                $field['text'] = mysql_real_escape_string((string)$xml->text[0]);
                $field['value'] = mysql_real_escape_string((string)$xml->value[0]);
                $field['status'] = mysql_real_escape_string((string)$xml->status[0]);
                $field['option'] = mysql_real_escape_string((string)$xml->option[0]);
                $fieldArr[] = $field;
            }
        }
        return $fieldArr;
    }

    private function fieldTypeTranslate($type)
    {
        switch($type)
        {
            case 'text': {
                $type = 'text';
                break;
            }
            case 'textarea': {
                $type = 'textarea';
                break;
            }
            case 'select':
            case 'radio': {
                $type = 'single select';
                break;
            }
            case 'mulit':
            case 'checkbox': {
                $type = 'multi select';
                break;
            }
            case 'user': {
                $type = 'single user';
                break;
            }
            case 'date': {
                $type = 'date';
                break;
            }
            default: {
                $type = 'text';
                break;
            }
        }
        return $type;
    }

    private function fieldStatusTranslate($status)
    {
        if('disable' == $status)
        {
            $status = 1;
        }
        else
        {
            $status = 0;
        }
        return $status;
    }

    private function fieldOptionTranslate($option)
    {
        if('not null' == $option)
        {
            $option = '1';
        }
        else
        {
            $option = '0';
        }
        return $option;
    }

    private function getUserIdsByName($nameStr)
    {
        $nameStr = mysql_real_escape_string($nameStr);
        $nameStr = str_replace(',', '","', $nameStr);
        $nameStr = substr($nameStr, 2, (strlen($nameStr) - 4));
        $fetchUserIdSql = 'SELECT `UserID` as `id` FROM `' . $this->oldpre . 'TestUser` WHERE `UserName` IN (' . $nameStr . ')';
        $result = mysql_query($fetchUserIdSql, $this->con);
        $userIds = array();
        while($result && $row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $userIds[] = $row['id'];
        }
        return $userIds;
    }

    private function insertCustemField($projectId, $type, $id, $sql)
    {
        $table = 'etton' . $type . '_' . $projectId;
        $field = $type . '_id';
        $judgeSql = 'SELECT * FROM `'. $this->newpre . $table . '` WHERE `' . $field . '` = ' . $id . '';
        $result = mysql_query($judgeSql);
        if($result && mysql_fetch_array($result))
        {
            $sql = 'UPDATE `' . $table . '` SET '. $sql . ' WHERE `' . $field . '` = ' . $id;
        }
        else
        {
            $sql = 'INSERT INTO `' . $table .'` SET ' . $sql;
        }
        list($result, $infos) = $this->executeDataSQL($sql, $this->newpre);
        return array($result, $infos);
    }

    private function getRealnamesByMailTo($mailTo)
    {
        $mailTo = trim($mailTo, ',');
        $realnames = array();
        $mailToArr = explode(',', $mailTo);
        foreach($mailToArr as $name)
        {
            $realnames[] = $this->getRealNameByName($name);
        }
        return join(',', $realnames);
    }

    private function getActivatedCount($bugId)
    {
        $countSql = 'SELECT count(*) as count FROM `' . $this->oldpre . 'TestAction` WHERE `ActionTarget` = "Bug" AND `ActionType` = "Activated" AND `IdValue` = "'
                . $bugId . '"';
        $result = mysql_query($countSql, $this->con);
        $count = 0;
        if($result && $row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $count = $row['count'];
        }
        return $count;
    }

    private function getRealNameByName($name)
    {
        $realname = mysql_real_escape_string($name);
        $fetchUserIdSql = 'SELECT `realname` FROM `' . $this->newpre . 'test_user` WHERE `username` = "' . $realname . '"';
        $result = mysql_query($fetchUserIdSql, $this->con);
        if($result && $row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            $name = $row['realname'];
        }
        return $name;
    }

    private function bbcode2html($data)
    {
        $data = htmlspecialchars($data);
        $data = nl2br(stripslashes(addslashes($data)));

        $search = array("\n", "\r");
        $replace = array("", "");
        $data = str_replace($search, $replace, $data);

        $data = str_replace('<br />', "<br />\r\n", $data);

        $search = array(
            "/\[email\](.*?)\[\/email\]/si",
            "/\[email=(.*?)\](.*?)\[\/email\]/si",
            "/\[url\](.*?)\[\/url\]/si",
            "/\[url=(.*?)\]([^]]*?)\[\/url\]/si",
            "/\[img\](.*?)\[\/img\]/si",
            "/\[code\](.*?)\[\/code\]/si",
            "/\[pre\](.*?)\[\/pre\]/si",
            "/\[list\](.*?)\[\/list\]/si",
            "/\[\*\](.*?)/si",
            "/\[b\](.*?)\[\/b\]/si",
            "/\[i\](.*?)\[\/i\]/si",
            "/\[u\](.*?)\[\/u\]/si",
        );
        $replace = array(
            "<a href=\"mailto:\\1\">\\1</a>",
            "<a href=\"mailto:\\1\">\\2</a>",
            "<a href=\"\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"\\1\" target=\"_blank\">\\2</a>",
            "<img src=\"\\1\" border=\"0\">",
            "<p><blockquote><font size=\"1\">code:</font><hr noshade size=\"1\"><pre>\\1</pre><br><hr noshade size=\"1\"></blockquote></p>",
            "<pre>\\1<br></pre>",
            "<ul>\\1</ul>",
            "<li>\\1</li>",
            "<strong>\\1</strong>",
            "<i>\\1</i>",
            "<u>\\1</u>",
        );
        $data = preg_replace($search, $replace, $data);

        $search = array(
            "/\[bug\](\d*?)\[\/bug\]/si",
            "/\[case\](\d*?)\[\/case\]/si",
            "/\[result\](\d*?)\[\/result\]/si",
        );
        $replace = array(
            "<a href=\"Bug.php?BugID=\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"Case.php?CaseID=\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"Result.php?ResultID=\\1\" target=\"_blank\">\\1</a>",
        );
        $data = preg_replace($search, $replace, $data);

        return $data;
    }

    private function beforeUpgrade()
    {
        // add test module path
        $sql = 'ALTER TABLE  `' . $this->oldpre . 'TestModule` ADD  `ModulePath` TEXT NULL';
        mysql_query($sql, $this->con);
        $sql = 'SELECT * FROM ' . $this->oldpre . 'TestModule';
        $result = mysql_query($sql, $this->con);
        while($row = mysql_fetch_array($result))
        {
            list($grade, $modulePath) = $this->getModuleGrade($row['ModuleID']);
            $sql = 'UPDATE ' . $this->oldpre . 'TestModule SET ModuleGrade = ' . $grade . ', ModulePath = "' . $modulePath . '" WHERE ModuleID = ' . $row['ModuleID'];
            mysql_query($sql, $this->con);
        }
        // add test user index
        $sql = 'ALTER TABLE  `' . $this->oldpre . 'TestUser` ADD INDEX  `'.$this->newpre.'idx_username` (  `UserName` )';
        mysql_query($sql, $this->con);
        // add test action index
        $sql = 'ALTER TABLE  `' . $this->oldpre . 'TestAction` ADD INDEX  `'.$this->newpre.'idx_idvalue` (  `IdValue` )';
        mysql_query($sql, $this->con);
    }

    private function getModuleGrade($id)
    {
        $sql = 'SELECT * FROM `' . $this->oldpre . 'TestModule` WHERE `ModuleID` = ' . $id;
        $result = mysql_query($sql, $this->con);
        $module = mysql_fetch_array($result);
        $module['ModuleName'] = str_replace('/', '-', $module['ModuleName']);
        $modulePathArr[] = $module['ModuleName'];
        $grade = 1;
        $modulePath = '';
        while($module['ParentID'] > 0)
        {
            $sql = 'SELECT * FROM `' . $this->oldpre . 'TestModule` WHERE `ModuleID` = ' . $module['ParentID'];
            $result = mysql_query($sql, $this->con);
            $module = mysql_fetch_array($result);
            $module['ModuleName'] = str_replace('/', '-', $module['ModuleName']);
            $modulePathArr[] = $module['ModuleName'];
            $grade++;
        }
        $modulePath = join('/', array_reverse($modulePathArr));
        return array($grade, $modulePath);
    }

    private function htmldecode($html)
    {
        if(is_array($html))
        {
            foreach($html as $key => $val)
            {
                $html[$key] = $this->htmldecode($val);
            }
        }
        else
        {
            $html = htmlspecialchars_decode($html);
        }

        return $html;
    }
}
?>