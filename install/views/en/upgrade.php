<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="favicon.png" type="image/x-png" />
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <title>Backup - BugFree</title>
        <script type="text/javascript" src="js/jquery.js"></script>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li>1. Check</li>
                    <li class="active">2. Backup</li>
                    <li>3. Upgrade</li>
                </ul>
            </div><!-- header-->
            <div id="content">
                <div class="loading">
                    Upgrading...
                </div>
                <table class="result">
                    <tr>
                        <th>
                            <h3>Backup</h3>
                        </th>
                    </tr>
                    <td>
                        <p>
                            Current BugFree version
                            <b><?php echo $_CFG['versionMap'][$version]; ?></b>
                        </p>
                        <p>
                            You should better <a href="index.php?action=backup&amp;download">backup</a> before upgrading.
                        </p>
                        <?php
                            if(16 > $version)
                            {
                                echo '<p>Adminitrator：<input id="admins" name="admin" type="text" />Please input the administrators same as the old configuration, such as admin1,admin2</p>';
                            }
                        ?>
                        <p>
                            <input type="button" id="upgrade-btn" style="height:28px" value="Upgrade to BugFree <?php echo $_CFG['version']; ?>"/>
                        </p>
                    </td>
                </table>
                <table class="result">
                    <tr>
                        <th>
                            <h3>Upgrade Log</h3>
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
                $("div.loading").text("Upgrading...");
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
                            $("div.loading").text("Upgrade failed.");
                        }
                    }
                });
            }
        })
    </script>
</html>