<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class NewTicket extends Notification
{
    use Queueable;


    protected $name;
    protected $ticket_subject;
    protected $ticket_id;
    protected $from;

    /**
     * Create a new notification instance.
     *
     * @param $from
     * @param $name
     * @param $ticket_subject
     * @param $ticket_id
     */
    public function __construct($from, $name, $ticket_subject, $ticket_id)
    {
        $this->name = $name;
        $this->ticket_subject = $ticket_subject;
        $this->ticket_id = $ticket_id;
        $this->from = $from;
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
        Log::info('email sending was started!');
        return (new MailMessage)
            ->from(Config::get('mail.from.address'), $this->from)
            ->subject('New ticket for you!')
            ->line('Hello ' . $this->name . ',')
            ->line('You have a new ticket on your profile:' . $this->ticket_subject)
            ->action('View online' , env('APP_URL') . '/ticket/' . $this->ticket_id)
            ->line('Or respond directly to this email.')
            ->line('Please do not copy this message in your email response, and do not change subject of this email.
            This may cause delays in processing as your response may not be correctly assigned to your ticket.')
            ->line('Have a great day ahead!')
            ->salutation('Regards, ' . $this->from);
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
