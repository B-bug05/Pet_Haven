<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetPhoto extends Model
{
    protected $fillable = ['pet_id', 'image', 'caption'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}