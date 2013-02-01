#!/bin/bash
# File to Mail bugs assigned to somebody of BugFree system.
#
# BugFree is free software under the terms of the FreeBSD License.
#
# @link        http://www.bugfree.org.cn
# @package     BugFree
#
NoticeUrl="http://10.32.20.129/bugfree3/notice/notice"
/opt/lampp/bin/php /opt/lampp/htdocs/bugfree3/notice.php $NoticeUrl
