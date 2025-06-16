<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class RobotsHeaders
{
    protected array $robotHeadersProperties = [];

    /**
     * @param resource|null $context
     */
    public static function readFrom(string $source, $context = null): self
    {
        $content = @file_get_contents($source, context: is_resource($context) ? $context : null);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read from source `{$source}`");
        }

        return new self($http_response_header ?? []);
    }

    public static function create(array $headers): self
    {
        return new self($headers);
    }

    public function __construct(array $headers)
    {
        $this->robotHeadersProperties = $this->parseHeaders($headers);
    }

    public function mayIndex(string $userAgent = '*'): bool
    {
        return $this->none($userAgent) ? false : ! $this->noindex($userAgent);
    }

    public function mayFollow(string $userAgent = '*'): bool
    {
        return $this->none($userAgent) ? false : ! $this->nofollow($userAgent);
    }

    public function noindex(string $userAgent = '*'): bool
    {
        return
            $this->robotHeadersProperties[$userAgent]['noindex']
            ?? $this->robotHeadersProperties['*']['noindex']
            ?? false;
    }

    public function nofollow(string $userAgent = '*'): bool
    {
        return
            $this->robotHeadersProperties[$userAgent]['nofollow']
            ?? $this->robotHeadersProperties['*']['nofollow']
            ?? false;
    }

    public function none(string $userAgent = '*'): bool
    {
        return
            $this->robotHeadersProperties[$userAgent]['none']
            ?? $this->robotHeadersProperties['*']['none']
            ?? false;
    }

    protected function parseHeaders(array $headers): array
    {
        $robotHeaders = $this->filterRobotHeaders($headers);

        return array_reduce($robotHeaders, function (array $parsedHeaders, $header) {
            $header = $this->normalizeHeaders($header);

            $headerParts = explode(':', $header);

            $userAgent = count($headerParts) === 3
                ? trim($headerParts[1])
                : '*';

            $options = end($headerParts);

            $parsedHeaders[$userAgent] = [
                'noindex' => strpos(strtolower($options), 'noindex') !== false,
                'nofollow' => strpos(strtolower($options), 'nofollow') !== false,
                'none' => strpos(strtolower($options), 'none') !== false,
            ];

            return $parsedHeaders;
        }, []);
    }

    protected function filterRobotHeaders(array $headers): array
    {
        return array_filter($headers, function ($header) use ($headers) {
            $headerContent = $this->normalizeHeaders($headers[$header] ?? []);

            return strpos(strtolower($header), 'x-robots-tag') === 0
                || strpos(strtolower($headerContent), 'x-robots-tag') === 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function normalizeHeaders($headers): string
    {
        return implode(',', (array) $headers);
    }
}
