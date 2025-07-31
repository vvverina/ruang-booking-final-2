<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingStatusChanged extends Notification {
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
            ->subject('Status Booking Anda Berubah')
            ->line('Status booking ruangan Anda telah berubah menjadi: ' . ucfirst($this->booking->status))
            ->line('Tanggal: ' . $this->booking->booking_date)
            ->line('Ruangan: ' . $this->booking->room->name);
    }
}
