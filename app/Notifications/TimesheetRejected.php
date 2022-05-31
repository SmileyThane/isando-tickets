<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class TimesheetRejected extends Notification
{
    use Queueable;

    private $timesheet = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($timesheet)
    {
        $this->timesheet = $timesheet;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->from(Config::get('mail.from.address'))
            ->subject('Your request was rejected.')
            ->line("Your request was rejected by {$this->timesheet->approver->full_name} at {$this->timesheet->submitted_on}");
        if ($this->timesheet->note)
            $mail->line("with notice: {$this->timesheet->note}");

        return $mail->action('Log in to the system', url('/tracking/timesheet'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
