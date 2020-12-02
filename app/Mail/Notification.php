<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array|string $recipients
     * @param string $subject
     * @param string $body
     * @param arraty $attachments
     * @return void
     */
    public function __construct($recipients, $subject, $body, $attachments = [])
    {
        $this->subject($subject);
        $this->html($body);
        $this->attachments = $attachments;

        foreach ($attachments as $file) {
            $this->attachData($file['data'], $file['name']);
        }

        if (is_string($recipients) && strpos($recipients, ',') !== false) {
            $recipients = explode(',', $recipients);
        }
        foreach ($recipients as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->bcc($email);
            }
        }
    }

}
