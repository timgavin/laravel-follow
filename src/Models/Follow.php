<?php

namespace TimGavin\LaravelFollow\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    use HasFactory;

    protected $table = 'follows';

    protected $fillable = [
        'user_id',
        'following_id',
    ];

    /**
     * Returns who a user is following.
     */
    public function following(): BelongsTo
    {
        $userModel = config('laravel-follow.user_model') ?? config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'following_id');
    }

    /**
     * Returns who is following a user.
     */
    public function user(): BelongsTo
    {
        $userModel = config('laravel-follow.user_model') ?? config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Alias for user() for backwards compatibility.
     */
    public function followers(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Scope to get follows where a user is following others.
     */
    public function scopeWhereUserFollows(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get follows where a user is being followed.
     */
    public function scopeWhereUserIsFollowedBy(Builder $query, int $userId): Builder
    {
        return $query->where('following_id', $userId);
    }

    /**
     * Scope to get follows involving a specific user (either direction).
     */
    public function scopeInvolvingUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)
            ->orWhere('following_id', $userId);
    }
}
