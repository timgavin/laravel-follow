<?php

namespace TimGavin\LaravelFollow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUnfollowed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $unfollowedId,
    ) {}
}
