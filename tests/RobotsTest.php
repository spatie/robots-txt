<?php

namespace Spatie\Robots\Tests;

use Spatie\Robots\Robots;

class RobotsTest extends TestCase
{
    /** @test */
    public function test()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->mayIndex('/'));
    }

    /** @test */
    public function it_return_true_on_source_string()
    {
        $this->assertTrue((new Robots('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36', 'source'))->mayIndex('https://spatie.be'));
    }

    /** @test */
    public function with_custom_user_agent_in_construct()
    {
        $robots = Robots::create('google')
            ->withTxt(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->mayIndex('/'));
    }

    /** @test */
    public function with_custom_user_agent_in_method_call()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->mayIndex('/', 'google'));
    }

    /** @test */
    public function test_may_follow()
    {
        $robots = Robots::create()
            ->withTxt(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->mayFollowOn(__DIR__.'/data/noindex-nofollow.html'));
    }

    /** @test */
    public function it_can_discover_default_robots_file()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = Robots::create();

        $this->assertTrue($robots->mayIndex($this->getLocalTestServerUrl('/nl/admin')));

        $this->assertFalse($robots->mayIndex($this->getLocalTestServerUrl('/nl/admin/')));

        $this->assertTrue($robots->mayIndex($this->getLocalTestServerUrl('/nl')));
    }
}
