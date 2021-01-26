<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ResetPasswordEmail extends Notification
{
    use Queueable;

    protected $title;
    protected $name;
    protected $role;
    protected $email;
    protected $password;
    protected $from;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param $from
     * @param $title
     * @param $name
     * @param $role
     * @param $email
     * @param $password
     * @param $language
     */
    public function __construct($from, $title, $name, $role, $email, $password, $language = 'en')
    {
        $this->from = $from;
        $this->title = $title;
        $this->name = $name;
        $this->role = $role;
        $this->email = $email;
        $this->password = $password;
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
                ->subject('Your password was restored at the ticketing system!')
                ->line('Dear ' . $this->name . ', ')
                ->line("Welcome back to our ticketing system. Your account has been restored.")
                ->line("Please use your new password to log into your account:")
                ->line('Your login name: ' . $this->email)
                ->line('Your password: ' . $this->password)
                ->action('Link to our ticketing system: ', env('APP_URL'))
                ->line('Have a Good Day!')
                ->line('')
                ->salutation('- Best Wishes, INAX AG' . $this->from);
        } else {
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject('Sie wurden zum Ticketsystem eingeladen!')
                ->greeting(' ')
                ->line('Hallo '. $this->name . ',')
                ->line("Willkommen zurück zu unserem Ticketing-System der INAX AG. Ihr Konto wurde wiederhergestellt.")
                ->line("Bitte benutzen Sie Ihr neues Passwort, um sich in Ihr Konto anzumelden:")
                ->line('Ihr Login-Name: ' . $this->email)
                ->line('Ihr Passwort: ' . $this->password)
                ->action('Link zu unserem Ticketing-System: ', env('APP_URL'))
                ->line('Wir wünschen Ihnen einen schönen Tag!')
                ->line('')
                ->salutation('Freundliche Grüsse, , INAX AG');
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
