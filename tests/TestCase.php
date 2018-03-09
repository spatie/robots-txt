<?php

namespace Spatie\Robots\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getLocalTestServerUrl(string $url = ''): string
    {
        $url = ltrim($url, '/');

        return "http://localhost:4020/{$url}";
    }

    protected function markAsSkippedUnlessLocalTestServerIsRunning()
    {
        if ($this->localTestServerIsRunning()) {
            return;
        }

        $this->markTestSkipped("The test server is not running. Start it by running `tests/server/start_server.sh`.");
    }

    protected function localTestServerIsRunning(): bool
    {
        $contents = @file_get_contents($this->getLocalTestServerUrl('robots.txt'));

        return ! empty($contents);
    }
}
