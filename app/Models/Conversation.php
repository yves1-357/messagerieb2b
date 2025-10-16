<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // 'private' ou 'group'
        'created_by',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Users qui participent à cette conversation
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withTimestamps()
                    ->withPivot('joined_at', 'left_at');
    }

    /**
     * Messages de cette conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    /**
     * Dernier message de la conversation
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Créateur de la conversation
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour les conversations d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Obtenir l'autre participant dans une conversation privée
     */
    public function getOtherParticipant($currentUserId)
    {
        if ($this->type === 'private') {
            return $this->users()->where('user_id', '!=', $currentUserId)->first();
        }
        return null;
    }
}
