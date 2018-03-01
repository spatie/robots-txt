<?php

namespace Spatie\Robots\Tests;

use Spatie\Robots\Robots;

class RobotsTest extends TestCase
{
    /** @test */
    public function test()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__ . '/data/robots.txt');

        $this->assertTrue($robots->mayIndex('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_construct()
    {
        $robots = Robots::create('google')
            ->withTxt(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->mayIndex('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_method_call()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->mayIndex('/', 'google'));
    }

    /** @test */
    public function test_may_follow()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->mayFollowOn(__DIR__ . '/data/noindex-nofollow.html'));
    }

    /** @test */
    public function it_can_discover_default_robots_file()
    {
        $robots = Robots::create();

        $this->assertFalse($robots->mayIndex($this->getLocalServerUrl('/nl/admin')));

        $this->assertFalse($robots->mayIndex($this->getLocalServerUrl('/nl/admin/')));

        $this->assertTrue($robots->mayIndex($this->getLocalServerUrl('/nl')));
    }
}
