<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <title>环境检查 - BugFree</title>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li class="active">1. 环境检查</li>
                    <li>2. 配置</li>
                    <li>3. 安装</li>
                </ul>
            </div><!-- header-->
            <form id="content" method="get">
                <table class="result">
                    <tr>
                        <th colspan="2">系统信息</th>
                    </tr>
                    <tr>
                        <td>服务器系统</td>
                        <td><?php echo php_uname('s') . ' ' . php_uname('r') . ' On ' . php_uname('m'); ?></td>
                    </tr>
                    <tr>
                        <td>服务器软件</td>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                </table>
                <table class="result">
                    <tr>
                        <th>项目</th><th>需求</th><th>当前</th>
                    </tr>
                    <?php foreach ($requirements as $requirement): ?>
                        <tr>
                            <td>
                                <?php echo $requirement[0]; ?>
                            </td>
                            <td>
                                <?php echo $requirement[3]; ?>
                            </td>
                            <td>
                                <strong class="<?php echo $requirement[1] ? 'supported' : 'not-supported' ?>">
                                    <?php
                                    echo $requirement[1] ? '√' : '×';
                                    ?>
                                </strong>
                                <?php echo $requirement[2]; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <table class="result">
                    <tr>
                        <th>路径</th><th>读</th><th>写</th>
                    </tr>
                    <?php foreach ($dirRights as $dirRight): ?>
                        <tr>
                            <td>
                                <?php echo $dirRight[0]; ?>
                            </td>
                            <td>
                                <strong class="<?php echo $dirRight[2] ? 'supported' : 'not-supported' ?>">
                                    <?php
                                    echo $dirRight[2] ? '√' : '×';
                                    ?>
                                </strong>
                            </td>
                            <td>
                                <strong class="<?php echo $dirRight[3] ? 'supported' : 'not-supported' ?>">
                                    <?php
                                    echo $dirRight[3] ? '√' : '×';
                                    ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="buttons">
                    <input type="hidden" name="action" value="config" />
                    <input type="button" value="再试一次" class="button" onclick="location.reload()"/>
                    <input type="submit" value="继续" class="button" <?php if (!$checkResult) { ?> disabled = "disabled" <?php } ?>/>
                </p>
            </form>
        </div><!-- page -->
    </body>
</html>