<?php

namespace TimGavin\LaravelFollow\Models;

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
     *
     * @return BelongsTo
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'following_id');
    }

    /**
     * Returns who is following a user.
     *
     * @return BelongsTo
     */
    public function followers(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
