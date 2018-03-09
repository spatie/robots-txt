<?php

namespace Spatie\Robots;

class Robots
{
    /** @var string|null */
    protected $userAgent;

    /** @var \Spatie\Robots\RobotsTxt|null */
    protected $robotsTxt;

    public function __construct(string $userAgent = null, string $source = null)
    {
        $this->userAgent = $userAgent;

        $this->robotsTxt = $source
            ? RobotsTxt::readFrom($source)
            : null;
    }

    public function withTxt(string $source): self
    {
        $this->robotsTxt = RobotsTxt::readFrom($source);

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
