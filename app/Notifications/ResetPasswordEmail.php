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
                ->greeting(' ')
                ->subject('Your password was restored!')
                ->line('Dear ' . $this->name . ', ')
                ->line("Welcome back. Your account has been restored.")
                ->line("Please use your new password to log into your account:")
                ->line('Your login name: ' . $this->email)
                ->line('Your password: ' . $this->password)
                ->action('Link to INAX online support ', env('APP_URL'))
                ->line('Have a good day!')
                ->line('')
                ->salutation('- Best Wishes, ' . $this->from);
        } else {
            return (new MailMessage)
                ->from(Config::get('mail.from.address'), $this->from)
                ->greeting(' ')
                ->subject('Ihr Passwort wurde wiederhergestellt!')
                ->line('Guten Tag ' . $this->name . ',')
                ->line("Willkommen zurück beim INAX Online Support. Ihr Passwort wurde wiederhergestellt.")
                ->line("Bitte verwenden Sie Ihr neues Passwort, um sich in Ihr Konto anzumelden:")
                ->line('Ihr Login-Name: ' . $this->email)
                ->line('Ihr Passwort: ' . $this->password)
                ->action('Link zum INAX Online Support ', env('APP_URL'))
                ->line('Wir wünschen Ihnen einen schönen Tag!')
                ->line('')
                ->salutation('Freundliche Grüße, ' . $this->from);
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
