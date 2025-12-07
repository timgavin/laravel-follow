# Laravel Follow

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Tests][ico-tests]][link-tests]

A simple Laravel package for following users.

## Requirements
- PHP 8.3 or greater
- Laravel 12 or greater

## Installation

Via Composer

``` bash
$ composer require timgavin/laravel-follow
```

Import Laravel Follow into your User model and add the trait.

```php
namespace App\Models;

use TimGavin\LaravelFollow\LaravelFollow;

class User extends Authenticatable
{
    use LaravelFollow;
}
```

Then run migrations.

```
php artisan migrate
```

## Configuration

Publish the config file.

```bash
php artisan vendor:publish --tag=laravel-follow-config
```

Available options:

```php
return [
    'cache_duration' => 60 * 60 * 24, // 24 hours in seconds
    'dispatch_events' => true,
    'user_model' => null, // falls back to auth config
];
```

## Usage

### Follow a user

Returns `true` if the user was followed, `false` if already following.

```php
auth()->user()->follow($user);
```

### Unfollow a user

Returns `true` if the user was unfollowed, `false` if not following.

```php
auth()->user()->unfollow($user);
```

### Toggle follow

Returns `true` if now following, `false` if unfollowed.

```php
auth()->user()->toggleFollow($user);
```

### Check if a user is following another user

```php
@if (auth()->user()->isFollowing($user))
    You are following this user.
@endif
```

### Check if a user is followed by another user

```php
@if (auth()->user()->isFollowedBy($user))
    This user is following you.
@endif
```

### Check if users are mutually following each other

```php
@if (auth()->user()->isMutuallyFollowing($user))
    You follow each other.
@endif
```

### Check if there is any follow relationship between two users

```php
@if (auth()->user()->hasFollowWith($user))
    There is a follow relationship.
@endif
```

### Get following count

```php
auth()->user()->getFollowingCount();
```

### Get followers count

```php
auth()->user()->getFollowersCount();
```

### Get the users a user is following

```php
auth()->user()->getFollowing();
```

### Get the users a user is following with pagination

```php
auth()->user()->getFollowingPaginated(15);
```

### Get the users who are following a user

```php
auth()->user()->getFollowers();
```

### Get the users who are following a user with pagination

```php
auth()->user()->getFollowersPaginated(15);
```

### Get the most recent users who are following a user

```php
// default limit is 5
auth()->user()->getLatestFollowers($limit);
```

### Get an array of IDs of the users a user is following

```php
auth()->user()->getFollowingIds();
```

### Get an array of IDs of the users who are following a user

```php
auth()->user()->getFollowersIds();
```

### Get an array of IDs of both following and followers

```php
auth()->user()->getFollowingAndFollowersIds();
```

## Relationships

Access the follows relationship (users this user is following).

```php
$user->follows;
```

Access the followers relationship (users following this user).

```php
$user->followers;
```

Get the follow relationship record where this user follows another.

```php
$user->getFollowingRelationship($otherUser);
```

Get the follow relationship record where another user follows this user.

```php
$user->getFollowerRelationship($otherUser);
```

Get all follow relationships between two users.

```php
$user->getFollowRelationshipsWith($otherUser);
```

## Caching

Cache the IDs of the users a user is following. Default duration is set in config.

```php
auth()->user()->cacheFollowing();

// custom duration in seconds
auth()->user()->cacheFollowing(3600);
```

Get the cached IDs of the users a user is following.

```php
auth()->user()->getFollowingCache();
```

Cache the IDs of the users who are following a user.

```php
auth()->user()->cacheFollowers();
```

Get the cached IDs of the users who are following a user.

```php
auth()->user()->getFollowersCache();
```

Clear the Following cache.

```php
auth()->user()->clearFollowingCache();
```

Clear the Followers cache.

```php
auth()->user()->clearFollowersCache();
```

Clear the Followers cache for another user. Useful after following a user to keep their followers cache in sync.

```php
auth()->user()->clearFollowersCacheFor($user);
```

Clear the Following cache for another user.

```php
auth()->user()->clearFollowingCacheFor($user);
```

Note: The cache is automatically cleared when calling `follow()` or `unfollow()`. However, only the current user's cache is cleared. Use `clearFollowersCacheFor()` to clear the target user's followers cache if needed.

## Events

Events are dispatched when users follow or unfollow each other.

```php
use TimGavin\LaravelFollow\Events\UserFollowed;
use TimGavin\LaravelFollow\Events\UserUnfollowed;

Event::listen(UserFollowed::class, function ($event) {
    // $event->userId - the user who followed
    // $event->followingId - the user who was followed
});

Event::listen(UserUnfollowed::class, function ($event) {
    // $event->userId - the user who unfollowed
    // $event->unfollowedId - the user who was unfollowed
});
```

Disable events in config.

```php
'dispatch_events' => false,
```

## Query Scopes

Query scopes are available on the Follow model.

```php
use TimGavin\LaravelFollow\Models\Follow;

// Get follows where a user is following others
Follow::whereUserFollows($userId)->get();

// Get follows where a user is being followed
Follow::whereUserIsFollowedBy($userId)->get();

// Get all follows involving a user
Follow::involvingUser($userId)->get();
```

## Upgrading

If upgrading from 1.x, please see the [upgrade guide](UPGRADE.md).

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email tim@timgavin.me instead of using the issue tracker.

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/timgavin/laravel-follow.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/timgavin/laravel-follow.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/actions/workflow/status/timgavin/laravel-follow/tests.yml?branch=main&label=tests&style=flat-square

[link-packagist]: https://packagist.org/packages/timgavin/laravel-follow
[link-downloads]: https://packagist.org/packages/timgavin/laravel-follow
[link-tests]: https://github.com/timgavin/laravel-follow/actions/workflows/tests.yml
[link-author]: https://github.com/timgavin
[link-contributors]: ../../contributors
