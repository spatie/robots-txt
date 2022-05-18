<?php

namespace Spatie\Robots;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

class RobotsMeta
{
    protected array $robotsMetaTagProperties = [];

    public static function readFrom(string $source): self
    {
        $content = @file_get_contents($source);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read from source `{$source}`");
        }

        return new self($content);
    }

    public static function create(string $source): self
    {
        return new self($source);
    }

    public function __construct(string $html)
    {
        $this->robotsMetaTagProperties = $this->findRobotsMetaTagProperties($html);
    }

    public function mayIndex(): bool
    {
        return ! $this->noindex();
    }

    public function mayFollow(): bool
    {
        return ! $this->nofollow();
    }

    public function noindex(): bool
    {
        return $this->robotsMetaTagProperties['noindex'] ?? false;
    }

    public function nofollow(): bool
    {
        return $this->robotsMetaTagProperties['nofollow'] ?? false;
    }

    #[ArrayShape(['noindex' => "bool", 'nofollow' => "bool"])]
    protected function findRobotsMetaTagProperties(string $html): array
    {
        $metaTagLine = $this->findRobotsMetaTagLine($html);

        return [
            'noindex' => $metaTagLine
                ? strpos(strtolower($metaTagLine), 'noindex') !== false
                : false,

            'nofollow' => $metaTagLine
                ? strpos(strtolower($metaTagLine), 'nofollow') !== false
                : false,
        ];
    }

    protected function findRobotsMetaTagLine(string $html): ?string
    {
        if (preg_match('/\<meta name=("|\')robots("|\').*?\>/mis', $html, $matches)) {
            return $matches[0];
        }

        return null;
    }
}
