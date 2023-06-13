<?php

namespace TimGavin\LaravelFollow\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use TimGavin\LaravelFollow\Models\User;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_follow_another_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);

        $this->assertDatabaseHas('follows', [
            'user_id' => 1,
            'following_id' => 2,
        ]);
    }

    /** @test */
    public function a_user_can_follow_another_user_by_id()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2->id);

        $this->assertDatabaseHas('follows', [
            'user_id' => 1,
            'following_id' => 2,
        ]);
    }

    /** @test */
    public function a_user_can_unfollow_another_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);
        $user1->unfollow($user2);

        $this->assertDatabaseMissing('follows', [
            'user_id' => 1,
            'following_id' => 2,
        ]);
    }

    /** @test */
    public function a_user_can_unfollow_another_user_by_id()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2->id);
        $user1->unfollow($user2->id);

        $this->assertDatabaseMissing('follows', [
            'user_id' => 1,
            'following_id' => 2,
        ]);
    }

    /** @test */
    public function is_a_user_following_another_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);

        if ($user1->isFollowing($user2)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function is_a_user_following_another_user_in_cache()
    {
        $user1 = User::create();
        $user2 = User::create();

        $this->actingAs($user1);

        auth()->user()->follow($user2);
        auth()->user()->cacheFollowing();

        if ($user1->isFollowing($user2)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function is_a_user_following_another_user_by_id()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2->id);

        if ($user1->isFollowing($user2->id)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function is_a_user_followed_by_another_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);

        if ($user2->isFollowedBy($user1)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function is_a_user_followed_by_another_user_in_cache()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $this->actingAs($user1);

        auth()->user()->cacheFollowers();

        if (auth()->user()->isFollowedBy($user2)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function is_a_user_followed_by_another_user_by_id()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2->id);

        if ($user2->isFollowedBy($user1->id)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function it_gets_the_users_a_user_is_following()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);

        $following = $user1->getFollowing();

        foreach ($following as $item) {
            if ($item->following->id === 2) {
                $this->assertTrue(true);
            } else {
                $this->fail();
            }
        }
    }

    /** @test */
    public function it_gets_the_ids_of_users_a_user_is_following()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user1->follow($user2);

        $followingIds = $user1->getFollowingIds();

        $this->assertContains(2, $followingIds);
    }

    /** @test */
    public function it_gets_the_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $followedBy = $user1->getFollowers();

        foreach ($followedBy as $item) {
            if ($item->following->id === 1) {
                $this->assertTrue(true);
            } else {
                $this->fail();
            }
        }
    }

    /** @test */
    public function it_gets_the_ids_of_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $followedByIds = $user1->getFollowersIds();

        $this->assertContains(2, $followedByIds);
    }

    /** @test */
    public function it_caches_the_ids_of_users_a_user_is_following()
    {
        $user1 = User::create();
        $user2 = User::create();

        $this->actingAs($user1);

        auth()->user()->follow($user2);
        auth()->user()->cacheFollowing();

        $this->assertContains(2, cache('following.' . auth()->id()));
    }

    /** @test */
    public function it_gets_the_cached_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $this->actingAs($user1);

        auth()->user()->follow($user2);
        auth()->user()->cacheFollowing();

        $this->assertContains(2, auth()->user()->getFollowingCache());
    }

    /** @test */
    public function it_caches_the_ids_of_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $this->actingAs($user1);

        auth()->user()->cacheFollowers();

        $this->assertContains(2, cache('followers.' . auth()->id()));
    }

    /** @test */
    public function it_gets_the_cached_ids_of_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $this->actingAs($user1);

        auth()->user()->cacheFollowers();

        $this->assertContains(2, auth()->user()->getFollowersCache());
    }

    /** @test */
    public function it_clears_the_cached_ids_of_users_who_are_followed_by_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $this->actingAs($user1);

        auth()->user()->cacheFollowing();

        auth()->user()->clearFollowingCache();

        $cache = auth()->user()->getFollowingCache();

        if (empty($cache)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }

    /** @test */
    public function it_clears_the_cached_ids_of_users_who_are_following_a_user()
    {
        $user1 = User::create();
        $user2 = User::create();

        $user2->follow($user1);

        $this->actingAs($user1);

        auth()->user()->cacheFollowers();

        auth()->user()->clearFollowersCache();

        $cache = auth()->user()->getFollowersCache();

        if (empty($cache)) {
            $this->assertTrue(true);
        } else {
            $this->fail();
        }
    }
}
