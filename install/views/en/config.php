<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <title>Configure - BugFree</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/jquery.form.js"></script>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li>1. Check</li>
                    <li class="active">2. Configure</li>
                    <li>3. Install</li>
                </ul>
            </div><!-- header-->
            <form id="content" method="post">
                <div class="loading">
                    Installing...
                </div>
                <table class="result">
                    <tr>
                        <th colspan="2">Database Configure</th>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbhost">Database Server</label></td>
                        <td>
                            <input class="text" type="text" name="dbhost" value="localhost"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbname">Database Name</label></td>
                        <td>
                            <input class="text" type="text" name="dbname" value="bugfree"/>
                            Please input the same database name if upgrading from the old version.
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="port">Port</label></td>
                        <td>
                            <input class="text" type="text" name="port" value="3306"/>
                            MySQL default port is 3306.
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbuser">Database User</label></td>
                        <td><input class="text" type="text" name="dbuser"/></td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbpassword">Database Password</label></td>
                        <td><input class="text" type="password" name="dbpwd" /></td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbprefix">Database Table Prefix</label></td>
                        <td>
                            <input class="text" type="text" name="dbprefix" value="bf_"/>
                            Please input the same table prefix if upgrading from the old version.
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label for="dbprefix">Language</label></td>
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
                    <label for="accept">Accept BugFree <a href="../LICENSE" target="_blank">license</a></label>
                </p>
                <p class="buttons">
                    <input type="hidden" name="action" value="install" />
                    <input type="button" value="Back" class="button" onclick="history.go(-1)"/>
                    <input id="install" type="submit" value="Install" class="button" disabled="true"/>
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
                    dbhost: "Please input the database server name.",
                    port: "Please input the port, and the port must be digit.",
                    dbpwd: "Please input the database password.",
                    dbprefix: "Please input the database table prefix."
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