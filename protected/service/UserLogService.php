<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserLogService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserLogService
{
    public static function createUserLog($params)
    {
        $resultInfo = array();
        $userLog = new UserLog();
        $userLog->attributes = $params;
        if(!$userLog->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $userLog->getErrors();
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            $resultInfo['detail'] = array('id' => $userLog->id);
        }
        return $resultInfo;
    }
}

?>
