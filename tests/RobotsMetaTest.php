<?php

namespace Spatie\Robots\Tests;

use PHPUnit\Framework\Attributes\Test;
use Spatie\Robots\RobotsMeta;

class RobotsMetaTest extends TestCase
{
    #[Test]
    public function test_noindex()
    {
        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-nofollow.html')->mayIndex());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex.html')->mayIndex());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-minified.html')->mayIndex());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayIndex());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayIndex());
    }

    #[Test]
    public function it_throws_exception_on_reading_from_source()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not read from source `invalid_source`');

        $robots = RobotsMeta::readFrom('invalid_source');
    }

    #[Test]
    public function it_can_create_new_from_source_string()
    {
        $this->assertTrue(RobotsMeta::create('<html></html>')->mayIndex());
    }

    #[Test]
    public function test_nofollow()
    {
        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-nofollow.html')->mayFollow());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/noindex.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayFollow());
    }
}
