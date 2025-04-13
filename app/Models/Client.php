<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'dob',
        'photo',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
