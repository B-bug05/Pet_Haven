<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['name', 'type', 'breed', 'age', 'description', 'image_path', 'status', 'health_summary'];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}