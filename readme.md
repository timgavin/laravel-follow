# Laravel Block

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

A simple Laravel package for blocking users.

Uses the default `User` class that ships with Laravel.

## Installation

Via Composer

``` bash
$ composer require timgavin/laravel-block
```

Import Laravel Block into your User model and add the trait.

```php
namespace App\Models;

use TimGavin\LaravelBlock\LaravelBlock;

class User extends Authenticatable
{
    use LaravelBlock;
}
```

Then run migrations.

```
php artisan migrate
```

## Usage

Block a user
```php
auth()->user()->block($user);
```

Unblock a user
```php
auth()->user()->unblock($user);
```

Check if a user is blocking another user
```php
@if (auth()->user()->isBlocking($user))
    You are blocking this user.
@endif
```

Check if a user is blocked by another user
```php
@if (auth()->user()->isBlockedBy($user))
    This user is blocking you.
@endif
```

Returns the users a user is blocking
```php
auth()->user()->getBlocking();
```

Returns an array of IDs of the users a user is blocking
```php
auth()->user()->getBlockingIds();
```

Returns the users who are blocking a user
```php
auth()->user()->getBlockedBy();
```

Returns an array of IDs of the users who are blocking a user
```php
auth()->user()->getBlockedByIds();
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

[ico-version]: https://img.shields.io/packagist/v/timgavin/laravel-block.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/timgavin/laravel-block.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/timgavin/laravel-block/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/timgavin/laravel-block
[link-downloads]: https://packagist.org/packages/timgavin/laravel-block
[link-travis]: https://travis-ci.org/timgavin/laravel-block
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/timgavin
[link-contributors]: ../../contributors
