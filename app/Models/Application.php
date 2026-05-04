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
        'reviewed_by',
        'has_other_pets',
        'housing_type',
        'landlord_allows_pets',
        'hours_alone',
        'has_outdoor_space',
        'previous_pet_experience',
        'why_this_pet',
    ];

    protected $with = ['user', 'pet', 'welfareCheckins'];

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

    public function welfareCheckins()
    {
        return $this->hasMany(WelfareCheckin::class);
    }
}