<?php

namespace TimGavin\LaravelBlock;

use \TimGavin\LaravelBlock\Models\Block;

trait LaravelBlock
{
    /**
     * Block the given user
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function block($user)
    {
        Block::firstOrCreate([
            'user_id' => $this->id,
            'blocking_id' => $user->id,
        ]);
    }

    /**
     * Unblock the given user
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function unblock($user)
    {
        Block::where('user_id', $this->id)
            ->where('blocking_id', $user->id)
            ->delete();
    }

    /**
     * Check if a user is blocking the given user
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function isBlocking($user)
    {
        return Block::where('user_id', $this->id)
            ->where('blocking_id', $user->id)
            ->first();
    }

    /**
     * Check if a user is blocked by the given user
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public function isBlockedBy($user)
    {
        return Block::where('user_id', $user->id)
            ->where('blocking_id', $this->id)
            ->first();
    }

    /**
     * Returns the users a user is blocking
     *
     * @return array
     */
    public function getBlocking()
    {
        return Block::where('user_id', $this->id)
            ->with('blocking')
            ->get();
    }

    /**
     * Returns IDs of the users a user is blocking
     *
     * @return array
     */
    public function getBlockingIds()
    {
        return Block::where('user_id', $this->id)
            ->with('blocking')
            ->pluck('blocking_id');
    }

    /**
     * Returns the users who are blocking a user
     *
     * @return array
     */
    public function getBlockedBy()
    {
        return Block::where('blocking_id', $this->id)
            ->with('blockers')
            ->get();
    }

    /**
     * Returns IDs of the users who are blocking a user
     *
     * @return array
     */
    public function getBlockedByIds()
    {
        return Block::where('blocking_id', $this->id)
            ->with('blockers')
            ->pluck('user_id');
    }
}
