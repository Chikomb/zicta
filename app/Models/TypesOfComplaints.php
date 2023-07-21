<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypesOfComplaints extends Model
{
    use HasFactory;
    protected $fillable = [ 'complaint_type', 'complaint_description'
];
}
