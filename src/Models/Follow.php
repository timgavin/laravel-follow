<?php

namespace TimGavin\LaravelFollow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * @return void
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    /**
     * Returns who is following a user.
     *
     * @return void
     */
    public function followers()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
