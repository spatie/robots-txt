<?php

namespace Spatie\Robots;

class RobotsMeta
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $this->parseContent($content);
    }

    public static function readFrom(string $source): self
    {
        $content = file_get_contents($source);

        return new self($content);
    }

    public static function create(string $content): self
    {
        return new self($content);
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
        return $this->content['noindex'] ?? false;
    }

    public function nofollow(): bool
    {
        return $this->content['nofollow'] ?? false;
    }

    private function parseContent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $meta = null;

        reset($lines);

        while (!$meta && $line = current($lines)) {
            if (strpos(strtolower($line), '<meta name="robots"') === false) {
                next($lines);

                continue;
            }

            $meta = $line;
        }

        return [
            'noindex' => $meta
                ? strpos(strtolower($meta), 'noindex') !== false
                : false,

            'nofollow' => $meta
                ? strpos(strtolower($meta), 'nofollow') !== false
                : false,
        ];
    }
}
