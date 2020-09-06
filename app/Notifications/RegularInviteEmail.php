<?php

namespace App\Notifications;

use App\Role;
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
            ->line('Hello, ' . $this->name)
            ->line('You has been attached to ticketing system as ' . Role::find($this->role)->name)
            ->line('Your login is ' . $this->email)
            ->line('Your password is ' . $this->password)
            ->action('Start using system', env('APP_URL'))
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
