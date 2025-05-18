<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CaseCreatedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Choose how the notification will be delivered
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // This will be saved in the notifications table
    public function toArray($notifiable)
    {
        return [
            'author'   => $this->data['author'],
            'category' => $this->data['category'],
            'message'  => 'A new case has been created.',
        ];
    }

    // This will be broadcast via Pusher
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => [
                'author'   => $this->data['author'],
                'category' => $this->data['category'],
                'message'  => 'A new case has been created.',
            ]
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\Channel('notification');
    }

    public function broadcastAs()
    {
        return 'test.notification';
    }
}
