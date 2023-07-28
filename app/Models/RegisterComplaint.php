<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterComplaint extends Model
{
    use HasFactory;
    protected $fillable = ['complaint_number', 'description','status', 'session_id'];

    // Validation rules
    public static $rules = [
        'description' => 'required|string|max:255',
        // Add any other validation rules you need
    ];

    // Generate the complaint number
    public static function generateComplaintNumber()
    {
        return 'CMP-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
    }

    // Save the complaint with a unique complaint number
    public static function saveComplaint($description, $session_id)
    {
        $complaint_number = self::generateComplaintNumber();
        $complaint = new self([
            'complaint_number' => $complaint_number,
            'description' => $description,
            'session_id' => $session_id,
        ]);
        $complaint->save();
        return $complaint;
    }


}
