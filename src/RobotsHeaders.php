<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class RobotsHeaders
{
    protected $content;

    public function __construct(array $headers)
    {
        $this->content = $this->parseHeaders($headers);
    }

    public static function readFrom(string $source): self
    {
        $content = @file_get_contents($source);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read from source {$source}");
        }

        return new self($http_response_header ?? []);
    }

    public static function create(array $headers): self
    {
        return new self($headers);
    }

    public function mayIndex(?string $userAgent = '*'): bool
    {
        return ! $this->noindex($userAgent);
    }

    public function mayFollow(?string $userAgent = '*'): bool
    {
        return ! $this->nofollow($userAgent);
    }

    public function noindex(?string $userAgent = '*'): bool
    {
        return $this->content[$userAgent]['noindex'] ?? false;
    }

    public function nofollow(?string $userAgent = '*'): bool
    {
        return $this->content[$userAgent]['nofollow'] ?? false;
    }

    protected function parseHeaders(array $headers): array
    {
        $content = [];

        foreach ($headers as $header) {
            if (strpos(strtolower($header), 'x-robots-tag') === false) {
                continue;
            }

            $headerParts = explode(':', $header);

            $userAgent = count($headerParts) === 3
                ? trim($headerParts[1])
                : '*';

            $options = end($headerParts);

            $content[$userAgent] = [
                'noindex' => strpos(strtolower($options), 'noindex') !== false,
                'nofollow' => strpos(strtolower($options), 'nofollow') !== false,
            ];
        }

        return $content;
    }
}
