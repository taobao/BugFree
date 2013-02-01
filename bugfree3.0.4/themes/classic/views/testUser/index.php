<script type="text/javascript">
    $(function(){
        $('a.disable_user').click(function(){
            if(confirm('<?php echo Yii::t('Common', 'Are you sure?'); ?>'))
            {
                var changeToStatus = '0';
                var nowStatus = $(this).attr('drop_status');
                if('0' == nowStatus)
                {
                    changeToStatus = '1';
                }
                changeUserStatus($(this).attr('user_id'),changeToStatus);
            }
            
        });
    })
    function changeUserStatus($userId,$dropStatus)
    {
        jQuery.get("<?php echo Yii::app()->createUrl('testUser/disable'); ?>", {'id':$userId,'is_dropped':$dropStatus}, function (data, textStatus){
            if('success' == textStatus)
            {
                if('' == data)
                {
                    window.location.href = window.location.href;
                }
                else
                {
                    alert(data);
                }
            }
        });
    }   
</script>
<div class="admin_search">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('testUser/index'), 'get'); ?>
        <a class="add_link" href="<?php echo Yii::app()->createUrl('testUser/adminedit'); ?>" ><?php echo Yii::t('TestUser', 'Add User'); ?></a>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">&nbsp;
        <input class="btn" type="submit" value="<?php echo Yii::t('Common', 'Post Query'); ?>">
        <input class="btn" type="reset" value="<?php echo Yii::t('Common', 'Reset Query'); ?>" onclick="window.location.href= '<?php echo Yii::app()->createUrl('testUser/index'); ?>'">
    <?php echo CHtml::endForm(); ?>
</div>
<?php
$this->widget('View', array(
    'id' => 'searchresult-grid',
    'dataProvider' => $dataProvider,
    'rowCssClassExpression' => 'CommonService::getRowCss($data["is_dropped"])',
    'columns' => array(
        'id',
        'username',
        array('name' => Yii::t('TestUser','authmode'),'type'=>'raw',
            'value' => 'TestUserService::getModeMessage($data["authmode"])'),
        'realname',
        'email',
        array('name' => Yii::t('TestUser','email_flag'),'type'=>'raw',
            'value' => 'CommonService::getTrueFalseName($data["email_flag"])'),
        array('name' => Yii::t('TestUser','group_name'),'type'=>'raw',
            'value' => 'TestUserService::getUserGroupOption($data["id"])'),
        array('name' => Yii::t('Common','Operation'),'type'=>'raw',
            'value'=>'TestUserService::getUserOperation($data["id"],$data["created_by"],$data["authmode"],$data["is_dropped"])'),
        array('name' => 'created_by', 'value' => 'CommonService::getUserRealName($data["created_by"])'),
        'created_at',
        array('name' => 'updated_by', 'value' => 'CommonService::getUserRealName($data["updated_by"])'),
        'updated_at'
    )
));
?>


