<?php $this->beginContent('//layouts/main'); ?>
<?php
$productClass = 'selectedtab';
$testUserClass = '';
$userGroupClass = '';
$userLogClass = '';
$adminLogClass = '';
$testOptionClass = '';
$route = $_SERVER['REQUEST_URI'];
$productClass = '';
$routeArr['product'] = array('product/index','product/edit',
    'productModule/index','fieldConfig/index','fieldConfig/edit');
$routeArr['testUser'] = array('testUser/index','testUser/adminedit');
$routeArr['userGroup'] = array('userGroup/index','userGroup/edit');
$routeArr['testOption'] = array('testOption/index','testOption/edit');
$routeArr['adminLog'] = array('adminAction/index');
$routeArr['userLog'] = array('userLog/index');
foreach($routeArr as $key=>$value)
{
    $findFlag = false;
    foreach($value as $routeTmp)
    {
        if(strpos($route, $routeTmp))
        {
            $findFlag = true;
            break;
        }
    }
    if($findFlag)
    {
        $routClassName = $key.'Class';
        $$routClassName = 'selectedtab';
        break;
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/blue.css" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_blue.ico" type="image/x-icon" />
<script type="text/javascript">
    $(function(){
        disableHiddenPager();
        setSearchHeight();
        document.onkeydown = disableReturnSubmit;
    })
    $(window).resize(function() {
        setSearchHeight();
    });
    function disableReturnSubmit(event){
        if(event==null){
            event=window.event;//IE
        }
        if(event.keyCode==13||event.which==13){
            return false;
        }
    }
    function disableHiddenPager()
    {
        $('div.pager li.hidden a').removeAttr('href');
    }
    function setSearchHeight()
    {
        $height = $(window).height();
        if($.browser.msie)
        {
            $('#SearchResultDiv').css('height',$height-120+'px');
        }
        else
        {
            $('#SearchResultDiv').css('height',$height-90+'px');
        }
    }
</script>
<div style="height: 100%;">
    <div id="top">
        <div id="logo">
            <a href="<?php echo Yii::app()->createUrl('info/index',array('type'=>Info::TYPE_BUG,'product_id'=>Yii::app()->user->getState('product')));?>">
                <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/blue/logo.png'; ?>" alt="BugFree" title="BugFree"/>
            </a>
        </div>
        <div id="top-nav">
            <ul class="menu">
                <li id="product_management">
                    <a class="<?php echo $productClass; ?>" href="<?php echo Yii::app()->createUrl('product/index');?>" ><?php echo Yii::t('Common', 'Product Management'); ?></a>
                </li>
                <li id="user_management">
                    <a class="<?php echo $testUserClass; ?>" href="<?php echo Yii::app()->createUrl('testUser/index');?>"><?php echo Yii::t('Common', 'User Management'); ?></a>
                </li>
                <li id="group_management">
                    <a class="<?php echo $userGroupClass; ?>" href="<?php echo Yii::app()->createUrl('userGroup/index');?>"><?php echo Yii::t('Common', 'Group Management'); ?></a>
                </li>
                <?php
                if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin'))
                {
                    echo '<li id="system_setting"><a class="'.$testOptionClass.'" href="'.Yii::app()->createUrl('testOption/index').'">'.
                        Yii::t('Common', 'System Setting').'</a></li>';
                    echo '<li id="admin_log"><a class="'.$adminLogClass.'" href="'.Yii::app()->createUrl('adminAction/index').'">'.
                        Yii::t('Common', 'Admin Log').'</a></li>';
                    echo '<li id="user_log"><a class="'.$userLogClass.'" href="'.Yii::app()->createUrl('userLog/index').'">'.
                        Yii::t('Common', 'User Log').'</a></li>';
                }
                ?>
            </ul>
        </div>
        <div class="user-info" style="font-size: 20px;">
            <?php echo Yii::t('Common', 'Administration'); ?>
        </div>
    </div>
    <div class="maincontainer">
        <?php
            if(isset($this->breadcrumbs))
            {
                $this->widget('zii.widgets.CBreadcrumbs', array(
                    'homeLink' => false,
                    'links'=>$this->breadcrumbs
                ));
            }
            echo '<div id="flash-message">' . Yii::app()->user->getFlash('successMessage') . '</div>';
            echo $content;
        ?>
    </div>
</div>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'admin_mask_dialog',
        // additional javascript options for the dialog plugin
        'options'=>array(
            'title' => Yii::t('AdminCommon','Operation Tips'),
            'autoOpen'=>false,
            'width' => '300px',
            'modal' => true,
            'height' => 'auto',
            'resizable' => false,
        )
    ));

    echo '<br/><img style="float:left;" src="' .
                Yii::app()->theme->baseUrl . '/assets/images/processing.gif" /><div style="margin-top:10px;">' .
                Yii::t('AdminCommon', 'Under processing, please wait ...').'</div>';
                $this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php $this->endContent(); ?>

