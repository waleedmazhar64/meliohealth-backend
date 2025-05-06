<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'cardholder_name',
        'card_number',
        'expiry_date',
        'cvv',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
