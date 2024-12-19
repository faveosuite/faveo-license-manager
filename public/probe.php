<?php

error_reporting(-1);

ini_set('display_errors', 1);

require __DIR__.'/../bootstrap/autoload.php';

use App\Http\Controllers\Dependency\LicenseDependencyController;

//store application's config data such as version, name etc. available in config/app.php
$config = require_once '../config/app.php';
$env = '../.env';
$envFound = is_file($env);
if ($envFound) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/..');
    $dotenv->load();
}

$passwordMatched = false;
$showError = false;
if (isset($_POST['submit'])) {
    $probePhrase = env('PROBE_PASS_PHRASE', '   ');
    //Unique password incase support team requires access to probe.php
    $password = '599fe9896c015afebff1789ea0078f61';

    $input = $_POST['passPhrase'];
    if (! in_array($input, [$probePhrase, $password])) {
        $showError = true;
    } else {
        $passwordMatched = true;
    }
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $appName = 'License Manager';
// $logo = 'themes/default/common/images/installer/faveo.png';
// $ico = 'themes/default/common/images/favicon.ico';
if (isWhiteLabelEnabled()) {
    $appName = str_replace('Faveo ', '', $appName);
    // $logo = 'themes/default/common/images/whitelabel.png';
    // $ico = 'themes/default/common/images/whitefavicon.png';
}

?>
    <title><?=$appName?></title>
    <!--<img src="<?=$logo?>" alt="faveo" width="200px" height="130px">-->
    <!-- links-->
    <!--<link href="<?=$ico?>"  rel="shortcut icon" />-->
    <link href='themes/default/css/bootstrap.min.css' rel="stylesheet" type="text/css"/>
    <link href="themes/default/css/loadstyles.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/css/css.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/css/setup.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/css/probe-custom.css" rel="stylesheet" type="text/css" />
    <!-- links-->
</head>
<?php if ($envFound && ! $passwordMatched) { ?>
    <body>
    <div class="setup-content" style="padding: 10 0 10 0; margin-top: 20px">
        <div style="height: auto; width: 500; margin: auto;  padding: 10 10 10 10;">
            <h1 style="text-align: center; color: #71BEE3"><?php echo isWhiteLabelEnabled() ? '' : 'Faveo '; ?> Probe</h1>
            <?php if ($showError) { ?>
                <h4><span style="color: red">The magic phrase you entered is not working.</span></h4>
            <?php } ?>
            <form method="POST" action="probe.php">
                 table Mod Rewrite block
                <table class="t01">
                    <label style="float: left;">What's the magic phrase</label>
                    <input  style="float: right; width: 300px; height: 25px; outline: none;" type="password" name="passPhrase" autofocus id="passPhrase">
                    <tfoot>
                    <tr>
                        <td style="border: 1px solid #ffffff;">
                             Adding app version to make it easy to identify app version during troubleshooting client's system. As many of times if code is encoded
                            we are stuck to identify application version and support team needs to get login
                            details or ask client the app version.
                            -->
                            <p style="font-size: .8em">
                                <b>App Name:</b> <?= $appName; ?><br/>
                                <b>App Version:</b> <?= $config['tags']; ?>
                            </p>
                        </td>
                        <td style="border: 1px solid #ffffff;">
                            <form action="pre-license" method="post"  class="border-line">
                                <p class="setup-actions step">
                                    <button type="submit" name="submit" id="passSubmit" class="button button-large" style="float: right;" disabled>Continue</button>
                                </p>
                            </form>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                 .table
            </form>
        </div>
    </div>
    </body>
<?php } else { ?>
    <body>

    <ol class="setup-steps" style = "margin-left: 70px;">
        <li class="active">Server Requirements</li>
        <!--<li class="@yield('license')">License Agreement</li>-->
        <!-- <li class="@yield('environment')">Environment Test</li> -->
        <li class="@yield('database')">Database Setup</li>
        <li class="@yield('locale')">Getting Started</li>
        <li class="@yield('ready')">Final</li>
    </ol>
    <div class="setup-content">
        <div style="width: 700; margin: auto;">
            <h1 style="text-align: center; color: #71BEE3">Server Requirements</h1>

            <!-- table Directory Permission block-->
            <?php
        $errorCount = 0;
    $basePath = substr(__DIR__, 0, -6);
    $details = (new LicenseDependencyController('probe'))->validateDirectory($basePath, $errorCount);
    $table = '<table class="t01">
                <tr>
                    <th style="width: 40%;">Directory</th>
                    <th>Permissions</th>
                </tr>';
    $extColor = 'green';
    foreach ($details as $detail) {
        $table = $table.'<tr><td>'.$detail['extensionName']."</td><td style='color:".$detail['color']."'>".$detail['message'].'</td></tr>';
    }
    $table = $table.'</table>';

    echo htmlspecialchars_decode($table);
    ?>





            <!-- table Requirement Check block-->
            <?php
            $details = (new LicenseDependencyController('probe'))->validateRequisites($errorCount);
    $table = '<table class="t01">
                <tr>
                    <th style="width: 40%;">Requisites</th>
                    <th>Status</th>
                </tr>';
    $extColor = 'green';
    foreach ($details as $detail) {
        $table = $table.'<tr><td>'.$detail['extensionName']."</td><td style='color:".$detail['color']."'>".$detail['connection'].'</td></tr>';
    }
    $table = $table.'</table>';
    echo htmlspecialchars_decode($table);
    ?>



            <!-- .table -->


            <!-- table PHP Extension Check block-->


                <?php
    $details = (new LicenseDependencyController('probe'))->validatePHPExtensions($errorCount);
    $table = '<table class="t01">
                <tr>
                    <th style="width: 40%;">PHP Extensions</th>
                    <th>Status</th>
                </tr>';
    $extColor = 'red';
    $extString = 'Enabled';
    $extraStringForRedisExtension = (isWhiteLabelEnabled()) ? ' style="pointer-events: none;color:#444;"' : ' target="_blank"';
    foreach ($details as $detail) {
        $extString = "Not Enabled<p>To enable this, please install the extension on your server and  update '".php_ini_loaded_file()."' to enable ".$detail['extensionName'].'</p>'
                    .'<a href="https://support.faveohelpdesk.com/show/how-to-enable-required-php-extension-on-different-servers-for-faveo-installation"'.$extraStringForRedisExtension.'>How to install PHP extensions on my server?</a>';

        if ($detail['key'] == 'required') {
            $extColor = 'red';
            $errorCount += 1;
        } elseif ($detail['key'] == 'optional') {
            $extColor = '#F89C0D';
        } else {
            $extColor = 'green';
            $extString = 'Enabled';
        }

        $table = $table.'<tr><td>'.$detail['extensionName']."</td><td style='color:$extColor'>$extString</td></tr>";
    }
    $table = $table.'</table>';
    echo htmlspecialchars_decode($table);
    ?>




            <!-- </table> -->
            <!-- .table -->


            <?php

    /**
     * Gets license page URL
     *
     * @return string
     */
    function getLicenseUrl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = 'https://';
        } else {
            $url = 'http://';
        }
        // Append the host(domain name, ip) to the URL.
        $url .= $_SERVER['HTTP_HOST'];

        // Append the requested resource location to the URL
        $url .= $_SERVER['REQUEST_URI'];

        return str_replace('probe.php', 'db-setup', $url);
    }

    /**
     * Checks if user friendly url is on.
     *
     * @internal it curls for pre-license page, if it gets a 404, it returns false.
     * If any exception happens or curl is not found, it returns null
     *
     * @return bool|null
     */
    function checkUserFriendlyUrl()
    {
        if (function_exists('curl_init') === true) {
            try {
                $ch = curl_init(getLicenseUrl());
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                return $httpcode != 404;
            } catch(Exception $e) {
                return null;
            }
        }

        return null;
    }
    ?>

            <!-- table Mod Rewrite block-->
            <table class="t01">
                <tr>
                    <th style="width: 40%;">Mod Rewrite</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Rewrite Engine</td>
                    <?php
            $redirect = function_exists('apache_get_modules') ? (int) in_array('mod_rewrite', apache_get_modules()) : 2;
    $rewriteStatusColor = 'green';
    $rewriteStatusString = 'ON';
    if ($redirect == 2) {
        $rewriteStatusColor = '#F89C0D';
        $rewriteStatusString = 'Unable to detect';
    } elseif (! $redirect) {
        $errorCount += 1;
        $rewriteStatusColor = 'red';
        $rewriteStatusString = 'OFF';
    }
    ?>
                    <td style='color:<?=$rewriteStatusColor; ?>'><?=$rewriteStatusString; ?></td>
                </tr>
                <tr>
                    <td>User friendly URL</td>
                    <?php
        $userFriendlyUrl = checkUserFriendlyUrl();
    if ($userFriendlyUrl === true) {
        $userFriendlyUrlStatusColor = 'green';
        $userFriendlyUrlStatusString = 'ON';
    } elseif ($userFriendlyUrl === false) {
        $errorCount += 1;
        $userFriendlyUrlStatusColor = 'red';
        $userFriendlyUrlStatusString = 'OFF (If you are using apache, make sure <var><strong>AllowOverride</strong></var> is set to <var><strong>All</strong></var> in apache configuration)';
    } else {
        $userFriendlyUrlStatusColor = '#F89C0D';
        $userFriendlyUrlStatusString = 'Unable to detect';
    }
    ?>
                    <td style='color:<?=$userFriendlyUrlStatusColor; ?>'><?=$userFriendlyUrlStatusString; ?></td>
                </tr>

                <?php

                $display = ($errorCount == 0) ? ' <input type="submit" name="submit" id="submitme" class="button-primary button button-large button-next" value="Continue">' : '<button disabled="" class="button button-large" style="float: right;">Continue</button>';
    ?>
                <tfoot>
                <tr>
                    <td style="border: 1px solid #ffffff;">
                        <!-- Adding app version to make it easy to identify app version during troubleshooting client's system. As many of times if code is encoded
                        we are stuck to identify application version and support team needs to get login
                        details or ask client the app version.
                        -->

                    </td>
                    <td style="border: 1px solid #ffffff;">
                   <form action="db-setup" method="post">
                            <input type="hidden" name="count" value="<?php echo $errorCount; ?>" />
                            <p class="setup-actions step"><?= $display ?></p>
                             </form>
                    </td>
                </tr>
                </tfoot>
            </table>
            <!-- .table -->
        </div>
    </div>
    </body>
<?php } ?>
<?php
$footerString = 'Copyright &copy; 2015 - '.date('Y')." . Ladybird Web Solution Pvt Ltd. All rights reserved. Powered by <a target='_blank' href='https://www.faveohelpdesk.com/'>Faveo </a>";

if (isWhiteLabelEnabled()) {
    $footerString = 'Copyright &copy; 2015 - '.date('Y').'. All rights reserved ';
}
?>

<html>
<head>
    <!-- Your head content here -->
</head>
<body>

<!-- Your body content here -->

<footer style="margin-top: 15px;">
    <span style="text-align: center;"><?=$footerString; ?></span>
    <script src='themes/default/plugins/jquery/jquery-3.7.1.min.js' type="text/javascript"></script>
    <script type="text/javascript">
        $('#submitme').click(function() {
            location.reload(true);
        });
    </script>
</footer>

</body>
</html>

