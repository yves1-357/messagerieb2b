<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'created_by',
        'avatar',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Users qui participent à cette conversation
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withPivot('role', 'joined_at', 'left_at', 'notifications_enabled')
                    ->withTimestamps();
    }

    /**
     * Messages dans conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * User qui a créé cette conversation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the latest message in this conversation
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get conversation avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    /**
     * Check if conversation is private (2 users only)
     */
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Check if conversation is a group
     */
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Get conversation display name for a specific user
     */
    public function getDisplayName(User $user): string
    {
        if ($this->isGroup()) {
            return $this->name ?? 'Groupe sans nom';
        }

        // For private conversations, return the other user's name
        $otherUser = $this->users()->where('user_id', '!=', $user->id)->first();
        return $otherUser ? $otherUser->name : 'Conversation privée';
    }
}
