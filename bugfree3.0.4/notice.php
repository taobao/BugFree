<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */
@set_time_limit(0);
@ini_set('memory_limit', -1);
$url = $argv[1];
$ch = curl_init($url.'?authkey=12315454364625223345467'); //authkey to prevent user use url directly
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);
return $output;
?>
