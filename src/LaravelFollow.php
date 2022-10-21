<?php

namespace TimGavin\LaravelFollow;

use Carbon\Carbon;
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
        return Follow::toBase()
            ->where('user_id', $this->id)
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
        return Follow::toBase()
            ->where('user_id', $user->id)
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
        return Follow::toBase()
            ->where('user_id', $this->id)
            ->pluck('following_id')
            ->toArray();
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
        return Follow::toBase()
            ->where('following_id', $this->id)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Caches IDs of the users a user is following.
     *
     * @param mixed
     * @return array
     */
    public function cacheFollowing($duration = null)
    {
        $duration ?? Carbon::now()->addDay();

        cache()->forget('following.' . auth()->id());

        cache()->remember('following.' . auth()->id(), $duration, function () {
            return auth()->user()->getFollowingIds();
        });
    }

    /**
     * Returns IDs of the users a user is following.
     *
     * @return array
     */
    public function getFollowingCache()
    {
        return cache()->get('following.' . auth()->id()) ?? [];
    }
}
