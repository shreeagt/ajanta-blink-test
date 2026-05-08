<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['emp_code', 'name', 'hq', 'password', 'language', 'dm_name', 'rsm_name', 'state', 'role'];
}
