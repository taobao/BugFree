<?php
/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of MixInfo
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MixInfo
 *
 * @author Administrator
 */
class MixInfo
{

    protected $basicInfo;
    protected $customInfo;

    public function setBasicInfo($basicInfo)
    {
        $this->basicInfo = $basicInfo;
    }

    public function setCustomInfo($customInfo)
    {
        $this->customInfo = $customInfo;
    }

    public function getBasicInfo()
    {
        return $this->basicInfo;
    }

    public function getCustomInfo()
    {
        return $this->customInfo;
    }

}

?>
