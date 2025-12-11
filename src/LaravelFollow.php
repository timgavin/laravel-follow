<?php

namespace TimGavin\LaravelFollow;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use TimGavin\LaravelFollow\Events\UserFollowed;
use TimGavin\LaravelFollow\Events\UserUnfollowed;
use TimGavin\LaravelFollow\Models\Follow;

trait LaravelFollow
{
    /**
     * Define the follows relationship (users this user is following).
     */
    public function follows(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    /**
     * Define the followers relationship (users following this user).
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    /**
     * Follow the given user.
     *
     * @return bool True if the user was followed, false if already following or invalid.
     */
    public function follow(int|Authenticatable $user): bool
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null || $user_id === $this->id) {
            return false;
        }

        $follow = Follow::firstOrCreate([
            'user_id' => $this->id,
            'following_id' => $user_id,
        ]);

        if ($follow->wasRecentlyCreated) {
            $this->clearFollowingCache();

            if (config('laravel-follow.dispatch_events', true)) {
                event(new UserFollowed($this->id, $user_id));
            }

            return true;
        }

        return false;
    }

    /**
     * Unfollow the given user.
     *
     * @return bool True if the user was unfollowed, false if not following or invalid.
     */
    public function unfollow(int|Authenticatable $user): bool
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return false;
        }

        $deleted = Follow::where('user_id', $this->id)
            ->where('following_id', $user_id)
            ->delete();

        if ($deleted > 0) {
            $this->clearFollowingCache();

            if (config('laravel-follow.dispatch_events', true)) {
                event(new UserUnfollowed($this->id, $user_id));
            }

            return true;
        }

        return false;
    }

    /**
     * Toggle the follow status for a user.
     *
     * @return bool True if now following, false if unfollowed.
     */
    public function toggleFollow(int|Authenticatable $user): bool
    {
        if ($this->isFollowing($user)) {
            $this->unfollow($user);

            return false;
        }

        $this->follow($user);

        return true;
    }

    /**
     * Check if a user is following the given user.
     */
    public function isFollowing(int|Authenticatable $user): bool
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return false;
        }

        if (cache()->has('laravel-follow:following.'.$this->id)) {
            return in_array($user_id, $this->getFollowingCache());
        }

        return Follow::toBase()
            ->where('user_id', $this->id)
            ->where('following_id', $user_id)
            ->exists();
    }

    /**
     * Check if a user is followed by the given user.
     */
    public function isFollowedBy(int|Authenticatable $user): bool
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return false;
        }

        if (cache()->has('laravel-follow:followers.'.$this->id)) {
            return in_array($user_id, $this->getFollowersCache());
        }

        return Follow::toBase()
            ->where('user_id', $user_id)
            ->where('following_id', $this->id)
            ->exists();
    }

    /**
     * Check if two users mutually follow each other.
     */
    public function isMutuallyFollowing(int|Authenticatable $user): bool
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    /**
     * Check if there is any follow relationship between this user and another user.
     */
    public function hasAnyFollowWith(int|Authenticatable $user): bool
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return false;
        }

        return $this->isFollowing($user) || $this->isFollowedBy($user);
    }

    /**
     * Returns the users a user is following.
     */
    public function getFollowing(): Collection
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->get();
    }

    /**
     * Returns the users a user is following with pagination.
     */
    public function getFollowingPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Follow::where('user_id', $this->id)
            ->with('following')
            ->paginate($perPage);
    }

    /**
     * Returns the users who are following a user.
     */
    public function getFollowers(): Collection
    {
        return Follow::where('following_id', $this->id)
            ->with('user')
            ->get();
    }

    /**
     * Returns the users who are following a user with pagination.
     */
    public function getFollowersPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Follow::where('following_id', $this->id)
            ->with('user')
            ->paginate($perPage);
    }

    /**
     * Returns the latest users who are following a user.
     */
    public function getLatestFollowers(int $limit = 5): Collection
    {
        return Follow::where('following_id', $this->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Returns the count of users this user is following.
     */
    public function getFollowingCount(): int
    {
        return Follow::where('user_id', $this->id)->count();
    }

    /**
     * Returns the count of users following this user.
     */
    public function getFollowersCount(): int
    {
        return Follow::where('following_id', $this->id)->count();
    }

    /**
     * Returns IDs of the users a user is following.
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
     */
    public function getFollowersIds(): array
    {
        return Follow::toBase()
            ->where('following_id', $this->id)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Returns IDs of both users a user is following and followers.
     */
    public function getFollowingAndFollowersIds(): array
    {
        return [
            'following' => $this->getFollowingIds(),
            'followers' => $this->getFollowersIds(),
        ];
    }

    /**
     * Returns all user IDs involved in any follow relationship with this user.
     * Combines both following and followed-by in a single query.
     */
    public function getAllFollowUserIds(): array
    {
        return Follow::toBase()
            ->where('user_id', $this->id)
            ->orWhere('following_id', $this->id)
            ->get(['user_id', 'following_id'])
            ->flatMap(fn ($row) => [$row->user_id, $row->following_id])
            ->reject(fn ($id) => $id === $this->id)
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Scope to exclude users involved in any follow relationship with the given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|Authenticatable|null  $user  The user to check follows for (defaults to auth user)
     */
    public function scopeExcludeFollowRelated($query, int|Authenticatable|null $user = null): void
    {
        $userId = match (true) {
            is_int($user) => $user,
            $user instanceof Authenticatable => $user->id,
            default => auth()->id(),
        };

        if ($userId === null) {
            return;
        }

        $followIds = Follow::toBase()
            ->where('user_id', $userId)
            ->orWhere('following_id', $userId)
            ->get(['user_id', 'following_id'])
            ->flatMap(fn ($row) => [$row->user_id, $row->following_id])
            ->reject(fn ($id) => $id === $userId)
            ->unique()
            ->values()
            ->toArray();

        if (! empty($followIds)) {
            $query->whereNotIn($query->getModel()->getTable().'.id', $followIds);
        }
    }

    /**
     * Get follow status for multiple users in batch.
     * Returns array keyed by user ID with is_following and is_followed_by flags.
     *
     * @param  array<int>  $userIds
     * @return array<int, array{is_following: bool, is_followed_by: bool}>
     */
    public function getFollowStatusForUsers(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $following = Follow::toBase()
            ->where('user_id', $this->id)
            ->whereIn('following_id', $userIds)
            ->pluck('following_id')
            ->flip()
            ->toArray();

        $followedBy = Follow::toBase()
            ->whereIn('user_id', $userIds)
            ->where('following_id', $this->id)
            ->pluck('user_id')
            ->flip()
            ->toArray();

        return collect($userIds)->mapWithKeys(fn ($id) => [
            $id => [
                'is_following' => isset($following[$id]),
                'is_followed_by' => isset($followedBy[$id]),
            ],
        ])->toArray();
    }

    /**
     * Get follow status for a single user in one query.
     *
     * @return array{is_following: bool, is_followed_by: bool}
     */
    public function getFollowStatusFor(int|Authenticatable $user): array
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return [
                'is_following' => false,
                'is_followed_by' => false,
            ];
        }

        $results = Follow::toBase()
            ->where(function ($query) use ($user_id) {
                $query->where('user_id', $this->id)->where('following_id', $user_id);
            })
            ->orWhere(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('following_id', $this->id);
            })
            ->get(['user_id', 'following_id']);

        $isFollowing = false;
        $isFollowedBy = false;

        foreach ($results as $row) {
            if ($row->user_id == $this->id && $row->following_id == $user_id) {
                $isFollowing = true;
            }
            if ($row->user_id == $user_id && $row->following_id == $this->id) {
                $isFollowedBy = true;
            }
        }

        return [
            'is_following' => $isFollowing,
            'is_followed_by' => $isFollowedBy,
        ];
    }

    /**
     * Caches IDs of the users a user is following.
     */
    public function cacheFollowing(mixed $duration = null): void
    {
        $duration = $duration ?? config('laravel-follow.cache_duration', 86400);

        cache()->forget('laravel-follow:following.'.$this->id);

        cache()->remember('laravel-follow:following.'.$this->id, $duration, function () {
            return $this->getFollowingIds();
        });
    }

    /**
     * Caches IDs of the users who are following a user.
     */
    public function cacheFollowers(mixed $duration = null): void
    {
        $duration = $duration ?? config('laravel-follow.cache_duration', 86400);

        cache()->forget('laravel-follow:followers.'.$this->id);

        cache()->remember('laravel-follow:followers.'.$this->id, $duration, function () {
            return $this->getFollowersIds();
        });
    }

    /**
     * Returns the cached IDs of the users a user is following.
     */
    public function getFollowingCache(): array
    {
        return cache()->get('laravel-follow:following.'.$this->id) ?? [];
    }

    /**
     * Returns the cached IDs of the users who are following a user.
     */
    public function getFollowersCache(): array
    {
        return cache()->get('laravel-follow:followers.'.$this->id) ?? [];
    }

    /**
     * Clears the Following cache.
     */
    public function clearFollowingCache(): void
    {
        cache()->forget('laravel-follow:following.'.$this->id);
    }

    /**
     * Clears the Followers cache.
     */
    public function clearFollowersCache(): void
    {
        cache()->forget('laravel-follow:followers.'.$this->id);
    }

    /**
     * Clears the Followers cache for another user.
     */
    public function clearFollowersCacheFor(int|Authenticatable $user): void
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id !== null) {
            cache()->forget('laravel-follow:followers.'.$user_id);
        }
    }

    /**
     * Clears the Following cache for another user.
     */
    public function clearFollowingCacheFor(int|Authenticatable $user): void
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id !== null) {
            cache()->forget('laravel-follow:following.'.$user_id);
        }
    }

    /**
     * Get follow relationships between this user and another user.
     */
    public function getFollowRelationshipsWith(int|Authenticatable $user): Collection
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return new Collection;
        }

        return Follow::where(function ($query) use ($user_id) {
            $query->where('user_id', $this->id)
                ->where('following_id', $user_id);
        })->orWhere(function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                ->where('following_id', $this->id);
        })->get();
    }

    /**
     * Get the follow record where this user follows another.
     */
    public function getFollowingRelationship(int|Authenticatable $user): ?Follow
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return null;
        }

        return $this->follows()
            ->where('following_id', $user_id)
            ->first();
    }

    /**
     * Get the follow record where another user follows this user.
     */
    public function getFollowerRelationship(int|Authenticatable $user): ?Follow
    {
        $user_id = is_int($user) ? $user : ($user->id ?? null);

        if ($user_id === null) {
            return null;
        }

        return $this->followers()
            ->where('user_id', $user_id)
            ->first();
    }
}
