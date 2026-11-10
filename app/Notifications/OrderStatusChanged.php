<?php

namespace App\Notifications;

use App\Models\Vendor\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public $transaksi;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = [
            'pending' => 'Order Received',
            'processing' => 'In Production',
            'quality_check' => 'Quality Check',
            'completed' => 'Ready for Pickup',
            'cancelled' => 'Order Cancelled'  // Added cancel message
        ][$this->transaksi->status];

        return (new MailMessage)
            ->subject("Order Status Update - {$this->transaksi->kode}")
            ->line("Your order {$this->transaksi->kode} status has been updated to: {$status}")
            ->line("Current Progress: {$this->transaksi->progress_percentage}%")
            ->line("Estimated completion: {$this->transaksi->estimasi_selesai}")
            ->line('Thank you for your business!');
            
        // return (new MailMessage)->markdown('order_status', ['transaksi' => $this->transaksi]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
