<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <title>配置 - BugFree</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/jquery.form.js"></script>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li>1. 环境检查</li>
                    <li class="active">2. 配置</li>
                    <li>3. 安装</li>
                </ul>
            </div><!-- header-->
            <form id="content" method="post">
                <div class="loading">
                    正在安装...
                </div>
                <table class="result">
                    <tr>
                        <th colspan="2">数据库配置</th>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbhost">数据库服务器</label></td>
                        <td>
                            <input class="text" type="text" name="dbhost" value="localhost"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbname">数据库名</label></td>
                        <td>
                            <input class="text" type="text" name="dbname" value="bugfree"/>
                            若从旧版本升级，请添写相同的数据库名
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="port">端口</label></td>
                        <td>
                            <input class="text" type="text" name="port" value="3306"/>
                            MySQL默认端口号为3306
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbuser">数据库用户名</label></td>
                        <td><input class="text" type="text" name="dbuser"/></td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbpassword">数据库密码</label></td>
                        <td><input class="text" type="password" name="dbpwd"/></td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbprefix">数据表前缀</label></td>
                        <td>
                            <input class="text" type="text" name="dbprefix" value="bf_"/>
                            若从旧版本升级，请填写与旧版本相同的数据表前缀
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbprefix">选择语言</label></td>
                        <td>
                            <select name="language" id="language">
                                <option value="zh_cn" selected="selected">简体中文</option>
                                <option value="en">Englisth</option>                                
                            </select>
                        </td>
                    </tr>
                </table>
                <p style="text-align: center">
                    <input type="checkbox" id="accept" name="accept" />
                    <label for="accept">接受BugFree的<a href="../LICENSE" target="_blank">许可协议</a></label>
                </p>
                <p class="buttons">
                    <input type="hidden" name="action" value="install" />
                    <input type="button" value="返回" class="button" onclick="history.go(-1)"/>
                    <input id="install" type="submit" value="安装" class="button" disabled="true"/>
                </p>
            </form>
        </div><!-- page -->
    </body>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".text").focus(function(){
                $(this).addClass("focus");
            }).blur(function(){
                $(this).removeClass("focus");
            });
            
            $("#accept").click(function(){
                if($(this).attr("checked")) {
                    document.getElementById('install').disabled = '';
                } else {
                    $("#install").attr("disabled", "disabled");
                }
            });
            
            $("#content").validate({
                rules: {
                    dbhost: {
                        required: true
                    },
                    dbname: {
                        required: true
                    },
                    port: {
                        required: true,
                        digits: true
                    },
                    dbprefix: {
                        required: true           
                    }
                },
                messages: {
                    dbhost: "请输入数据库服务器名",
                    port: "请输入数据库端口，端口必须为整数",
                    dbpwd: "请输入数据库密码",
                    dbprefix: "请输入数据表前缀"
                },
                submitHandler: function(form) {
                    var installFormOption = {
                        type: 'POST',
                        dataType: 'json',
                        success: function(json) {
                            $("div.loading").hide();
                            $("#install").removeAttr("disabled");
                            if(json.result == 2) {
                                location.href = 'index.php?action=backup';
                            } else if(json.result) {
                                if('alert' == json.target) {
                                    alert(json.info);
                                } else {
                                    var obj = $("input[name=" + json.target + "]").clone(true);
                                    $("input[name=" + json.target + "]").parent().html(obj);
                                    $("input[name=" + json.target + "]").after('<label class="error">' + json.info + '</label>');
                                }
                            } else {
                                location.href = 'index.php?action=installed';
                            }
                        }
                    }
                    $(form).ajaxSubmit(installFormOption);
                    $("#install").attr("disabled", "disabled");
                    $("div.loading").show();
                }
            });
        });
    </script>
</html>