<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class Robots
{
    protected ?RobotsTxt $robotsTxt;

    public function __construct(
        protected ?string $userAgent = null,
        RobotsTxt|string|null $source = null,
    ) {
        if ($source instanceof RobotsTxt) {
            $this->robotsTxt = $source;
        } elseif (is_string($source)) {
            $this->robotsTxt = RobotsTxt::readFrom($source);
        } else {
            $this->robotsTxt = null;
        }
    }

    public function withTxt(RobotsTxt|string $source): self
    {
        $this->robotsTxt = $source instanceof RobotsTxt
                                ? $source
                                : RobotsTxt::readFrom($source);

        return $this;
    }

    public static function create(?string $userAgent = null, ?string $source = null): self
    {
        return new self($userAgent, $source);
    }

    /**
     * @param resource|null $context
     */
    public function mayIndex(string $url, ?string $userAgent = null, $context = null): bool
    {
        $userAgent = $userAgent ?? $this->userAgent;

        $robotsTxt = $this->robotsTxt ?? RobotsTxt::create($this->createRobotsUrl($url));

        if (! $robotsTxt->allows($url, $userAgent)) {
            return false;
        }

        $content = @file_get_contents($url, context: is_resource($context) ? $context : null);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read url `{$url}`");
        }

        return RobotsMeta::create($content)->mayIndex()
            && RobotsHeaders::create($http_response_header ?? [])->mayIndex();
    }

    /**
     * @param resource|null $context
     */
    public function mayFollowOn(string $url, $context = null): bool
    {
        $content = @file_get_contents($url, context: is_resource($context) ? $context : null);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read url `{$url}`");
        }

        return
            RobotsMeta::create($content)->mayFollow()
            && RobotsHeaders::create($http_response_header ?? [])->mayFollow();
    }

    protected function createRobotsUrl(string $url): string
    {
        $robotsUrl = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);

        if ($port = parse_url($url, PHP_URL_PORT)) {
            $robotsUrl .= ":{$port}";
        }

        return "{$robotsUrl}/robots.txt";
    }
}
