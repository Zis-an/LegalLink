<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'bar_id',
        'user_id',
        'practice_area',
        'chamber_name',
        'chamber_address',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
