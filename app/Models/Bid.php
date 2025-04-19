<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'lawyer_id',
        'fee',
        'time_estimated',
        'status',
    ];

    public function case(){
        return $this->belongsTo(Lawsuit::class, 'case_id');
    }

    public function lawyer(){
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }
}
