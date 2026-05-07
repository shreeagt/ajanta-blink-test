<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvsScreening extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_code',
        'blink_test_id',
        'symptom_data',
        'total_score',
        'has_cvs'
    ];

    protected $casts = [
        'symptom_data' => 'array'
    ];
}
