<?php

use TimGavin\LaravelFollow\Models\User;

it('allows a user to follow another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $this->assertDatabaseHas('follows', [
        'user_id' => 1,
        'following_id' => 2,
    ]);
});

it('allows a user to follow another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2->id);

    $this->assertDatabaseHas('follows', [
        'user_id' => 1,
        'following_id' => 2,
    ]);
});

it('allows a user to unfollow another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user1->unfollow($user2);

    $this->assertDatabaseMissing('follows', [
        'user_id' => 1,
        'following_id' => 2,
    ]);
});

it('allows a user to unfollow another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2->id);
    $user1->unfollow($user2->id);

    $this->assertDatabaseMissing('follows', [
        'user_id' => 1,
        'following_id' => 2,
    ]);
});

it('checks if a user is following another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    expect($user1->isFollowing($user2))->toBeTrue();
});

it('checks if a user is following another user in cache', function () {
    $user1 = User::create();
    $user2 = User::create();

    $this->actingAs($user1);

    auth()->user()->follow($user2);
    auth()->user()->cacheFollowing();

    expect($user1->isFollowing($user2))->toBeTrue();
});

it('checks if a user is following another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2->id);

    expect($user1->isFollowing($user2->id))->toBeTrue();
});

it('checks if a user is followed by another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    expect($user2->isFollowedBy($user1))->toBeTrue();
});

it('checks if a user is followed by another user in cache', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $this->actingAs($user1);

    auth()->user()->cacheFollowers();

    expect(auth()->user()->isFollowedBy($user2))->toBeTrue();
});

it('checks if a user is followed by another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2->id);

    expect($user2->isFollowedBy($user1->id))->toBeTrue();
});

it('gets the users a user is following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $following = $user1->getFollowing();

    expect($following)->toHaveCount(1);
    expect($following->first()->following->id)->toBe(2);
});

it('gets the ids of users a user is following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $followingIds = $user1->getFollowingIds();

    expect($followingIds)->toContain(2);
});

it('gets the users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $followers = $user1->getFollowers();

    expect($followers)->toHaveCount(1);
    expect($followers->first()->following->id)->toBe(1);
});

it('gets the latest users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $followers = $user1->getLatestFollowers(1);

    expect($followers)->toHaveCount(1);
    expect($followers->first()->following->id)->toBe(1);
});

it('gets the ids of users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $followerIds = $user1->getFollowersIds();

    expect($followerIds)->toContain(2);
});

it('caches the ids of users a user is following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $this->actingAs($user1);

    auth()->user()->follow($user2);
    auth()->user()->cacheFollowing();

    expect(cache('laravel-follow:following.'.auth()->id()))->toContain(2);
});

it('gets the cached ids of users a user is following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $this->actingAs($user1);

    auth()->user()->follow($user2);
    auth()->user()->cacheFollowing();

    expect(auth()->user()->getFollowingCache())->toContain(2);
});

it('caches the ids of users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $this->actingAs($user1);

    auth()->user()->cacheFollowers();

    expect(cache('laravel-follow:followers.'.auth()->id()))->toContain(2);
});

it('gets the cached ids of users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $this->actingAs($user1);

    auth()->user()->cacheFollowers();

    expect(auth()->user()->getFollowersCache())->toContain(2);
});

it('clears the cached ids of users a user is following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $this->actingAs($user1);

    auth()->user()->cacheFollowing();
    auth()->user()->clearFollowingCache();

    expect(auth()->user()->getFollowingCache())->toBeEmpty();
});

it('clears the cached ids of users who are following a user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $this->actingAs($user1);

    auth()->user()->cacheFollowers();
    auth()->user()->clearFollowersCache();

    expect(auth()->user()->getFollowersCache())->toBeEmpty();
});

it('returns the follows relationship', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    expect($user1->follows)->toHaveCount(1);
    expect($user1->follows->first()->following_id)->toBe(2);
});

it('returns the followers relationship', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    expect($user1->followers)->toHaveCount(1);
    expect($user1->followers->first()->user_id)->toBe(2);
});

it('checks if users have any follow relationship', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user1->follow($user2);

    expect($user1->hasAnyFollowWith($user2))->toBeTrue();
    expect($user2->hasAnyFollowWith($user1))->toBeTrue();
    expect($user1->hasAnyFollowWith($user3))->toBeFalse();
});

