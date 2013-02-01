<?php
/* 
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type"     content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="UTF-8">
<style type="text/css">
body{font-family: Tahoma;font-size: 12px;margin:0;padding:0;}
a:link{color: #0164C9;}a:visited{color: #0164C9;}
table.CommonTable{border-collapse: collapse;width:100%;border:1px solid #808080;background-color: #FFFFFF;font-size:12px;}
table.CommonTable caption{font-size:13px;border:1px solid #808080;margin:0;}
table.CommonTable th, table.CommonTable td{margin:1px;padding:2px 1px 1px;border-width: 1px 0;border-style:solid;border-color:#DDDDBB;}
.BugStatusActive{background-color: #FFDDDD;}
.BugStatusResolved{background-color: #FFFFBB;}
.BugStatusClosed{background-color: #FFFFFF;}
.BugStatusClosed a {color: #878787;}
.BugStatusResolved a {color:#000000;}
.BugStatusActive a {color: #115FA2;font-weight:bold;}
</style>
<title>Bug Notice</title>
</head>
<body style="margin:16px;">
 <table width="98%" align="center" class="CommonTable BugMode">
    <tr>
      <th><?php echo Yii::t('Common','id');?></th>
      <th><?php echo Yii::t('BugInfo','severity');?></th>
      <th><?php echo Yii::t('BugInfo','priority');?></th>
      <th><?php echo Yii::t('BugInfo','title');?></th>
      <th><?php echo Yii::t('BugInfo','bug_status');?></th>
      <th><?php echo Yii::t('Common','created_by');?></th>
      <th><?php echo Yii::t('Common','assign_to');?></th>
      <th><?php echo Yii::t('BugInfo','resolved_by');?></th>
      <th><?php echo Yii::t('BugInfo','solution');?></th>
      <th><?php echo Yii::t('Common','updated_at');?></th>
    </tr>
    <?php foreach($bugArr as $bugInfo){?>
    <tr class="BugStatus<?php echo $bugInfo['bug_status'];?>">
      <td align="center">
        <?php echo $bugInfo['id'];?>
      </td>
      <td align="center"><?php echo $bugInfo['severity'];?></td>
      <td align="center"><?php echo $bugInfo['priority'];?></td>
      <td title="<?php echo $bugInfo['title'];?>">
          <a target="_blank" href="<?php echo Yii::app()->createAbsoluteUrl('info/edit',array('type'=>  Info::TYPE_BUG,'id'=>$bugInfo['id']));?>"><?php echo CommonService::sysSubStr($bugInfo['title'], 50, true);?></a>
      </td>
      <td align="center"><?php echo $bugInfo['bug_status'];?></td>
      <td align="center"><?php echo $bugInfo['created_by_name'];?></td>
      <td align="center"><?php echo $bugInfo['assign_to_name'];?></td>
      <td align="center"><?php echo $bugInfo['resolved_by_name'];?></td>
      <td align="center"><?php echo $bugInfo['solution'];?></td>
      <td align="center"><?php echo CommonService::getDateStr($bugInfo['updated_at']);?></td>
    </tr>
    <?php }?>
  </table>
</body>
</html>
