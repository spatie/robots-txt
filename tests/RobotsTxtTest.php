<?php

namespace Tests\Spatie\Robots;

use PHPUnit\Framework\TestCase;
use Spatie\Robots\RobotsTxt;

class RobotsTxtTest extends TestCase
{
    /** @test */
    public function it_can_parse_content()
    {
        $robots = RobotsTxt::create(file_get_contents(__DIR__ . '/data/robots.txt'));

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function it_can_parse_content_from_a_source()
    {
        $robots = RobotsTxt::readFrom(__DIR__ . '/data/robots.txt');

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function test_allowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__ . '/data/robots.txt');

        $this->assertTrue($robots->isAllowed('/'));
    }

    /** @test */
    public function test_disallowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->isAllowed('/en/admin/'));
    }

    /** @test */
    public function test_allowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__ . '/data/robots.txt');

        $this->assertTrue($robots->isAllowed('/test', 'google'));
    }

    /** @test */
    public function test_disallowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__ . '/data/robots.txt');

        $this->assertFalse($robots->isAllowed('/', 'google'));
    }
}
