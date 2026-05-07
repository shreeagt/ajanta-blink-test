<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlinkTest extends Model
{
    use HasFactory;
    
    protected $fillable = ['emp_code', 'blink_count'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_code', 'emp_code');
    }

    public function cvs()
    {
        return $this->hasOne(CvsScreening::class, 'blink_test_id');
    }
}
