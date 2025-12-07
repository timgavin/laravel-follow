# Upgrading from 1.x to 2.0

This guide covers upgrading from laravel-follow 1.x to 2.0.

## Breaking Changes

### Return Type Changes

The `follow()` and `unfollow()` methods now return `bool` instead of `void`.

**Before (1.x):**
```php
$user->follow($otherUser); // returns void
$user->unfollow($otherUser); // returns void
```

**After (2.0):**
```php
$result = $user->follow($otherUser); // returns true if followed, false if already following
$result = $user->unfollow($otherUser); // returns true if unfollowed, false if not following
```

**Migration:** If you're not using the return values, no changes needed. If you have code that explicitly checks for `null` returns, update it to check for `bool`.

## New Migration

A new migration adds database indexes for improved query performance. Run:

```bash
php artisan migrate
```

Or publish and run the migration:

```bash
php artisan vendor:publish --tag=laravel-follow-migrations
php artisan migrate
```

## New Features

### Toggle Method
```php
$result = $user->toggleFollow($otherUser);
// Returns true if now following, false if unfollowed
```

### Count Methods
```php
$followingCount = $user->getFollowingCount();
$followersCount = $user->getFollowersCount();
```

### Mutual Follow Check
```php
if ($user->isMutuallyFollowing($otherUser)) {
    // Both users follow each other
}
```

### Pagination
```php
$following = $user->getFollowingPaginated(15);
$followers = $user->getFollowersPaginated(15);
```

### Events

Events are now dispatched when users follow/unfollow each other:

- `TimGavin\LaravelFollow\Events\UserFollowed`
- `TimGavin\LaravelFollow\Events\UserUnfollowed`

Listen to these events:
```php
use TimGavin\LaravelFollow\Events\UserFollowed;

Event::listen(UserFollowed::class, function ($event) {
    // $event->userId - the user who followed
    // $event->followingId - the user who was followed
});
```

Disable events via config:
```php
// config/laravel-follow.php
'dispatch_events' => false,
```

### Query Scopes

New query scopes on the `Follow` model:
```php
use TimGavin\LaravelFollow\Models\Follow;

Follow::whereUserFollows($userId)->get();
Follow::whereUserIsFollowedBy($userId)->get();
Follow::involvingUser($userId)->get();
```

### Configuration

Publish the config file:
```bash
php artisan vendor:publish --tag=laravel-follow-config
```

Available options:
```php
return [
    'cache_duration' => 60 * 60 * 24, // 24 hours
    'dispatch_events' => true,
    'user_model' => null, // Falls back to auth config
];
```

### Automatic Cache Invalidation

The following/followers cache is now automatically cleared when you call `follow()` or `unfollow()`. Manual cache management is still available but often unnecessary.

## Upgrade Steps

1. Update your composer.json:
   ```json
   "timgavin/laravel-follow": "^2.0"
   ```

2. Run composer update:
   ```bash
   composer update timgavin/laravel-follow
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   ```

4. (Optional) Publish and review config:
   ```bash
   php artisan vendor:publish --tag=laravel-follow-config
   ```

5. Update any code that relied on `follow()`/`unfollow()` returning void.
