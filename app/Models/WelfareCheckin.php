<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelfareCheckin extends Model
{
    protected $fillable = [
        'application_id',
        'requested_by',
        'submitted_by',
        'status',
        'message',
        'photo',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}