<?php

namespace Spatie\Robots\Tests;

use Spatie\Robots\RobotsMeta;

class RobotsMetaTest extends TestCase
{
    /** @test */
    public function test_noindex()
    {
        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-nofollow.html')->mayIndex());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex.html')->mayIndex());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-minified.html')->mayIndex());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayIndex());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayIndex());
    }

    /** @test */
    public function it_throws_exception_on_reading_from_source()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not read from source `invalid_source`');

        $robots = RobotsMeta::readFrom('invalid_source');
    }

    /** @test */
    public function it_can_create_new_from_source_string()
    {
        $this->assertTrue(RobotsMeta::create('<html></html>')->mayIndex());
    }

    /** @test */
    public function test_nofollow()
    {
        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-nofollow.html')->mayFollow());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/noindex.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayFollow());
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

        RobotsMeta::readFrom($this->getLocalTestServerUrl('/client-ua-must-match'), $context);

        // Execute an invalid call, expecting it to fail, confirming the expected result
        $context = stream_context_create([
            'http' => [
                'user_agent' => 'bad-user-agent',
            ]
        ]);

        $this->expectException(\InvalidArgumentException::class);

        RobotsMeta::readFrom($this->getLocalTestServerUrl('/client-ua-must-match'), $context);
    }
}
