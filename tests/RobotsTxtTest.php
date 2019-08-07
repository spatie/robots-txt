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
    public function test_disallow_keyword_in_url_is_correctly_disallowed()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/es/admin-disallow/', '*'));
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
    public function it_can_handle_star_in_pattern()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/en/admin'));
        $this->assertFalse($robots->allows('/en/admin/'));
        $this->assertFalse($robots->allows('/en/admin/users'));
    }

    /** @test */
    public function it_can_handle_dollar_in_pattern()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad'));
        $this->assertFalse($robots->allows('/fr/admin'));
        $this->assertTrue($robots->allows('/fr/admin/'));
        $this->assertTrue($robots->allows('/fr/admin?test'));
    }

    /** @test */
    public function it_can_handle_query_strings()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/en/admin'));
        $this->assertTrue($robots->allows('/en/admin?id=123'));
        $this->assertFalse($robots->allows('/en/admin?print'));
        $this->assertFalse($robots->allows('/en/admin?print=true'));
    }
}
