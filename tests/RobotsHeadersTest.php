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

        $this->assertFalse($robots->mayFollow('other-bot'));
    }

    /** @test */
    public function it_can_parse_headers_with_none()
    {
        $robots = RobotsHeaders::create([
            'X-custom: test',
            'X-Robots-Tag: google: none',
        ]);

        $this->assertTrue($robots->mayIndex());
        $this->assertTrue($robots->mayFollow());

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));

        $this->assertTrue($robots->mayIndex('other-bot'));
        $this->assertTrue($robots->mayFollow('other-bot'));

        $robots = RobotsHeaders::create([
            'X-custom: test',
            'X-Robots-Tag: none',
        ]);

        $this->assertFalse($robots->mayIndex());
        $this->assertFalse($robots->mayFollow());

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));

        $this->assertFalse($robots->mayIndex('other-bot'));
        $this->assertFalse($robots->mayFollow('other-bot'));
    }

    /** @test */
    public function it_throws_exception_on_reading_source()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not read from source `invalid_source`');

        $robots = RobotsHeaders::readFrom('invalid_source');
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

        $robots = RobotsHeaders::readFrom($this->getLocalTestServerUrl('/nofollow-noindex'));

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));

        $robots = RobotsHeaders::readFrom($this->getLocalTestServerUrl('/none'));

        $this->assertFalse($robots->mayIndex());
        $this->assertFalse($robots->mayFollow());

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));

        $robots = RobotsHeaders::readFrom($this->getLocalTestServerUrl('/none-google'));

        $this->assertFalse($robots->mayIndex('google'));
        $this->assertFalse($robots->mayFollow('google'));

        $this->assertTrue($robots->mayIndex());
        $this->assertTrue($robots->mayFollow());
    }

    /** @test */
    public function it_can_use_stream_context()
    {
        $this->markAsSkippedUnlessLocalTestServerIsRunning();

        // Execute a valid call, not expecting an exception
        $context = stream_context_create([
            'http' => [
                'user_agent' => 'test-user-agent',
            ]
        ]);

        RobotsHeaders::readFrom($this->getLocalTestServerUrl('/client-ua-must-match'), $context);

        // Execute an invalid call, expecting it to fail, confirming the expected result
        $context = stream_context_create([
            'http' => [
                'user_agent' => 'bad-user-agent',
            ]
        ]);

        $this->expectException(\InvalidArgumentException::class);

        RobotsHeaders::readFrom($this->getLocalTestServerUrl('/client-ua-must-match'), $context);
    }
}
