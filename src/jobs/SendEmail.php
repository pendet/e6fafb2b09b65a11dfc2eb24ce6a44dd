<?php

namespace App\Jobs;

use Exception;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class SendEmail
{
    public static function handle(array $payload)
    {
        $transport = Transport::fromDsn($_ENV['MAIL_DSN']);
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from($payload['from'])
            ->to($payload['to'])
            ->subject($payload['subject'])
            ->text($payload['body'])
            ->priority(Email::PRIORITY_HIGHEST);

        if (isset($payload['cc'])) {
            $email->cc($payload['cc']);
        }

        if (isset($payload['bcc'])) {
            $email->bcc($payload['bcc']);
        }

        try {
            $mailer->send($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
