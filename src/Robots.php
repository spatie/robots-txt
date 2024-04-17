<?php

namespace Spatie\Robots;

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

        return
            $robotsTxt->allows($url, $userAgent)
            && RobotsMeta::readFrom($url)->mayIndex()
            && RobotsHeaders::readFrom($url)->mayIndex();
    }

    public function mayFollowOn(string $url): bool
    {
        return
            RobotsMeta::readFrom($url)->mayFollow()
            && RobotsHeaders::readFrom($url)->mayFollow();
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
