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

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayIndex());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayIndex());
    }

    /** @test */
    public function test_nofollow()
    {
        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/noindex-nofollow.html')->mayFollow());

        $this->assertFalse(RobotsMeta::readFrom(__DIR__.'/data/nofollow.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/noindex.html')->mayFollow());

        $this->assertTrue(RobotsMeta::readFrom(__DIR__.'/data/all-allowed.html')->mayFollow());
    }
}
