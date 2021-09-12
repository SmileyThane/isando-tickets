<?php

namespace App\Notifications;

use App\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ChangedTicketStatus extends Notification implements ShouldQueue
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
        $ticket = Ticket::find($this->ticket_id);

        if ($this->language == 'en') {
            $ticket = Ticket::find($this->ticket_id);
            if ($ticket && $ticket->status_id === 5) {
                $subject = 'Updates on your ticket: ' . $this->ticket_subject;
                $firstLine = "Your ticket has been successfully closed.
             We hope that you have been satisfied with the resolution of the ticket and the speed of response. ";
                $secondLine = "";
            } else {
                $subject = 'Updates on your ticket: ' . $this->ticket_subject;
                $firstLine = "Your ticket has been updated.";
                $secondLine = "Or respond directly to this email.
              Please do not copy this message in your email response, and do not change subject of this email.
              This may cause delays in processing as your response may not be correctly assigned to your ticket.";
            }

            Log::info('email sending was started!');
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject($subject)
                ->line('Hello ' . $this->name . ',')
                ->line($firstLine)
                ->action('View online', env('APP_URL') . '/ticket/' . $this->ticket_id)
                ->line($secondLine)
                ->line('Have a great day ahead!')
                ->salutation('Regards, ' . $this->from);
        } else {
            if ($ticket && $ticket->status_id === 5) {
                $subject = 'Updates auf Ihrem Ticket: ' . $this->ticket_subject;
                $firstLine = "Ihr Ticket wurde erfolgreich abgeschlossen.
                Wir hoffen, dass Sie mit der Abwicklung des Tickets und der Reaktionszeit zufrieden waren.";
                $secondLine = "";
            } else {
                $subject = 'Updates auf Ihrem Ticket: ' . $this->ticket_subject;
                $firstLine = "Ihr Ticket wurde aktualisiert.";
                $secondLine = "Oder antworten Sie direkt auf diese E-Mail.
               Bitte kopieren Sie diese Nachricht nicht in Ihre E-Mail-Antwort und ändern Sie den Betreff dieser E-Mail nicht.
               Dies kann zu Verzögerungen bei der Verarbeitung führen, da Ihre Antwort Ihrem Ticket möglicherweise nicht korrekt zugeordnet ist.";
            }

            Log::info('email sending was started!');
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject($subject)
                ->line('Hallo '. $this->name . ',')
                ->line($firstLine)
                ->action('Online ansehen', env('APP_URL') . '/ticket/' . $this->ticket_id)
                ->line($secondLine)
                ->line('Wir wünschen Ihnen einen schönen Tag!')
                ->salutation('Freundliche Grüsse, ' . $this->from);
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
