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


    protected $name;
    protected $role;
    protected $email;
    protected $password;
    protected $from;
    protected $language;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $role
     * @param $email
     * @param $password
     * @param $language
     */
    public function __construct($from, $name, $role, $email, $password, $language = 'en')
    {
        $this->from = $from;
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
                ->line("Welcome back to our $this->from ticketing system. Your account has been restored.")
                ->line("Please use your new password to log into your account:")
                ->line('Your login name:' . $this->email)
                ->line('Your password: ' . $this->password)
                ->action('Link to our ticketing system: ', env('APP_URL'))
                ->line('Have a Good Day!')
                ->line('')
                ->salutation('- Best Wishes, ' . $this->from);
        } else {
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->subject('Ihr Passwort wurde im Ticketsystem wiederhergestellt!')
                ->line('Lieber ' . $this->name . ', ')
                ->line("Willkommen zurück in unserem $this->from Ticketingsystem. Ihr Konto wurde wiederhergestellt.")
                ->line("Bitte verwenden Sie Ihr neues Passwort, um sich in Ihr Konto einzuloggen: ")
                ->line('Login:' . $this->email)
                ->line('Passwort: ' . $this->password)
                ->action('Link zu unserem Ticketsystem: ', env('APP_URL'))
                ->line('Wir wünschen Ihnen einen schönen Tag!')
                ->line('')
                ->salutation('- Best Wishes, ' . $this->from);
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
