<?php

namespace Tests\Spatie\Robots;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Spatie\Robots\RobotsHeaders;

class RobotsHeadersTest extends TestCase
{
    /** @test */
    public function it_can_parse_headers()
    {
        $robots = RobotsHeaders::create([
            'X-custom: test',
            'X-Robots-Tag: nofollow',
            'X-Robots-Tag: google: noindex, nofollow',
        ]);

        $this->assertTrue($robots->mayIndex());
        $this->assertFalse($robots->mayFollow());

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));
    }

    /** @test */
    public function it_can_read_response_headers_from_a_server()
    {
        try {
            $robots = RobotsHeaders::readFrom('http://localhost:4020/nofollow');

            $this->assertTrue($robots->mayIndex());
            $this->assertFalse($robots->mayFollow());
        } catch (InvalidArgumentException $e) {
            $this->markTestSkipped('Could not connect to the server.');
        }
    }

    /** @test */
    public function it_can_read_response_headers_from_a_server_for_a_user_agent()
    {
        try {
            $robots = RobotsHeaders::readFrom('http://localhost:4020/nofollow-noindex-google');

            $this->assertFalse($robots->mayIndex('google'));
            $this->assertFalse($robots->mayFollow('google'));
        } catch (InvalidArgumentException $e) {
            $this->markTestSkipped('Could not connect to the server.');
        }
    }
}
