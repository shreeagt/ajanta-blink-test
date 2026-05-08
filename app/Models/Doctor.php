<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'speciality',
        'mobile',
        'city',
        'emp_code'
    ];

    public function blinkTests()
    {
        return $this->hasMany(BlinkTest::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_code', 'emp_code');
    }
}
