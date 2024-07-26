<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Manages email sending
 */
class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends an email using a template with provided context.
     *
     * @param mixed $from     the email address of the sender
     * @param mixed $to       the email address of the recipient
     * @param mixed $subject  the subject of the email
     * @param mixed $template The name of the email template (without ".html.twig" extension).
     * @param mixed $context  the context data to be passed to the email template
     */
    public function send(string $from, string $to, string $subject, string $template, array $context): void
    {
        // Create a new email instance with the provided details
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // Send the email
        $this->mailer->send($email);
    }
}
