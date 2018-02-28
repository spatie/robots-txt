<?php

namespace Tests\Spatie\Robots;

use PHPUnit\Framework\TestCase;
use Spatie\Robots\Robots;

class RobotsTest extends TestCase
{
    /** @test */
    public function test()
    {
        $robots = Robots::create(null, __DIR__ . '/data/robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_construct()
    {
        $robots = Robots::create('google', __DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->allows('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_method_call()
    {
        $robots = Robots::create(null, __DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->allows('/', 'google'));
    }

    /** @test */
    public function test_may_follow()
    {
        $robots = Robots::create(null, __DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->mayFollowOn(__DIR__ . '/data/noindex-nofollow.html'));
    }

    /** @test */
    public function it_can_discover_default_robots_file()
    {
        $robots = Robots::create();

        $this->assertFalse($robots->allows('https://www.spatie.be/nl/admin/'));

        $this->assertTrue($robots->allows('https://www.spatie.be/nl'));
    }
}
