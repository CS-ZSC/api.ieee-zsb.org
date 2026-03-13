<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_participant_id',
        'ticket_type',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->qr_code = (string) Str::uuid();
        });
    }

    public function eventParticipant()
    {
        return $this->belongsTo(EventParticipant::class);
    }
}