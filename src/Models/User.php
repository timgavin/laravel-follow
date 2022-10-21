<?php

namespace TimGavin\LaravelFollow\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use TimGavin\LaravelFollow\LaravelFollow;

class User extends Authenticatable
{
    use LaravelFollow;

    public $timestamps = false;

    // this model is only to be used for running tests
}
