<?php

namespace Controllers;

require ROOT . '/libs/PHPMailer/Exception.php';
require ROOT . '/libs/PHPMailer/PHPMailer.php';
require ROOT . '/libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public function __construct(string|array $to, string $subject, string $content, string $link = null, string $linkName = 'Click here', string $attachmentFilePath = null)
    {
        if (empty($to)) {
            throw new \Exception('Error: mail recipient cannot be empty');
        }
        if (empty($subject)) {
            throw new \Exception('Error: mail subject cannot be empty');
        }
        if (empty($content)) {
            throw new \Exception('Error: mail message cannot be empty');
        }

        if (is_array($to)) {
            $to = implode(',', $to);
        }

        /**
         *  HTML message template
         *  Powered by stripo.email
         */
        ob_start();
        include(ROOT . '/templates/mail/mail.template.html.php');
        $template = ob_get_clean();

        /**
         *  PHPMailer
         */
        $mail = new PHPMailer(true);

        try {
            // Recipients
            $mail->setFrom('noreply@' . WWW_HOSTNAME, 'Motion-UI');
            $mail->addAddress($to);
            $mail->addReplyTo('noreply@' . WWW_HOSTNAME, 'Motion-UI');

            // Attachments
            if (!empty($attachmentFilePath)) {
                $mail->addAttachment($attachmentFilePath);
            }

            // Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $template;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
