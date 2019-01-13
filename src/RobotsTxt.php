<?php

namespace Spatie\Robots;

class RobotsTxt
{
    protected static $robotsCache = [];

    protected $disallowsPerUserAgent = [];

    public static function readFrom(string $source): self
    {
        $content = @file_get_contents($source) ?? '';

        return new self($content);
    }

    public function __construct(string $content)
    {
        $this->disallowsPerUserAgent = $this->getDisallowsPerUserAgent($content);
    }

    public static function create(string $source): self
    {
        if (
            strpos($source, 'http') !== false
            && strpos($source, 'robots.txt') !== false
        ) {
            return self::readFrom($source);
        }

        return new self($source);
    }

    // Google says (https://developers.google.com/search/reference/robots_txt) :
    // Only one group of group-member records is valid for a particular crawler.
    // The crawler must determine the correct group of records by finding the group
    // with the most specific user-agent that still matches.
    public function allows(string $url, ?string $userAgent = '*'): bool
    {
        $userAgent = strtolower($userAgent);

        if ($userAgent === null) {
            $userAgent = '*';
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if ($userAgent != '*' && isset($this->disallowsPerUserAgent[$userAgent])) {
            return ! $this->pathIsDenied($path, $this->disallowsPerUserAgent[$userAgent]) ?? true;
        } elseif ($userAgent != '*' && $wildCardUserAgent = $this->getWildCardUserAgent($userAgent)) {
            return ! $this->pathIsDenied($path, $this->disallowsPerUserAgent[$wildCardUserAgent]) ?? true;
        } elseif (isset($this->disallowsPerUserAgent['*'])) {
            return ! $this->pathIsDenied($path, $this->disallowsPerUserAgent['*']) ?? true;
        }

        return true;
    }

    protected function getWildCardUserAgent(string $userAgent): ?string
    {
        if ($userAgent !== '*') {
            for ($i = 1; $i <= strlen($userAgent); $i++) {
                $wildCardUserAgent = substr($userAgent, 0, $i).'*';
                if (isset($this->disallowsPerUserAgent[$wildCardUserAgent])) {
                    return $wildCardUserAgent;
                }
            }
        }

        return null;
    }

    protected function pathIsDenied(string $path, array $rules)
    {
        foreach ($rules as $pattern => $rule) {
            if ($this->match($pattern, $path)) {
                return $rule;
            }
        }
    }

    protected function complexRule($path): boolean
    {
        return strpos($path, ['$', '*']);
    }

    protected function match($pattern, $string)
    {
        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace('\*', '.*', $pattern);
        //$pattern = preg_replace('/\\\$$/', '$', $pattern); // is not working
        $pattern = substr($pattern, -2) == '\$' ? substr($pattern, 0, strlen($pattern) - 2).'$' : $pattern;
        $pattern = preg_replace('/\/$/', '/?', $pattern);

        return (bool) preg_match('/^'.$pattern.'/', $string);
    }

    protected function getDisallowsPerUserAgent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines);

        $disallowsPerUserAgent = [];

        $currentUserAgent = null;

        foreach ($lines as $line) {
            if ($this->isUserAgentLine($line)) {
                $disallowsPerUserAgent[$this->parseUserAgent($line)] = [];

                $currentUserAgent = &$disallowsPerUserAgent[$this->parseUserAgent($line)];

                continue;
            }

            if ($currentUserAgent === null) {
                continue;
            }

            list($pattern, $rule) = $this->parse($line);
            if ($pattern !== null) { // other than allow/disallow
                $currentUserAgent[$pattern] = $rule;
            }
        }

        return $this->orderRules($disallowsPerUserAgent);
    }

    // Google says (https://developers.google.com/search/reference/robots_txt) :
    // At a group-member level, in particular for allow and disallow directives, the most specific rule
    // based on the length of the [path] entry will trump the less specific (shorter) rule. The order of
    // precedence for rules with wildcards is undefined.
    protected function orderRules(array $disallowsPerUserAgent): array
    {
        foreach ($disallowsPerUserAgent as $userAgent => $rules) {
            array_multisort(array_map('strlen', array_keys($rules)), SORT_DESC, $rules);
            $disallowsPerUserAgent[$userAgent] = $rules;
        }

        return $disallowsPerUserAgent;
    }

    protected function isUserAgentLine(string $line): bool
    {
        return stripos(str_replace(' ', '', $line), 'user-agent:') === 0;
    }

    protected function parseUserAgent(string $line): string
    {
        return strtolower(trim(preg_replace('/^User-agent\s*:/i', '', trim($line))));
    }

    protected function parse(string $line): ?array
    {
        $line = trim(preg_replace('/\s+!/', ':', $line));

        if (stripos($line, 'disallow:') === 0) {
            return [trim(preg_replace('/^disallow:/i', '', $line)), true];
        }

        if (stripos($line, 'allow:') === 0) {
            return [trim(preg_replace('/^allow:/i', '', $line)), false];
        }

        // else: could be crawl-delay, sitemap...
    }

    protected function isUrlInDirectory(string $url, string $path): bool
    {
        return strpos($url, $path) === 0;
    }
}
