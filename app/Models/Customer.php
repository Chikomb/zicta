<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['phone_number', 'session_id'];

    // Method to get the customer's phone number based on the session ID
    public static function getPhoneNumberBySessionId($session_id)
    {
        $customer = self::where('session_id', $session_id)->first();

        if ($customer) {
            return $customer->phone_number;
        }

        return null;
    }
}


