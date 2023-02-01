<?php

namespace App\Notifications;

use App\Domains\Loans\Models\LoanInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LateFeesGenerated extends Notification
{
    use Queueable;

    protected $repaymentCount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($repaymentCount)
    {
      $this->repaymentCount = $repaymentCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
          'action' => "/loans/repayments?filter[status]=" . LoanInstallment::STATUS_LATE,
          'message' => "Se han generado $this->repaymentCount moras de pagos de prestamos",
          'data' => []
        ];
    }
}
