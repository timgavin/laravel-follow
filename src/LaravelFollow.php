<?php

namespace TimGavin\LaravelFollow;

use Carbon\Carbon;
use TimGavin\LaravelFollow\Models\Follow;

trait LaravelFollow
{
    /**
     * Follow the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function follow(mixed $user): void
    {
        $user_id = is_int($user) ? $user : $user->id;

        Follow::firstOrCreate([
            'user_id' => $this->id,
            'following_id' => $user_id,
        ]);
    }

    /**
     * Unfollow the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function unfollow(mixed $user): void
    {
        $user_id = is_int($user) ? $user : $user->id;

        Follow::where('user_id', $this->id)
            ->where('following_id', $user_id)
            ->delete();
    }

    /**
     * Check if a user is following the given user.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function isFollowing(mixed $user): bool
    {
        $user_id = is_int($user) ? $user : $user->id;

        if (cache()->has('following.'.$this->id)) {
            if (in_array($user_id, $this->getFollowingCache())) {
                return true;
            }

            return false;
        }

        $isFollowing = Follow::toBase()
            ->where('user_id', $this->id)
            ->where('following_id', $user_id)
            ->first();

        if ($isFollowing) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is followed by the given user.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function isFollowedBy(mixed $user): bool
    {
        $user_id = is_int($user) ? $user : $user->id;

        if (cache()->has('following.'.$this->id)) {
            if (in_array($user_id, $this->getFollowersCache())) {
                return true;
            }

            return false;
        }

        $isFollowedBy = Follow::toBase()
            ->where('user_id', $user_id)
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFollowing(): \Illuminate\Database\Eloquent\Collection
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->get();
    }

    /**
     * Returns the users who are following a user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFollowers(): \Illuminate\Database\Eloquent\Collection
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
     * @param  mixed  $duration
     * @return void
     */
    public function cacheFollowing(mixed $duration = null): void
    {
            $duration ?? Carbon::now()->addDay();

        cache()->forget('following.'.auth()->id());

        cache()->remember('following.'.auth()->id(), $duration, function () {
            return auth()->user()->getFollowingIds();
        });
    }

    /**
     * Caches IDs of the users who are following a user.
     *
     * @param  mixed|null  $duration
     * @return void
     */
    public function cacheFollowers(mixed $duration = null): void
    {
            $duration ?? Carbon::now()->addDay();

        cache()->forget('followers.'.auth()->id());

        cache()->remember('followers.'.auth()->id(), $duration, function () {
            return auth()->user()->getFollowersIds();
        });
    }

    /**
     * Returns the cached IDs of the users a user is following.
     *
     * @return array
     *
     * @throws
     */
    public function getFollowingCache(): array
    {
        return cache()->get('following.'.auth()->id()) ?? [];
    }

    /**
     * Returns the cached IDs of the users who are followers a user.
     *
     * @return array
     *
     * @throws
     */
    public function getFollowersCache(): array
    {
        return cache()->get('followers.'.auth()->id()) ?? [];
    }

    /**
     * Clears the Following cache.
     *
     * @return void
     */
    public function clearFollowingCache(): void
    {
        cache()->forget('following.'.auth()->id());
    }

    /**
     * Clears the Followers cache.
     *
     * @return void
     */
    public function clearFollowersCache(): void
    {
        cache()->forget('followers.'.auth()->id());
    }
}
