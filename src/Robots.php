<?php

namespace Spatie\Robots;

class Robots
{
    private $robotsTxt;
    private $userAgent;

    public function __construct(string $source, ?string $userAgent = null)
    {
        $this->robotsTxt = RobotsTxt::readFrom($source);
        $this->userAgent = $userAgent;
    }

    public static function create(string $source, ?string $userAgent = null): self
    {
        return new self($source, $userAgent);
    }

    public function isAllowed(string $url, ?string $userAgent = null): bool
    {
        $userAgent = $userAgent ?? $this->userAgent;

        return
            $this->robotsTxt->isAllowed($url, $userAgent)
            && RobotsMeta::readFrom($url)->mayIndex();
    }

    public function mayFollow(string $url): bool
    {
        return RobotsMeta::readFrom($url)->mayFollow();
    }
}
