<?php
/**
 * Common utilites for the application
 */
use Phalcon\DI\FactoryDefault as PhDi,
    Phalcon\Exception as PhException;

class Basics {

    /**
    * Prints out debug information about given variable.
    * Referenced from cakePHP
    * Only runs if application.resource.debug = true
    * 
    * @access public
    * @param boolean $var Variable to show debug information for.
    * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
    * @param boolean $showFrom If set to true, the method prints from where the function was called.
    */
    public static function debug( $var = false, $showHtml = false, $showFrom = true ) {
    
        $di         = PhDi::getDefault();
        $config     = $di['config'];
    
        if ('1' == $config->application->debug) {
            if ($showFrom) {
                $calledFrom = debug_backtrace();
                echo '<strong>' . substr(str_replace(ROOT_PATH, '', $calledFrom[0]['file']), 1) . '</strong>';
                echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
            }
            echo "\n<pre class=\"debug\">\n";

            $var = print_r($var, true);
            if ($showHtml) {
                $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
            }
            echo $var . "\n</pre>\n";
        }
    }

    /**
    * Send email using Mandrill Service
    * Options (type, subject, toEmail, toName)
    */
    public static function sendEmail( $options = array() ) {

        $di         = PhDi::getDefault();
        $config     = $di['config'];
        $logger     = $di['logger'];

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp1.example.com';
        $mail->Port = '587';
        $mail->SMTPAuth = true;
        $mail->Username = 'user@example.com';
        $mail->Password = 'secret';

        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress($options['toEmail'], $options['toName']);

        switch($options['type']) {
            case 'reset':
                $mail->Subject = 'Password reset notification';
                $message =<<<EOT
<h3>Hi, {$options['toName']}</h3>
<p class="lead">Forgot your password?</p>
<p class="callout">Please click the following link to start password reset process. <a href="{$options['resetUrl']}" target="_blank">Click here! &raquo;</a>.</p>
<p>This link will expire {$config->application->hashTokenExpiryHours} hours after this email was sent.</p>
<p><br>Thank you!</p>
EOT;
                $altBody =<<<EOT
Hi, {$options['toName']}\n\n
You recently requested to reset your password.\n\n
Please click the following link to start password reset process. \n{$options['resetUrl']}\n\n
This link will expire {$config->application->hashTokenExpiryHours} hours after this email was sent.\n\n
Thank you! \n\n
EOT;
            break;
            case 'resetConfirm':
                $mail->Subject = 'Your password has been reset';
                $message =<<<EOT
<h3>Hi, {$options['toName']}</h3>
<p class="lead">The password has been successfully reset.</p>
<p><br>Thank you!</p>
EOT;
                $altBody =<<<EOT
Hi, {$options['toName']}\n\n
The password has been successfully reset.\n\n
Thank you! \n\n
EOT;
            break;
            case 'newUser':
                $mail->Subject = 'Your new account has been created';
                $message =<<<EOT
<h3>Hi, {$options['toName']}</h3>
<p class="lead">This is to confirm creation of your new account.</p>
<p class="callout">Your temporary password is <b>{$options['tempPassword']}</b>. <a href="{$options['welcomeUrl']}" target="_blank">Click here! &raquo;</a> to access your new account.</p>
<p><br>Thank you!</p>
EOT;
                $altBody =<<<EOT
Hi, {$options['toName']}\n\n
This is to confirm creation of your new account.\n\n
Your temporary password is <b>{$options['tempPassword']}</b>. Click this link {$options['welcomeUrl']} to access your new account.\n\n
Thank you! \n\n
EOT;
            break;
        }

        $content = Basics::emailTemplate(array(
            'messageBlock'      => $message,
            'baseUrl'           => $config->application->baseUrl
        ));

        $mail->msgHTML($content);
        $mail->AltBody = $altBody;
        
        //send the message, check for errors
        if ( !$mail->send() ) {
            $logger->log("PHPMailer Error: " . $mail->ErrorInfo, \Phalcon\Logger::ERROR);
        } else {
            $logger->log("Reset email send to: " . $options['toEmail'], \Phalcon\Logger::INFO);
        }

    }

