<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'patient_name',
        'dob',
        'symptoms',
        'evaluation',
        'bp',
        'oxygen',
        'observation',
    ];
}
