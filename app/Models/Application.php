<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'status',
        'adopter_address',
        'contact_number',
        'adopter_message',
        'reviewed_by'
    ];

    // The person applying
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The pet they want
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    // The staff member who reviewed it
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}