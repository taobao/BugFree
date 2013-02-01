<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/assets/css/login.css" />
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
<div id="LoginContainer">
    <img src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/login_bg_left.gif" class="loginBgImage"/>
    <div id="LoginMain">
        <div id="LoginLogo">
            <span id="Version">3.0</span>
        </div>
        <div id="LoginFormContainer">
                <?php
        $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'LoginForm',
                    'enableClientValidation' => true,
                    'clientOptions' => array(
                        'validateOnSubmit' => true,
                    ),
                ));
        ?>
            <p>
                <label for="TestUserName"><strong id="ForTestUserName"><?php echo Yii::t('LoginForm','Username') ?></strong></label>
                <?php
                echo $form->textField($model, 'username',
                        array('class' => 'TxtInput'));
                ?>
            </p>

            <p>
                <label for="TestUserPWD"><strong id="ForTestUserPWD"><?php echo Yii::t('LoginForm','Password') ?></strong></label>
                <?php echo $form->passwordField($model, 'password', array('class' => 'TxtInput')); ?>
            </p>
            <p>
                <label for="Language"><strong id="ForLanguage"><?php echo Yii::t('LoginForm','Language') ?></strong></label>
                <?php
                    echo $form->dropDownList($model, 'language',
                            array(LoginForm::LANGUAGE_ZH_CN => '简体中文',
                                LoginForm::LANGUAGE_EN => 'English'),
                            array('onchange' => 'switchLanguage($(this).val());','class'=>'select'));
                    ?>
            </p>

            <p>
                <label>&nbsp;</label>
                <?php echo $form->checkBox($model, 'rememberMe'); ?><span id="ForRememberMe" style="margin:20px 0 0"><?php echo Yii::t('LoginForm', 'Remember me'); ?></span>
            </p>
            <p>
                <input type="submit" id="SubmitLoginBTN" value="<?php echo Yii::t('LoginForm', 'Login') ?>" accesskey="L" class="loginSubmit" />
            </p>
            <?php
                $loginErrors = $model->getErrors();
                if(!empty($loginErrors))
                {
                    $errorMsg = '';
                    foreach($loginErrors as $key => $value)
                    {
                        $errorMsg .= $value[0] . '&nbsp;&nbsp;';
                    }
                    echo '<div id="ActionMessage" class="Error">'.$errorMsg.'</div>';
                }
            ?>
            <?php $this->endWidget(); ?>
        </div>
    </div>
    <img src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/login_bg_right.gif" class="loginBgImage"/>
    <br class="clear" />
    <div id="shadow">
      <img src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/login_shadow_left.gif" class="loginBgImage"/>
      <div id="ShadowCenter">
        <center>
        <br/>
                     请使用域帐号(类似taobao-hz\yourname）和域密码进行登录
        </center>
        <center>
        <br/>
                     没有帐号或权限？点击：<a href="http://twiki.corp.taobao.com/bin/view/Taobao_AD_QA/Permission" target="_blank">新用户注册和权限申请</a>
        </center>
      </div>
      <img src="<?php echo Yii::app()->theme->baseUrl; ?>/assets/images/login_shadow_right.gif" class="loginBgImage"/>
    </div>
</div>
