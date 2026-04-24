<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    public $application;

    // We pass the specific application into the notification when we trigger it
    public function __construct($application)
    {
        $this->application = $application;
    }

    // Tell Laravel we ONLY want to save this to the database (no emails for now)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // This is the exact data that gets saved to the database to display to the user
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'pet_id' => $this->application->pet->id,
            'pet_name' => $this->application->pet->name,
            'new_status' => $this->application->status,
            'message' => "Your application for {$this->application->pet->name} was updated to: {$this->application->status}"
        ];
    }
}