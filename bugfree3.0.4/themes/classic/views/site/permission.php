<div style="margin: 0 auto;width:90%;">
    <h2>BugFree权限备注:</h2>
    1. 用户在使用BugFree前，要先请<strong>用户组管理员</strong>将用户的帐号加入到BugFree系统。没有加入的帐号系统会禁止登录。<br/>
    2. 访问限定用户组的产品（即用户组不包括<strong>[All Users]</strong>）时,需要请相应的<strong>用户组管理员</strong>将帐号加入到用户组里。<br/>
    3. 产品如果包含<strong>[All Users]</strong>用户组，则BugFree系统里的所有用户都可以访问该产品。<br/>
    <br/><br/><br/>
    下图红色框即为当前使用的<strong>产品</strong><br/>
    <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/product.png'; ?>"/><br/>
    <br/><br/><br/>
    <?php echo $permissionTable; ?>
</div>