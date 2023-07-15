# Laravel Follow

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

A simple Laravel package for following users.

## Requirements
- Laravel 9 or greater.
- Laravel `User` model.

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

## Usage

Follow a user
```php
auth()->user()->follow($user);
```

Unfollow a user
```php
auth()->user()->unfollow($user);
```

Check if a user is following another user
```php
@if (auth()->user()->isFollowing($user))
    You are following this user.
@endif
```

Check if a user is followed by another user
```php
@if (auth()->user()->isFollowedBy($user))
    This user is following you.
@endif
```

Returns the users a user is following
```php
auth()->user()->getFollowing();
```

Returns the users who are following a user
```php
auth()->user()->getFollowers();
```

Returns an array of IDs of the users a user is following
```php
auth()->user()->getFollowingIds();
```

Returns an array of IDs of the users who are following a user
```php
auth()->user()->getFollowersIds();
```

Returns an array of IDs of the users a user is following, and who is following a user
```php
auth()->user()->getFollowingAndFollowersIds()
```

Caches the IDs of the users a user is following. Default is 1 day.
```php
// 1 day
auth()->user()->cacheFollowing();

// 1 hour
auth()->user()->cacheFollowing(3600);

// 1 month
auth()->user()->cacheFollowing(Carbon::addMonth());
```

Returns an array of IDs of the users a user is following.
```php
auth()->user()->getFollowingCache();
```

Caches the IDs of the users who are following a user. Default is 1 day.
```php
auth()->user()->cacheFollowers();
```

Returns an array of IDs of the users who are following a user.
```php
auth()->user()->getFollowersCache();
```

Clears the Following cache
```php
auth()->user()->clearFollowingCache();
```

Clears the Followers cache
```php
auth()->user()->clearFollowersCache();
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email tim@timgavin.name instead of using the issue tracker.

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/timgavin/laravel-follow.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/timgavin/laravel-follow.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/timgavin/laravel-follow?style=flat-square
[ico-styleci]: https://styleci.io/repos/545076824/shield

[link-packagist]: https://packagist.org/packages/timgavin/laravel-follow
[link-downloads]: https://packagist.org/packages/timgavin/laravel-follow
[link-travis]: https://travis-ci.org/timgavin/laravel-follow
[link-styleci]: https://styleci.io/repos/545076824
[link-author]: https://github.com/timgavin
[link-contributors]: ../../contributors
