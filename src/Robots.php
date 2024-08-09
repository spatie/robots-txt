<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class Robots
{
    protected RobotsTxt | null $robotsTxt;

    public function __construct(
        protected string | null $userAgent = null,
        RobotsTxt | string | null $source = null,
    ) {
        if ($source instanceof RobotsTxt) {
            $this->robotsTxt = $source;
        } elseif (is_string($source)) {
            $this->robotsTxt = RobotsTxt::readFrom($source);
        } else {
            $this->robotsTxt = null;
        }
    }

    public function withTxt(RobotsTxt | string $source): self
    {
        $this->robotsTxt = $source instanceof RobotsTxt
                                ? $source
                                : RobotsTxt::readFrom($source);

        return $this;
    }

    public static function create(string $userAgent = null, string $source = null): self
    {
        return new self($userAgent, $source);
    }

    public function mayIndex(string $url, string $userAgent = null): bool
    {
        $userAgent = $userAgent ?? $this->userAgent;

        $robotsTxt = $this->robotsTxt ?? RobotsTxt::create($this->createRobotsUrl($url));

        $content = @file_get_contents($url);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read url `{$url}`");
        }

        return
            $robotsTxt->allows($url, $userAgent)
            && RobotsMeta::create($content)->mayIndex()
            && RobotsHeaders::create($http_response_header ?? [])->mayIndex();
    }

    public function mayFollowOn(string $url): bool
    {
        $content = @file_get_contents($url);

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