it('gets follow relationships between two users', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user2->follow($user1);

    $relationships = $user1->getFollowRelationshipsWith($user2);

    expect($relationships)->toHaveCount(2);
});

it('gets the following relationship record', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $relationship = $user1->getFollowingRelationship($user2);

    expect($relationship)->not->toBeNull();
    expect($relationship->user_id)->toBe(1);
    expect($relationship->following_id)->toBe(2);
});

it('gets the follower relationship record', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $relationship = $user1->getFollowerRelationship($user2);

    expect($relationship)->not->toBeNull();
    expect($relationship->user_id)->toBe(2);
    expect($relationship->following_id)->toBe(1);
});

it('gets the combined following and followers ids', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user1->follow($user2);
    $user3->follow($user1);

    $ids = $user1->getFollowingAndFollowersIds();

    expect($ids['following'])->toContain(2);
    expect($ids['followers'])->toContain(3);
});

it('prevents a user from following themselves', function () {
    $user1 = User::create();

    $user1->follow($user1);

    $this->assertDatabaseMissing('follows', [
        'user_id' => 1,
        'following_id' => 1,
    ]);
});

it('prevents a user from following themselves by id', function () {
    $user1 = User::create();

    $user1->follow($user1->id);

    $this->assertDatabaseMissing('follows', [
        'user_id' => 1,
        'following_id' => 1,
    ]);
});

// New v2.0 tests

it('returns true when follow is successful', function () {
    $user1 = User::create();
    $user2 = User::create();

    $result = $user1->follow($user2);

    expect($result)->toBeTrue();
});

it('returns false when already following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $result = $user1->follow($user2);

    expect($result)->toBeFalse();
});

it('returns true when unfollow is successful', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $result = $user1->unfollow($user2);

    expect($result)->toBeTrue();
});

it('returns false when not following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $result = $user1->unfollow($user2);

    expect($result)->toBeFalse();
});

it('toggles follow on', function () {
    $user1 = User::create();
    $user2 = User::create();

    $result = $user1->toggleFollow($user2);

    expect($result)->toBeTrue();
    expect($user1->isFollowing($user2))->toBeTrue();
});

it('toggles follow off', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $result = $user1->toggleFollow($user2);

    expect($result)->toBeFalse();
    expect($user1->isFollowing($user2))->toBeFalse();
});

it('gets the following count', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user1->follow($user2);
    $user1->follow($user3);

    expect($user1->getFollowingCount())->toBe(2);
});

it('gets the followers count', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user2->follow($user1);
    $user3->follow($user1);

    expect($user1->getFollowersCount())->toBe(2);
});

it('checks if users are mutually following', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    expect($user1->isMutuallyFollowing($user2))->toBeFalse();

    $user2->follow($user1);
    expect($user1->isMutuallyFollowing($user2))->toBeTrue();
});

it('gets following with pagination', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $paginated = $user1->getFollowingPaginated(10);

    expect($paginated)->toBeInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);
    expect($paginated->total())->toBe(1);
});

it('gets followers with pagination', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user2->follow($user1);

    $paginated = $user1->getFollowersPaginated(10);

    expect($paginated)->toBeInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);
    expect($paginated->total())->toBe(1);
});

it('dispatches UserFollowed event when following', function () {
    \Illuminate\Support\Facades\Event::fake();

    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    \Illuminate\Support\Facades\Event::assertDispatched(
        \TimGavin\LaravelFollow\Events\UserFollowed::class,
        function ($event) {
            return $event->userId === 1 && $event->followingId === 2;
        }
    );
});

it('dispatches UserUnfollowed event when unfollowing', function () {
    \Illuminate\Support\Facades\Event::fake();

    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user1->unfollow($user2);

    \Illuminate\Support\Facades\Event::assertDispatched(
        \TimGavin\LaravelFollow\Events\UserUnfollowed::class,
        function ($event) {
            return $event->userId === 1 && $event->unfollowedId === 2;
        }
    );
});

it('clears cache when following', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user1->follow($user2);
    $user1->cacheFollowing();

    expect($user1->getFollowingCache())->toContain(2);

    $user1->follow($user3);

    expect(cache()->has('laravel-follow:following.1'))->toBeFalse();
});

