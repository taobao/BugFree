<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of MapUserInfoService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class MapUserInfoService
{

    /**
     * get user marked info id arr
     *
     * @author                                    youzhao.zxw<swustnjtu@gmail.com>
     * @param   array           $valueArr         id array been searched
     * @param   int             $userId           user id
     * @param   string          $type             bug,case or result
     * @return  array                             marked id array
     */
    public static function getUserMarkedInfoidArr($userId, $type)
    {
        $searchResult = array();
        if(Info::TYPE_BUG == $type)
        {
            $searchResult = MapUserBug::model()->findAllByAttributes(array('test_user_id' => $userId));
        }
        elseif(Info::TYPE_CASE == $type)
        {
            $searchResult = MapUserCase::model()->findAllByAttributes(array('test_user_id' => $userId));
        }
        if(Info::TYPE_RESULT == $type)
        {
            $searchResult = MapUserResult::model()->findAllByAttributes(array('test_user_id' => $userId));
        }
        $returnArr = array();
        foreach($searchResult as $value)
        {
            $returnArr[] = $value['info_id'];
        }
        return $returnArr;
    }

}

?>
