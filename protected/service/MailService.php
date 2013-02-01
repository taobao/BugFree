<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of MailService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class MailService
{

    /**
     * Enhanced mail function.
     *
     * @author                            Chunsheng Wang <wwccss@263.net>
     * @param   string      $toList       To address list.
     * @param   string      $ccList       CC address list.
     * @param   string      $subject      Subject.
     * @param   string      $message      Message.
     */
    public static function sysMail($toList, $ccList, $subject, $message)
    {
        if(CommonService::$TrueFalseStatus['TRUE'] != Yii::app()->params->mail['on'] )
        {
            return;
        }
        // Create an object of PHPMailer class and set the send method

        $mailInfo = Yii::createComponent('application.extensions.mailer.EMailer');
        //$mailInfo = new PHPMailer();
        switch(Yii::app()->params->mail['send_method'])
        {
            case "SMTP":
                $mailInfo->isSMTP();
                $mailInfo->Host = Yii::app()->params->mail['send_params']['host'];
                $mailInfo->SMTPAuth = Yii::app()->params->mail['send_params']["smtp_auth"];
                $mailInfo->Username = Yii::app()->params->mail['send_params']["username"];
                $mailInfo->Password = Yii::app()->params->mail['send_params']["password"];
                break;
            case "MAIL":
                $mailInfo->isMail();
                break;
            case "SENDMAIL":
                $mailInfo->isSendmail();
                break;
            case "QMAIL":
                $mailInfo->isQmail();
                break;
        }

        // Define From Address.
        $mailInfo->From = Yii::app()->params->mail['from_address'];
        $mailInfo->FromName = Yii::app()->params->mail['from_name'];

        if(!is_array($toList) && $toList != '')
        {
            $toList = explode(',', $toList);
        }
        if(empty($toList) && is_array($ccList) && !empty($ccList))
        {
            $toList[] = array_pop($ccList);
        }
        if(empty($toList))
        {
            return false;
        }
        foreach($toList as $to)
        {
            $mailInfo->addAddress($to);
        }
        // Add To Address.
        if(is_array($ccList))
        {
            $ccList = array_diff($ccList, $toList);
            $ccList = array_unique($ccList);
            foreach($ccList as $CC)
            {
                $mailInfo->addCC($CC);
            }
        }
        // Add Subject.
        $mailInfo->Subject= "=?UTF-8?B?".base64_encode(stripslashes($subject))."?=";
        // Set Body.
        $mailInfo->IsHTML(true);
        $mailInfo->CharSet = 'UTF-8';
        $mailInfo->Body = $message;
        if(!$mailInfo->Send())
        {
            Yii::log('mail send failed:' . json_encode($mailInfo->ErrorInfo), 'error', 'bugfree.ProductService');
        }
    }

}

?>
