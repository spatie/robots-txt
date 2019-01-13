<?php

namespace Spatie\Robots\Tests;

use Spatie\Robots\RobotsTxt;

class RobotsTxtTest extends TestCase
{
    /** @test */
    public function it_can_parse_content()
    {
        $robots = RobotsTxt::create(file_get_contents(__DIR__.'/data/robots.txt'));

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function it_can_parse_content_from_a_source()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function test_allowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function test_disallowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/en/admin/'));
    }

    /** @test */
    public function test_allowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/test', 'google'));
    }

    /** @test */
    public function test_disallowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/', 'google'));
    }

    /** @test */
    public function it_can_handle_an_invalid_robots_txt()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/invalid-robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function it_can_handle_an_empty_robots_txt()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/empty-robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function it_can_handle_wildcard_and_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/wildcard-robots.txt');

        $this->assertTrue($robots->allows('/en/', 'google'));
        $this->assertTrue($robots->allows('/fr/', 'googlebot-news'));
        $this->assertFalse($robots->allows('/fr/news', 'googlebot-news'));
    }
}
