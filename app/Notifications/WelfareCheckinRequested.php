<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class WelfareCheckinRequested extends Notification
{
    use Queueable;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message'        => "PetHaven has requested a welfare check-in for {$this->application->pet->name}. Please log in and submit an update!",
            'application_id' => $this->application->id,
            'pet_name'       => $this->application->pet->name,
            'type'           => 'welfare_request',
        ];
    }
}