    public static function emailTemplate ( $options = array() ) {

        return <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Basic</title>
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<style>
*{margin:0;padding:0}*{font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif}img{max-width:100%}.collapse{margin:0;padding:0}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100% !important;height:100%}a{color:#2ba6cb} .btn{display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: normal;line-height: 1.428571429;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;border: 1px solid transparent;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;color: #333;background-color: white;border-color: #CCC;} p.callout{padding:15px;background-color:#ecf8ff;margin-bottom:15px}.callout a{font-weight:bold;color:#2ba6cb}table.social{background-color:#ebebeb}.social .soc-btn{padding:3px 7px;border-radius:2px; -webkit-border-radius:2px; -moz-border-radius:2px; font-size:12px;margin-bottom:10px;text-decoration:none;color:#FFF;font-weight:bold;display:block;text-align:center}a.fb{background-color:#3b5998 !important}a.tw{background-color:#1daced !important}a.gp{background-color:#db4a39 !important}a.ms{background-color:#000 !important}.sidebar .soc-btn{display:block;width:100%}table.head-wrap{background: black; color: #FFF; font-size: 25px; font-weight: bold;width:100%}.header.container table td {font-family: "PT Sans", 'Lucida Grande', 'Lucida Sans', Verdana, sans-serif;}.header.container table td.label{padding:15px;padding-left:0}table.body-wrap{width:100%}table.footer-wrap{width:100%;clear:both !important}.footer-wrap .container td.content p{border-top:1px solid #d7d7d7;padding-top:15px}.footer-wrap .container td.content p{font-size:10px;font-weight:bold}h1,h2,h3,h4,h5,h6{font-family:"HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;line-height:1.1;margin-bottom:15px;color:#000}h1 small,h2 small,h3 small,h4 small,h5 small,h6 small{font-size:60%;color:#6f6f6f;line-height:0;text-transform:none}h1{font-weight:200;font-size:44px}h2{font-weight:200;font-size:37px}h3{font-weight:500;font-size:27px}h4{font-weight:500;font-size:23px}h5{font-weight:900;font-size:17px}h6{font-weight:900;font-size:14px;text-transform:uppercase;color:#444}.collapse{margin:0 !important}p,ul{margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6}p.lead{font-size:17px}p.last{margin-bottom:0}ul li{margin-left:5px;list-style-position:inside}ul.sidebar{background:#ebebeb;display:block;list-style-type:none}ul.sidebar li{display:block;margin:0}ul.sidebar li a{text-decoration:none;color:#666;padding:10px 16px;margin-right:10px;cursor:pointer;border-bottom:1px solid #777;border-top:1px solid #fff;display:block;margin:0}ul.sidebar li a.last{border-bottom-width:0}ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p{margin-bottom:0 !important}.container{display:block !important;max-width:600px !important;margin:0 auto !important;clear:both !important}.content{padding:15px;max-width:600px;margin:0 auto;display:block}.content table{width:100%}.column{width:300px;float:left}.column tr td{padding:15px}.column-wrap{padding:0 !important;margin:0 auto;max-width:600px !important}.column table{width:100%}.social .column{width:280px;min-width:279px;float:left}.clear{display:block;clear:both}@media only screen and (max-width:600px){a[class="btn"]{display:block !important;margin-bottom:10px !important;background-image:none !important;margin-right:0 !important}div[class="column"]{width:auto !important;float:none !important}table.social div[class="column"]{width:auto !important}}
</style>

</head>
 
<body bgcolor="#FFFFFF">

<!-- HEADER -->
<table class="head-wrap">
    <tr>
        <td></td>
        <td class="header container" >
                
                <div class="content">
                    <table>
                        <tr>
                            <td>Example</td>
                        </tr>
                    </table>
                </div>
                
        </td>
        <td></td>
    </tr>
</table><!-- /HEADER -->

<!-- BODY -->
<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <div class="content">
            <table>
                <tr>
                    <td>
                        {$options['messageBlock']}
                    </td>
                </tr>
            </table>
            </div><!-- /content -->
        </td>
        <td></td>
    </tr>
</table><!-- /BODY -->

</body>
</html>

EOT;

    }
}
