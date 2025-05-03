<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawsuit extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'voice_note',
        'status',
        'category',
        'subcategory'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class, 'case_id');
    }

    public function acceptedBid()
    {
        return $this->belongsTo(Bid::class, 'accepted_bid_id');
    }
}
