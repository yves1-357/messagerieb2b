<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'read_at',
        'reply_to',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'type' => 'string',
    ];

    /**
     * The conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * The user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The message this is replying to (if any)
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    /**
     * Messages that are replying to this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to');
    }

    /**
     * Check if message has been read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if message is a reply
     */
    public function isReply(): bool
    {
        return $this->reply_to !== null;
    }

    /**
     * Get formatted message content based on type
     */
    public function getFormattedContentAttribute(): string
    {
        switch ($this->type) {
            case 'image':
                return '📷 Image';
            case 'file':
                return '📎 Fichier';
            case 'voice':
                return '🎤 Message vocal';
            default:
                return $this->content;
        }
    }
}
