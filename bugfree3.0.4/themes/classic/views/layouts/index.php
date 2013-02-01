<?php $this->beginContent('//layouts/main'); ?>
<?php
$color = 'blue';
$bugclass = 'selectedtab';
$caseclass = '';
$resultclass = '';
$productId = $_GET['product_id'];
if(isset($_GET['type']))
{
    $bugclass = '';
    $tag = $_GET['type'];
    if('case' == $tag)
    {
        $color = 'green';
        $caseclass = 'selectedtab';
    }
    elseif('result' == $tag)
    {
        $color = 'orange';
        $resultclass = 'selectedtab';
    }
    else
    {
        $bugclass = 'selectedtab';
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/<?php echo $color; ?>.css" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_<?php echo $color; ?>.ico" type="image/x-icon" />
<div style="height: 100%;">
    <div id="top">
        <div id="logo">
            <a href="<?php echo Yii::app()->createUrl('info/index', array('type' => Info::TYPE_BUG, 'product_id' => $productId)); ?>">
                <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/' . $color . '/logo.png'; ?>"
                     alt="BugFree" title="BugFree"/>
            </a>
        </div>
        <div id="top-nav">
            <ul class="menu">
                <li id="bug">
                    <a class="<?php echo $bugclass; ?>" href="<?php echo Yii::app()->createUrl('info/index', array('type' => Info::TYPE_BUG, 'product_id' => $productId)); ?>" >Bug</a>
                </li>
                <?php
                $isShowCaseResultTab = Yii::app()->params['showCaseResultTab'];
                if($isShowCaseResultTab)
                {
                    echo '<li id="case"><a class="'.$caseclass.'" href="'.Yii::app()->createUrl('info/index', array('type' => Info::TYPE_CASE, 'product_id' => $productId)).'">Case</a></li>';
                    echo '<li id="result"><a class="'.$resultclass.'" href="'.Yii::app()->createUrl('info/index', array('type' => Info::TYPE_RESULT, 'product_id' => $productId)).'">Result</a></li>';
                }
                ?>
                <li id="createli">
                </li>
            </ul>
        </div>
        <div class="user-info">
            <?php
                if(!Yii::app()->user->isGuest)
                {

                    $rightTopLinkStr = Yii::t('Common', 'Welcome') . ",&nbsp;&nbsp;" . CHtml::encode(Yii::app()->user->realname) .
                            "&nbsp;|&nbsp;<a target='_blank' href='" .
                            Yii::app()->createUrl('testUser/edit', array('id' => Yii::app()->user->id)) .
                            "'>" . Yii::t('Common', 'Edit My Info') . "</a>&nbsp;|&nbsp;";
                    if(CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_admin') ||
                            CommonService::$TrueFalseStatus['TRUE'] == Yii::app()->user->getState('system_manager'))
                    {
                        $rightTopLinkStr .= "<a target='_blank' href='" . Yii::app()->createUrl('product/index') . "'>" . Yii::t('Common', 'Administration') . "</a>&nbsp;|&nbsp;";
                    }
                    $rightTopLinkStr .= "<a href='" . Yii::app()->createUrl('site/logout') . "'>" . Yii::t('Common', 'Logout') . "</a>&nbsp;|&nbsp;";
                    $rightTopLinkStr .= "<a href='http://testing.etao.com/handbook/bugfree' target='_blank'>" . Yii::t('Common', 'Help') . "</a>";
                    echo $rightTopLinkStr;
                }
            ?></div>
        </div>
        <div id="indexmain" class="maincontainer">
        <?php echo $content; ?>
            </div>
        </div>
<?php $this->endContent(); ?>

