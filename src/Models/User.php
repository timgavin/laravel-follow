<?php

namespace TimGavin\LaravelBlock\Models;

use Illuminate\Database\Eloquent\Model;
use TimGavin\LaravelBlock\LaravelBlock;

class User extends Model
{
    use LaravelBlock;

    public $timestamps = false;

    // this model is only to be used for running tests
}
