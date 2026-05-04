<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\WelfareCheckin;

class WelfareCheckinSubmitted extends Notification
{
    use Queueable;

    public $checkin;

    public function __construct(WelfareCheckin $checkin)
    {
        $this->checkin = $checkin;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message'        => "{$this->checkin->application->user->name} submitted a welfare check-in for {$this->checkin->application->pet->name}.",
            'application_id' => $this->checkin->application_id,
            'pet_name'       => $this->checkin->application->pet->name,
            'adopter_name'   => $this->checkin->application->user->name,
            'type'           => 'welfare_submitted',
        ];
    }
}