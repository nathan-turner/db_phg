<?php

class Core_Controller_Email extends SpcController {

    public function __construct() {
        parent::__construct();
    }

    public function send($from, $to, $subject, $msg, $headers = '', $attachement = null) {
        require_once SPC_SYSPATH . '/libs/swift-mailer/lib/swift_required.php';
        if (USE_PHP_MAILER) {
            $swiftTransporter = Swift_SmtpTransport::newInstance(PHP_MAILER_HOST, PHP_MAILER_PORT, PHP_MAILER_SECURITY)
                                ->setUsername(PHP_MAILER_USERNAME)
                                ->setPassword(PHP_MAILER_PASSWORD);

            $swiftMailer = Swift_Mailer::newInstance($swiftTransporter);
        //use PHP native mail function
        } else {
            $swiftTransporter = Swift_MailTransport::newInstance();
            $swiftMailer = Swift_Mailer::newInstance($swiftTransporter);
        }

        $swiftMessage = Swift_Message::newInstance($subject)
                            ->setReplyTo(array($from))
                            ->setFrom(array($from => $from))
                            ->setTo(array($to))
                            ->setBody($msg, 'text/html');
        if ($attachement) {
            if (!isset($attachement['mime'])) {
                $attachement['mime'] = 'text/plain';
            }
            $swiftAttachment = Swift_Attachment::fromPath($attachement['path'], $attachement['mime']);
            $swiftMessage->attach($swiftAttachment);
        }

        $swiftMailer->send($swiftMessage);

        //unlink attachement file after sent
        if (isset($attachement['unlink'])) {
            unlink($attachement['path']);
        }
    }
}