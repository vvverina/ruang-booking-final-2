<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewBookingCreated extends Notification {
    use Queueable;
    public $booking;

    public function __construct($booking) {
        $this->booking = $booking;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Booking Baru Diajukan')
            ->line('User ' . $this->booking->user->name . ' mengajukan booking:')
            ->line('Tanggal: ' . $this->booking->booking_date)
            ->line('Ruangan: ' . $this->booking->room->name);
    }
}
