<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RegularInviteEmail extends Notification
{
    use Queueable;


    protected $name;
    protected $role;
    protected $email;
    protected $password;
    protected $from;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $role
     * @param $email
     * @param $password
     */
    public function __construct($from, $name, $role, $email, $password)
    {
        $this->from = $from;
        $this->name = $name;
        $this->role = $role;
        $this->email = $email;
        $this->password = $password;
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
            ->subject('You have been invited to the ticketing system!')
            ->line('Dear ' . $this->name . ', ')
            ->line("Welcome to our $this->from ticketing system. Your account has been created.")
            ->line("Please use below credentials to log into your account:")
            ->line('Your login name:' . $this->email)
            ->line('Your password: ' . $this->password)
            ->action('Link to our ticketing system: ', env('APP_URL'))
            ->line('Have a Good Day!')
            ->line('')
            ->salutation('- Best Wishes, ' . $this->from);
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
