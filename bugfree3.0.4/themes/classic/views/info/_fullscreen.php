<?php
$imgDirection = 'left';
if('left' == $position)
{
    $imgDirection = 'right';
}
?>
<div class="fullscreen_div" id="comment_view_button_<?php echo $position; ?>">
    <img class="fullscreen" src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/arrow_<?php echo $imgDirection; ?>.gif">
</div>