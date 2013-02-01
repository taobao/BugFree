<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/blue.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/bugfree3_basic.css" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon_blue.ico" type="image/x-icon" />
<script type="text/javascript">
    var enUserLabel = 'Username';
    var zhUserLabel = '用户名';
    var enPasswdLabel = 'Password';
    var zhPasswdLabel = '密码';
    var enLanguageLabel = 'Language';
    var zhLanguageLabel = '选择语言';
    var enRemember = 'Remember me';
    var zhRemember = '记住密码';
    var enLoginLabel = 'Login';
    var zhLoginLabel = '登录';
    function switchLanguage($language)
    {
        if('en' == $language)
        {
            $('#ForTestUserName').text(enUserLabel);
            $('#ForTestUserPWD').text(enPasswdLabel);
            $('#ForLanguage').text(enLanguageLabel);
            $('#ForRememberMe').text(enRemember);
            $('#SubmitLoginBTN').val(enLoginLabel);
        }
        else
        {
            $('#ForTestUserName').text(zhUserLabel);
            $('#ForTestUserPWD').text(zhPasswdLabel);
            $('#ForLanguage').text(zhLanguageLabel);
            $('#ForRememberMe').text(zhRemember);
            $('#SubmitLoginBTN').val(zhLoginLabel);
        }
    }
</script>
<div id="logincontainer">
    <div id="loginlogo">
        <span id="version">3.0.4</span>
    </div>
    <div id="loginform-container">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'login-form',
                    'enableClientValidation' => true,
                    'clientOptions' => array(
                        'validateOnSubmit' => true,
                    ),
                ));
        ?>
        <table style="margin-left: 20px;">
            <tr>
                <td colspan="2" style="height:40px;padding: 0px;text-align: center;">
                    <?php
                    $loginErrors = $model->getErrors();
                    if(!empty($loginErrors))
                    {
                        $errorMsg = '';
                        foreach($loginErrors as $key => $value)
                        {
                            $errorMsg .= $value[0] . '&nbsp;&nbsp;';
                        }
                        echo '<div id="login-error-div" >' . $errorMsg . '</div>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: right;"><label><strong id="ForTestUserName" style="font-size:14px;"><?php echo Yii::t('LoginForm','Username') ?></strong></label></td>
                <td>
                <?php
                echo $form->textField($model, 'username',
                        array('class' => 'TxtInput'));
                ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: right;"><label><strong id="ForTestUserPWD" style="font-size:14px;"><?php echo Yii::t('LoginForm','Password') ?></strong></label></td>
                <td><?php echo $form->passwordField($model, 'password', array('class' => 'TxtInput')); ?></td>
            </tr>
            <tr>
                <td style="text-align: right;"><label><strong id="ForLanguage" style="font-size:14px;"><?php echo Yii::t('LoginForm','Language') ?></strong></label></td>
                <td>
                    <?php
                    echo $form->dropDownList($model, 'language',
                            array(LoginForm::LANGUAGE_ZH_CN => '简体中文',
                                LoginForm::LANGUAGE_EN => 'English'),
                            array('onchange' => 'switchLanguage($(this).val());','class'=>'select'));
                    ?>
                </td>
            </tr>
            <tr>
                <td><label>&nbsp;</label></td>
                <td><?php echo $form->checkBox($model, 'rememberMe'); ?><span id="ForRememberMe" style="margin:20px 0 0"><?php echo Yii::t('LoginForm', 'Remember me'); ?></span></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" id="SubmitLoginBTN" value="<?php echo Yii::t('LoginForm', 'Login') ?>" accesskey="L" class="loginbutton btn" /></td>
            </tr>
        </table>
        <?php $this->endWidget(); ?>
    </div>
</div>
