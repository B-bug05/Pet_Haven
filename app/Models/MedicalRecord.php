<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id', 'pet_id', 'status', 'adopter_address', 
        'contact_number', 'adopter_message', 'reviewed_by'
    ];

    public function adopter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
