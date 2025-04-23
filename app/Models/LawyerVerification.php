<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerVerification extends Model
{
    protected $fillable = [
        'lawyer_id',
        'status',
        'comment',
        'document_path',
        'reviewed_by',
        'reviewed_at',
    ];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
