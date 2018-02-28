<?php

namespace Spatie\Robots;

class Robots
{
    /** @var null|string */
    private $userAgent;

    /** @var null|\Spatie\Robots\RobotsTxt */
    private $robotsTxt;

    public function __construct(?string $userAgent = null, ?string $source = null)
    {
        $this->userAgent = $userAgent;

        $this->robotsTxt = $source
            ? RobotsTxt::readFrom($source)
            : null;
    }

    public static function create(?string $userAgent = null, ?string $source = null): self
    {
        return new self($userAgent, $source);
    }

    public function allows(string $url, ?string $userAgent = null): bool
    {
        $userAgent = $userAgent ?? $this->userAgent;

        $robotsTxt =
            $this->robotsTxt
            ?? RobotsTxt::create($this->createRobotsUrl($url));

        return
            $robotsTxt->allows($url, $userAgent)
            && RobotsMeta::readFrom($url)->mayIndex();
    }

    public function mayFollowOn(string $url): bool
    {
        return RobotsMeta::readFrom($url)->mayFollow();
    }

    private function createRobotsUrl(string $url): string
    {
        return parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/robots.txt';
    }
}
