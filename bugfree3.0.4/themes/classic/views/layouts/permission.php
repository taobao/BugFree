<?php $this->beginContent('//layouts/main'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/blue.css" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_blue.ico" type="image/x-icon" />
<div style="height: 100%;">
    <div id="top">
        <div id="logo">
            <a href="<?php echo Yii::app()->baseUrl; ?>">
                <img src="<?php echo Yii::app()->theme->baseUrl . '/assets/images/blue/logo.png'; ?>" alt="BugFree" title="BugFree"/>
            </a>
        </div>
        <div id="edit-my-info">

        </div>
    </div>
    <div class="maincontainer">
        <?php
            echo $content;
        ?>
    </div>
</div>
<?php $this->endContent(); ?>

