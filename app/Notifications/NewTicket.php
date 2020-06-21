<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewTicket extends Notification
{
    use Queueable;


    protected $name;
    protected $ticket_subject;
    protected $ticket_id;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $ticket_subject
     * @param $ticket_id
     */
    public function __construct($name, $ticket_subject, $ticket_id)
    {
        $this->name = $name;
        $this->ticket_subject = $ticket_subject;
        $this->ticket_id = $ticket_id;
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
            ->line('Hello, ' . $this->name)
            ->line('We are have new ticket for you!')
            ->line('Ticket subject is ' . $this->ticket_subject)
            ->action('Go to the ticket', env('APP_URL') . '/ticket/' . $this->ticket_id)
            ->line('Thank you for using our application!');
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
