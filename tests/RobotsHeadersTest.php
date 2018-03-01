<?php

namespace Spatie\Robots\Tests;

use InvalidArgumentException;
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
            $robots = RobotsHeaders::readFrom($this->getLocalServerUrl('/nofollow'));

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
            $robots = RobotsHeaders::readFrom($this->getLocalServerUrl('/nofollow-noindex-google'));

            $this->assertFalse($robots->mayIndex('google'));
            $this->assertFalse($robots->mayFollow('google'));
        } catch (InvalidArgumentException $e) {
            $this->markTestSkipped('Could not connect to the server.');
        }
    }
}
