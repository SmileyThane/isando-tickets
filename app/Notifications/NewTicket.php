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

    protected $title;
    protected $name;
    protected $ticket_subject;
    protected $ticket_id;
    protected $from;
    protected $language;

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
    public function __construct($from, $title, $name, $ticket_subject, $ticket_id, $language = 'en')
    {
        $this->from = $from;
        $this->title = $title;
        $this->name = $name;
        $this->ticket_subject = $ticket_subject;
        $this->ticket_id = $ticket_id;
        $this->language = $language;
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
        if ($this->language == 'en') {
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject('New ticket for you!')
                ->line('Hello ' . $this->name . ',')
                ->line('You have a new ticket on your profile:' . $this->ticket_subject)
                ->action('View online', env('APP_URL') . '/ticket/' . $this->ticket_id)
                ->line('Or respond directly to this email.')
                ->line('Please do not copy this message in your email response, and do not change subject of this email.
            This may cause delays in processing as your response may not be correctly assigned to your ticket.')
                ->line('Have a great day ahead!')
                ->salutation('Regards, ' . $this->from);
        } else {
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject('Neues Ticket für Sie!')
                ->line('Hallo ' . $this->name . ',')
                ->line('Sie haben ein neues Ticket in Ihrem Profil: ' . $this->ticket_subject)
                ->action('Online ansehen:', env('APP_URL') . '/ticket/' . $this->ticket_id)
                ->line('oder antworten Sie direkt auf diese E-Mail.')
                ->line('Bitte kopieren Sie diese Nachricht nicht in Ihre E-Mail-Antwort und ändern Sie den Betreff dieser E-Mail nicht.
                Dies kann zu Verzögerungen bei der Bearbeitung führen, da Ihre Antwort möglicherweise nicht korrekt Ihrem Ticket zugewiesen wird.')
                ->line('Wir wünschen Ihnen einen schönen Tag!')
                ->salutation('Freundliche Grüsse, ' . $this->from)
                ->line('')
                ->line('Wenn Sie Probleme beim Klicken auf den "Link zu unserem Ticketing-System" haben:
                Kopieren Sie die untenstehende URL und fügen Sie diese in Ihren Webbrowser ein: ' . env('APP_URL') . '.');
        }
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
