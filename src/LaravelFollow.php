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
    public function follow($user): void
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
    public function unfollow($user): void
    {
        Follow::where('user_id', $this->id)
            ->where('following_id', $user->id)
            ->delete();
    }

    /**
     * Check if a user is following the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isFollowing($user): bool
    {
        $isFollowing = Follow::toBase()
            ->where('user_id', $this->id)
            ->where('following_id', $user->id)
            ->first();

        if ($isFollowing) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is followed by the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isFollowedBy($user): bool
    {
        $isFollowedBy = Follow::toBase()
            ->where('user_id', $user->id)
            ->where('following_id', $this->id)
            ->first();

        if ($isFollowedBy) {
            return true;
        }

        return false;
    }

    /**
     * Returns the users a user is following.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getFollowing()
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->get();
    }

    /**
     * Returns the users who are following a user.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getFollowers()
    {
        return Follow::where('following_id', $this->id)
            ->with('followers')
            ->get();
    }

    /**
     * Returns IDs of the users a user is following.
     *
     * @return array
     */
    public function getFollowingIds(): array
    {
        return Follow::toBase()
            ->where('user_id', $this->id)
            ->pluck('following_id')
            ->toArray();
    }

    /**
     * Returns IDs of the users who are following a user.
     *
     * @return array
     */
    public function getFollowersIds(): array
    {
        return Follow::toBase()
            ->where('following_id', $this->id)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Returns IDs of the users a user is following.
     * Returns IDs of the users who are following a user.
     *
     * @return array
     */
    public function getFollowingAndFollowersIds(): array
    {
        return [
            'following' => $this->getFollowingIds(),
            'followers' => $this->getFollowersIds(),
        ];
    }

    /**
     * Caches IDs of the users a user is following.
     *
     * @param mixed
     * @return void
     */
    public function cacheFollowing($duration = null): void
    {
        $duration ?? Carbon::now()->addDay();

        cache()->forget('following.' . auth()->id());

        cache()->remember('following.' . auth()->id(), $duration, function () {
            return auth()->user()->getFollowingIds();
        });
    }

    /**
     * Caches IDs of the users who are following a user.
     *
     * @param mixed
     * @return void
     */
    public function cacheFollowers($duration = null): void
    {
        $duration ?? Carbon::now()->addDay();

        cache()->forget('followers.' . auth()->id());

        cache()->remember('followers.' . auth()->id(), $duration, function () {
            return auth()->user()->getFollowersIds();
        });
    }

    /**
     * Returns the cached IDs of the users a user is following.
     *
     * @return array
     */
    public function getFollowingCache(): array
    {
        return cache()->get('following.' . auth()->id()) ?? [];
    }

    /**
     * Returns the cached IDs of the users who are followers a user.
     *
     * @return array
     */
    public function getFollowersCache(): array
    {
        return cache()->get('followers.' . auth()->id()) ?? [];
    }

    /**
     * Clears the Following cache.
     *
     * @return void
     */
    public function clearFollowingCache(): void
    {
        cache()->forget('following.' . auth()->id());
    }

    /**
     * Clears the Followers cache.
     *
     * @return void
     */
    public function clearFollowersCache(): void
    {
        cache()->forget('followers.' . auth()->id());
    }
}
