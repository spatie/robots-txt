<?php

namespace Spatie\Robots\Tests;

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
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = RobotsHeaders::readFrom($this->getLocalTestServerUrl('/nofollow'));

        $this->assertTrue($robots->mayIndex());
        $this->assertFalse($robots->mayFollow());
    }

    /** @test */
    public function it_can_read_response_headers_from_a_server_for_a_user_agent()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        $robots = RobotsHeaders::readFrom($this->getLocalTestServerUrl('/nofollow-noindex-google'));

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));
    }
}
