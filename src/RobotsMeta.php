<?php

namespace Spatie\Robots;

use InvalidArgumentException;

class RobotsMeta
{
    protected $robotsMetaTagProperties = [];

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
        $lines = explode(PHP_EOL, $html);

        foreach ($lines as $line) {
            if (strpos(strtolower(trim($line)), '<meta name="robots"') === 0) {
                return $line;
            }
        }

        return null;
    }
}
