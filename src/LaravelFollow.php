<?php

namespace TimGavin\LaravelFollow;

use TimGavin\LaravelFollow\Models\Follow;

trait LaravelFollow
{
    /**
     * Follow the given user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function follow($user)
    {
        Follow::firstOrCreate([
            'user_id' => $this->id,
            'following_id' => $user->id,
        ]);
    }

    /**
     * Unfollow the given user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function unfollow($user)
    {
        Follow::where('user_id', $this->id)
            ->where('following_id', $user->id)
            ->delete();
    }

    /**
     * Check if a user is following the given user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function isFollowing($user)
    {
        return Follow::where('user_id', $this->id)
            ->where('following_id', $user->id)
            ->first();
    }

    /**
     * Check if a user is followed by the given user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function isFollowedBy($user)
    {
        return Follow::where('user_id', $user->id)
            ->where('following_id', $this->id)
            ->first();
    }

    /**
     * Returns the users a user is following.
     *
     * @return array
     */
    public function getFollowing()
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->get();
    }

    /**
     * Returns IDs of the users a user is following.
     *
     * @return array
     */
    public function getFollowingIds()
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->pluck('following_id');
    }

    /**
     * Returns the users who are following a user.
     *
     * @return array
     */
    public function getFollowers()
    {
        return Follow::where('following_id', $this->id)
            ->with('followers')
            ->get();
    }

    /**
     * Returns IDs of the users who are following a user.
     *
     * @return array
     */
    public function getFollowersIds()
    {
        return Follow::where('following_id', $this->id)
            ->with('followers')
            ->pluck('user_id');
    }
}
