<?php

namespace TimGavin\LaravelFollow;

use TimGavin\LaravelFollow\Models\Follow;

class LaravelFollowManager
{
    /**
     * Get the Follow model class.
     */
    public function model(): string
    {
        return Follow::class;
    }

    /**
     * Get all follows for a user.
     */
    public function getFollowsFor(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Follow::where('user_id', $userId)->get();
    }

    /**
     * Get all followers for a user.
     */
    public function getFollowersFor(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Follow::where('following_id', $userId)->get();
    }

    /**
     * Check if a follow relationship exists.
     */
    public function exists(int $userId, int $followingId): bool
    {
        return Follow::where('user_id', $userId)
            ->where('following_id', $followingId)
            ->exists();
    }

    /**
     * Get the configured cache duration.
     */
    public function getCacheDuration(): int
    {
        return config('laravel-follow.cache_duration', 86400);
    }

    /**
     * Check if events are enabled.
     */
    public function eventsEnabled(): bool
    {
        return config('laravel-follow.dispatch_events', true);
    }
}
