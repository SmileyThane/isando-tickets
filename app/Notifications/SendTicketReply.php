<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class SendTicketReply extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $attachments;

    /**
     * Create a new notification instance.
     *
     * @param $from
     * @param $title
     * @param $name
     * @param $ticket_subject
     * @param $ticket_id
     * @param $language
     */
    public function __construct($subject, $message, $attachments)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->attachments = $attachments;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->from(Config::get('mail.from.address'))
            ->subject($this->subject)
            ->bcc(Config::get('mail.bcc.address'));

        foreach ($this->attachments as $attachment) {
            $message->attach($attachment);
        }

        $message->view('ticket_reply', ['reply' => $this->message]);
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
