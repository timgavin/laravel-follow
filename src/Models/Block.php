<?php

namespace TimGavin\LaravelBlock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $table = 'blocks';

    protected $fillable = [
        'user_id',
        'blocking_id',
    ];

    /**
     * Returns who a user is blocking
     *
     * @return void
     */
    public function blocking()
    {
        return $this->belongsTo(User::class, 'blocking_id');
    }

    /**
     * Returns who is blocking a user
     *
     * @return void
     */
    public function blockers()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
