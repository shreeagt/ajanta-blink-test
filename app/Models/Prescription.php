<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['so_id', 'doctor_name', 'mobile_number', 'clinic_name', 'speciality', 'prescription_count', 'images', 'visit_date'];

    protected $casts = [
        'images' => 'array',
    ];
    use HasFactory;
}
