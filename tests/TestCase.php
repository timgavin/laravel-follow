<?php

namespace TimGavin\LaravelFollow\Tests;

use TimGavin\LaravelFollow\LaravelFollowServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelFollowServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');

        include_once __DIR__.'/migrations/create_users_table.php';

        (new \CreateUsersTable())->up();
    }
}
