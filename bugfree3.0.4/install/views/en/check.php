<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="css/main.css" />
        <title>Check - BugFree</title>
    </head>
    <body>
        <div id="page">
            <div id="header">
                <img src="images/blue_logo.png" alt="BugFree" />
                <ul id="menu">
                    <li class="active">1. Check</li>
                    <li>2. Configure</li>
                    <li>3. Install</li>
                </ul>
            </div><!-- header-->
            <form id="content" method="get">
                <table class="result">
                    <tr>
                        <th colspan="2">System Information</th>
                    </tr>
                    <tr>
                        <td>Server Information</td>
                        <td><?php echo php_uname('s') . ' ' . php_uname('r') . ' On ' . php_uname('m'); ?></td>
                    </tr>
                    <tr>
                        <td>HTTP Server Information</td>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                </table>
                <table class="result">
                    <tr>
                        <th>Name</th><th>Required By</th><th>Result</th>
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
                        <th>Path</th><th>Readable</th><th>Writable</th>
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
                    <input type="button" value="Try again" class="button" onclick="location.reload()"/>
                    <input type="submit" value="Next" class="button" <?php if (!$checkResult) { ?> disabled = "disabled" <?php } ?>/>
                </p>
            </form>
        </div><!-- page -->
    </body>
</html>