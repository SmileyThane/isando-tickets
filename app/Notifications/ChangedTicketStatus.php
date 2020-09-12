<?php

namespace App\Notifications;

use App\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ChangedTicketStatus extends Notification
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
        $ticket = Ticket::find($this->ticket_id);
        if ($ticket && $ticket->status_id === 5) {
            $subject = 'Updates on your ticket: ' . $this->ticket_subject;
            $firstLine = "Your ticket has been successfully closed.
             We hope that you have been satisfied with the resolution of the ticket and the speed of response. ";
            $secondLine = "";
        } else {
            $subject = 'Updates on your ticket: ' . $this->ticket_subject;
            $firstLine = "Your ticket has been updated.";
            $secondLine = "or respond directly to this email.
              Please do not copy this message in your email response, and do not change subject of this email.
              This may cause delays in processing as your response may not be correctly assigned to your ticket.";
        }

        Log::info('email sending was started!');
        return (new MailMessage)
            ->from(Config::get('mail.from.address'), $this->from)
            ->subject($subject)
            ->line('Hello, ' . $this->name)
            ->line($firstLine)
            ->action('View online', env('APP_URL') . '/ticket/' . $this->ticket_id)
            ->line($secondLine)
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
