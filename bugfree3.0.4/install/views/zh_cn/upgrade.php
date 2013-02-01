<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
        <title>备份 - BugFree</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li>1. 环境检查</li>
                    <li class="active">2. 备份</li>
                    <li>3. 升级</li>
                </ul>
            </div><!-- header-->
            <div id="content">
                <div class="loading">
                    正在升级...
                </div>
                <table class="result">
                    <tr>
                        <th>
                            <h3>备份</h3>
                        </th>
                    </tr>
                    <td>
                        <p>
                            当前BugFree版本
                            <b><?php echo $_CFG['versionMap'][$version]; ?></b>
                        </p>
                        <p>
                            建议升级前<a href="index.php?action=backup&amp;download">备份数据</a>
                        </p>
                        <?php
                            if(16 > $version)
                            {
                                echo '<p>系统管理员：<input id="admins" name="admin" type="text" />请添写旧版本配置文件中管理员名称，类似admin1,admin2</p>';
                            }
                        ?>
                        <p>
                            <input type="button" id="upgrade-btn" style="height:28px" value="升级至BugFree <?php echo $_CFG['version']; ?>"/>
                        </p>
                    </td>
                </table>
                <table class="result">
                    <tr>
                        <th>
                            <h3>升级运行记录</h3>
                        </th>
                    </tr>
                    <td id="log" style="padding: 5px; line-height: 20px;">
                    </td>
                </table>
            </div>
        </div><!-- page -->
    </body>
    <script type="text/javascript">
        $(document).ready(function(){            
            $("#upgrade-btn").click(function(){
                $(this).attr("disabled", "disabled");
                $("div.loading").show();
                $("#log").text("");
                $("div.loading").text("正在升级...");
                upgrade();
            });
            
            var upgrade = function(){
                var step    = arguments[0] ? arguments[0] : 1;
                var dbversion = arguments[1];
                var data = {action: 'upgrade', 'step': step};
                if(1 == step) {
                    var admins = $("#admins").val();
                    var data = {action: 'upgrade', 'step': step, 'admin': admins};
                }
                if(typeof(dbversion) != 'undefined') {
                    data = {action: 'upgrade', 'step': step, 'dbversion': dbversion};
                }
                $.getJSON('index.php', data, function(json){
                    if(2 == json.result) {
                        $("#log").append("Upgrade completed.");
                        setTimeout("location.href = \"index.php?action=upgraded\";", 5000);
                    } else {
                        var log = json.info.replace(/\n/g, "<br/>");
                        $("#log").append(log + "<br/>");
                        if(0 == json.result) {
                            upgrade(json.step, json.dbversion);
                        } else {
                            $("#upgrade-btn").removeAttr("disabled");
                            $("div.loading").text("升级失败");
                        }
                    }
                });
            }
        })
    </script>
</html>