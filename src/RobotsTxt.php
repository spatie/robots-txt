<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class RobotsTxt
{
    protected static $robotsCache = [];

    protected $disallowsPerUserAgent = [];

    public static function readFrom(string $source): self
    {
        $content = @file_get_contents($source) ?? '';

        return new self($content);
    }

    public function __construct(string $content)
    {
        $this->disallowsPerUserAgent = $this->getDisallowsPerUserAgent($content);
    }

    public static function create(string $source): self
    {
        if (
            strpos($source, 'http') !== false
            && strpos($source, 'robots.txt') !== false
        ) {
            return self::readFrom($source);
        }

        return new self($source);
    }

    public function allows(string $url, ?string $userAgent = '*'): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        $disallows = $this->disallowsPerUserAgent[$userAgent] ?? $this->disallowsPerUserAgent['*'] ?? [];

        return ! $this->pathIsDenied($path, $disallows);
    }

    protected function pathIsDenied(string $path, array $disallows): bool
    {
        foreach ($disallows as $disallow) {
            $trimmedDisallow = rtrim($disallow, '/');

            if (in_array($path, [$disallow, $trimmedDisallow])) {
                return true;
            }

            if (! $this->concernsDirectory($disallow)) {
                continue;
            }

            if ($this->isUrlInDirectory($path, $disallow)) {
                return true;
            }
        }

        return false;
    }

    protected function getDisallowsPerUserAgent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines);

        $disallowsPerUserAgent = [];

        $currentUserAgent = null;

        foreach ($lines as $line) {
            if ($this->isCommentLine($line)) {
                continue;
            }

            if ($this->isUserAgentLine($line)) {
                $disallowsPerUserAgent[$this->parseUserAgent($line)] = [];

                $currentUserAgent = &$disallowsPerUserAgent[$this->parseUserAgent($line)];

                continue;
            }

            if ($currentUserAgent === null) {
                continue;
            }

            $disallowUrl = $this->parseDisallow($line);

            $currentUserAgent[$disallowUrl] = $disallowUrl;
        }

        return $disallowsPerUserAgent;
    }

    protected function isCommentLine(string $line): bool
    {
        return strpos(trim($line), '#') === 0;
    }

    protected function isUserAgentLine(string $line): bool
    {
        return strpos(trim($line), 'User-agent') === 0;
    }

    protected function parseUserAgent(string $line): string
    {
        return trim(str_replace('User-agent', '', trim($line)), ': ');
    }

    protected function parseDisallow(string $line): string
    {
        return trim(str_replace('Disallow', '', trim($line)), ': ');
    }

    protected function concernsDirectory(string $path): bool
    {
        return substr($path, strlen($path) - 1, 1) === '/';
    }

    protected function isUrlInDirectory(string $url, string $path): bool
    {
        return strpos($url, $path) === 0;
    }
}
