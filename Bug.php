<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of Case
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
if(isset($_GET['BugID']))
{

    ob_start();
    header('location: bug/'.$_GET['BugID']); 
    ob_end_flush(); //now the headers are sent
}
else
{
    echo "invalid request";
}
?>
