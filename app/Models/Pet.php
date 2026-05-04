<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
    'name', 
    'type', 
    'breed', 
    'age', 
    'description', 
    'image', 
    'status', // <--- THIS MUST BE IN THE ARRAY
    'health_summary'
];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function applications()
    {
        // Make sure the namespace is exactly like this:
        return $this->hasMany(\App\Models\Application::class);
    }

    public function photos()
    {
        return $this->hasMany(PetPhoto::class);
    }
}