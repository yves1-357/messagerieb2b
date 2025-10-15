<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     *
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'username',
        'status',
        'last_seen_at',
    ];

    /**
     * champs cachées.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

        /**
     * convertit vers BD.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Determine if user est online
     */
    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    /**
     *  status detaillée with time info
     */
    public function getStatusWithTime(): array
    {
        if ($this->status === 'online') {
            return [
                'status' => 'online',
                'text' => 'en ligne',
                'color' => 'green'
            ];
        }

        if (!$this->last_seen_at) {
            return [
                'status' => 'offline',
                'text' => 'hors ligne',
                'color' => 'gray'
            ];
        }

        $lastSeen = is_string($this->last_seen_at) ?
            \Carbon\Carbon::parse($this->last_seen_at) :
            $this->last_seen_at;

        $minutesAgo = $lastSeen->diffInMinutes(now());

        if ($minutesAgo < 1) {
            return [
                'status' => 'recently',
                'text' => 'à l\'instant',
                'color' => 'yellow'
            ];
        } elseif ($minutesAgo < 60) {
            return [
                'status' => 'recently',
                'text' => "il y a {$minutesAgo} min",
                'color' => 'yellow'
            ];
        } elseif ($minutesAgo < 1440) { // 24 hours
            $hoursAgo = round($minutesAgo / 60);
            return [
                'status' => 'offline',
                'text' => "il y a {$hoursAgo}h",
                'color' => 'gray'
            ];
        } else {
            $daysAgo = round($minutesAgo / 1440);
            return [
                'status' => 'offline',
                'text' => "il y a {$daysAgo}j",
                'color' => 'gray'
            ];
        }
    }
}
