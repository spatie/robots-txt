<?php

namespace Spatie\Robots;

class RobotsTxt
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

    public function allows(string $url, ?string $userAgent = '*'): bool
    {
        $rules = $this->content[$userAgent] ?? [];

        $isMatchingRule = false;

        reset($rules);

        while (! $isMatchingRule && $rule = current($rules)) {
            $isMatchingRule = $rule === $url;

            if ($isMatchingRule) {
                break;
            }

            if (! $this->isDirectory($rule)) {
                continue;
            }

            $isMatchingRule = $this->isUrlInDirectory($url, $rule);

            next($rules);
        }

        return ! $isMatchingRule;
    }

    private function parseContent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $parsed = [];

        $current = null;

        foreach ($lines as $line) {
            if (! $line) {
                continue;
            }

            if ($this->isCommentLine($line)) {
                continue;
            }

            if ($this->isUserAgentLine($line)) {
                $parsed[$this->parseUserAgent($line)] = [];

                $current = &$parsed[$this->parseUserAgent($line)];

                continue;
            }

            if ($current === null) {
                continue;
            }

            $disallowUrl = $this->parseDisallow($line);

            $current[$disallowUrl] = $disallowUrl;
        }

        unset($current);

        return $parsed;
    }

    private function isCommentLine(string $line): bool
    {
        return strpos(trim($line), '#') === 0;
    }

    private function isUserAgentLine(string $line): bool
    {
        return strpos(trim($line), 'User-agent') === 0;
    }

    private function parseUserAgent(string $line): string
    {
        return trim(str_replace('User-agent', '', trim($line)), ': ');
    }

    private function parseDisallow(string $line): string
    {
        return trim(str_replace('Disallow', '', trim($line)), ': ');
    }

    private function isDirectory(string $path): bool
    {
        return substr($path, strlen($path) - 1, 1) === '/';
    }

    private function isUrlInDirectory(string $url, string $path): bool
    {
        return strpos($url, $path) === 0;
    }
}
