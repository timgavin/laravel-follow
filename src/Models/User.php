<?php

namespace TimGavin\LaravelFollow\Models;

use Illuminate\Database\Eloquent\Model;
use TimGavin\LaravelFollow\LaravelFollow;

class User extends Model
{
    use LaravelFollow;

    public $timestamps = false;

    // this model is only to be used for running tests
}
