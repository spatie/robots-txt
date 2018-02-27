<?php

namespace Tests\Spatie\Robots;

use PHPUnit\Framework\TestCase;
use Spatie\Robots\Robots;

class RobotsTest extends TestCase
{
    /** @test */
    public function test()
    {
        $robots = Robots::create(__DIR__ . '/data/robots.txt');

        $this->assertTrue($robots->isAllowed('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_construct()
    {
        $robots = Robots::create(__DIR__ . '/data/robots.txt', 'google');

        $this->assertFalse($robots->isAllowed('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_method_call()
    {
        $robots = Robots::create(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->isAllowed('/', 'google'));
    }

    /** @test */
    public function test_may_follow()
    {
        $robots = Robots::create(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->mayFollowOn(__DIR__ . '/data/noindex-nofollow.html'));
    }
}
