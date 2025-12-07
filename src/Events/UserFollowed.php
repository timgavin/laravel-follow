<?php

namespace TimGavin\LaravelFollow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $userId,
        public int $followingId,
    ) {
    }
}
