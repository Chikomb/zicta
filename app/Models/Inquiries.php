<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiries extends Model
{
    use HasFactory;
    protected $fillable = [ 
        'inquiry_number',
        'description',
        'session_id',
        'status',
    ];
}
