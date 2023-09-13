<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UssdSessions extends Model
{
    use HasFactory;
    protected $fillable= [
        'phone_number',
        'case_no',
        'step_no',
        'session_id',
        'sub_case_no'
    ];
}
