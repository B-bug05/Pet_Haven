<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class NewApplicationSubmitted extends Notification
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
            'application_id' => $this->application->id,
            'pet_id'         => $this->application->pet->id,
            'pet_name'       => $this->application->pet->name,
            'adopter_name'   => $this->application->user->name,
            'message'        => "{$this->application->user->name} submitted an adoption application for {$this->application->pet->name}.",
        ];
    }
}