it('clears cache when unfollowing', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user1->cacheFollowing();

    expect($user1->getFollowingCache())->toContain(2);

    $user1->unfollow($user2);

    expect(cache()->has('laravel-follow:following.1'))->toBeFalse();
});

it('uses query scopes on Follow model', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);

    $follows = \TimGavin\LaravelFollow\Models\Follow::whereUserFollows(1)->get();
    expect($follows)->toHaveCount(1);

    $followers = \TimGavin\LaravelFollow\Models\Follow::whereUserIsFollowedBy(2)->get();
    expect($followers)->toHaveCount(1);
});

it('clears the followers cache for another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user2->cacheFollowers();

    expect(cache()->has('laravel-follow:followers.2'))->toBeTrue();

    $user1->clearFollowersCacheFor($user2);

    expect(cache()->has('laravel-follow:followers.2'))->toBeFalse();
});

it('clears the followers cache for another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user2->cacheFollowers();

    expect(cache()->has('laravel-follow:followers.2'))->toBeTrue();

    $user1->clearFollowersCacheFor($user2->id);

    expect(cache()->has('laravel-follow:followers.2'))->toBeFalse();
});

it('clears the following cache for another user', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user1->cacheFollowing();

    expect(cache()->has('laravel-follow:following.1'))->toBeTrue();

    $user2->clearFollowingCacheFor($user1);

    expect(cache()->has('laravel-follow:following.1'))->toBeFalse();
});

it('clears the following cache for another user by id', function () {
    $user1 = User::create();
    $user2 = User::create();

    $user1->follow($user2);
    $user1->cacheFollowing();

    expect(cache()->has('laravel-follow:following.1'))->toBeTrue();

    $user2->clearFollowingCacheFor($user1->id);

    expect(cache()->has('laravel-follow:following.1'))->toBeFalse();
});

// New v2.1 tests - Batch query methods

it('gets all follow user ids in single query', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();
    $user4 = User::create();

    $user1->follow($user2); // user1 follows user2
    $user3->follow($user1); // user3 follows user1

    $ids = $user1->getAllFollowUserIds();

    expect($ids)->toContain($user2->id)
        ->toContain($user3->id)
        ->not->toContain($user1->id)
        ->not->toContain($user4->id);
});

it('returns empty array when no follow relationships exist', function () {
    $user1 = User::create();

    expect($user1->getAllFollowUserIds())->toBe([]);
});

it('excludes follow-related users with scope', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user1->follow($user2);

    $results = User::query()->excludeFollowRelated($user1)->pluck('id')->toArray();

    expect($results)->toContain($user1->id)
        ->toContain($user3->id)
        ->not->toContain($user2->id);
});

it('excludes users following this user with scope', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();

    $user2->follow($user1); // user2 follows user1

    $results = User::query()->excludeFollowRelated($user1)->pluck('id')->toArray();

    expect($results)->toContain($user1->id)
        ->toContain($user3->id)
        ->not->toContain($user2->id);
});

it('scope does nothing when user is null', function () {
    $user1 = User::create();
    $user2 = User::create();

    $results = User::query()->excludeFollowRelated(null)->pluck('id')->toArray();

    expect($results)->toContain($user1->id)
        ->toContain($user2->id);
});

it('gets follow status for multiple users in batch', function () {
    $user1 = User::create();
    $user2 = User::create();
    $user3 = User::create();
    $user4 = User::create();

    $user1->follow($user2);
    $user3->follow($user1);

    $statuses = $user1->getFollowStatusForUsers([$user2->id, $user3->id, $user4->id]);

    expect($statuses[$user2->id])->toBe(['is_following' => true, 'is_followed_by' => false]);
    expect($statuses[$user3->id])->toBe(['is_following' => false, 'is_followed_by' => true]);
    expect($statuses[$user4->id])->toBe(['is_following' => false, 'is_followed_by' => false]);
});

it('returns empty array for empty user ids array', function () {
    $user1 = User::create();

    expect($user1->getFollowStatusForUsers([]))->toBe([]);
});

it('has any follow with returns false when no relationship', function () {
    $user1 = User::create();
    $user2 = User::create();

    expect($user1->hasAnyFollowWith($user2))->toBeFalse();
});
