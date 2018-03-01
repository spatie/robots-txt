<?php

namespace Spatie\Robots\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getLocalServerUrl(string $url): string
    {
        $url = ltrim($url, '/');

        return "http://localhost:4020/{$url}";
    }
}
