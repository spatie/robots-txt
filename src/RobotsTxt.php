<?php

namespace Spatie\Robots;

class RobotsTxt
{
    protected static array $robotsCache = [];

    protected array $disallowsPerUserAgent = [];

    protected bool $matchExactly = true;

    protected bool $includeGlobalGroup = true;

    public function ignoreGlobalGroup(): self
    {
        $this->includeGlobalGroup = false;

        return $this;
    }

    public function includeGlobalGroup(): self
    {
        $this->includeGlobalGroup = true;

        return $this;
    }

    public function withPartialMatches(): self
    {
        $this->matchExactly = false;

        return $this;
    }

    public function exactMatchesOnly(): self
    {
        $this->matchExactly = true;

        return $this;
    }

    public static function readFrom(string $source): self
    {
        $content = @file_get_contents($source);

        return new self($content !== false ? $content : '');
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

    public function allows(string $url, string | null $userAgent = '*'): bool
    {
        $requestUri = '';

        $parts = parse_url($url);

        if ($parts !== false) {
            if (isset($parts['path'])) {
                $requestUri .= $parts['path'];
            }

            if (isset($parts['query'])) {
                $requestUri .= '?'.$parts['query'];
            } elseif ($this->hasEmptyQueryString($url)) {
                $requestUri .= '?';
            }
        }

        $disallowsPerUserAgent = $this->includeGlobalGroup
            ? $this->disallowsPerUserAgent 
            : array_filter($this->disallowsPerUserAgent, fn ($key) => $key !== '*', ARRAY_FILTER_USE_KEY);
        
        $normalizedUserAgent = strtolower(trim($userAgent ?? ''));
            
        $disallows = $this->matchExactly
            ? $this->getDisallowsExactly($normalizedUserAgent, $disallowsPerUserAgent)
            : $this->getDisallowsContaining($normalizedUserAgent, $disallowsPerUserAgent);

        return ! $this->pathIsDenied($requestUri, $disallows);
    }

    protected function getDisallowsExactly(string $userAgent, array $disallowsPerUserAgent): array
    {
        return $disallowsPerUserAgent[$userAgent] ?? $disallowsPerUserAgent['*'] ?? [];
    }

    protected function getDisallowsContaining(string $userAgent, array $disallowsPerUserAgent): array
    {
        $disallows = [];

        foreach ($disallowsPerUserAgent as $userAgentKey => $disallowsPerUserAgentKey) {
            $contains = strpos($userAgent, $userAgentKey) !== false;

            if ($contains || $userAgentKey === '*') {
                $disallows = [...$disallows, ...$disallowsPerUserAgentKey];
            }
        }

        return $disallows;
    }

    protected function pathIsDenied(string $requestUri, array $disallows): bool
    {
        foreach ($disallows as $disallow) {
            if ($disallow === '') {
                continue;
            }

            $stopAtEndOfString = false;

            if ($disallow[-1] === '$') {
                // if the pattern ends with a dollar sign, the string must end there
                $disallow = substr($disallow, 0, -1);
                $stopAtEndOfString = true;
            }

            // convert to regexp
            $disallowRegexp = preg_quote($disallow, '/');

            // the pattern must start at the beginning of the string...
            $disallowRegexp = '^'.$disallowRegexp;

            // ...and optionally stop at the end of the string
            if ($stopAtEndOfString) {
                $disallowRegexp .= '$';
            }

            // replace (preg_quote'd) stars with an eager match
            $disallowRegexp = str_replace('\\*', '.*', $disallowRegexp);

            // enclose in delimiters
            $disallowRegexp = '/'.$disallowRegexp.'/';

            if (preg_match($disallowRegexp, $requestUri) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks for an empty query string.
     *
     * This works around the fact that parse_url() will not set the 'query' key when the query string is empty.
     * See: https://bugs.php.net/bug.php?id=78385
     */
    protected function hasEmptyQueryString(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if ($url[-1] === '?') { // ends with ?
            return true;
        }

        if (strpos($url, '?#') !== false) { // empty query string, followed by a fragment
            return true;
        }

        return false;
    }

    protected function getDisallowsPerUserAgent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines);

        $disallowsPerUserAgent = [];

        $currentUserAgents = [];

        $treatAllowDisallowLine = false;

        foreach ($lines as $line) {
            if ($this->isComment($line)) {
                continue;
            }

            if ($this->isEmptyLine($line)) {
                continue;
            }

            if ($this->isUserAgentLine($line)) {
                if ($treatAllowDisallowLine) {
                    $treatAllowDisallowLine = false;
                    $currentUserAgents = [];
                }
                $disallowsPerUserAgent[$this->parseUserAgent($line)] = [];

                $currentUserAgents[] = &$disallowsPerUserAgent[$this->parseUserAgent($line)];

                continue;
            }

            if ($this->isDisallowLine($line)) {
                $treatAllowDisallowLine = true;
            }

            if ($this->isAllowLine($line)) {
                $treatAllowDisallowLine = true;

                continue;
            }

            $disallowUrl = $this->parseDisallow($line);

            foreach ($currentUserAgents as &$currentUserAgent) {
                $currentUserAgent[$disallowUrl] = $disallowUrl;
            }
        }

        return $disallowsPerUserAgent;
    }

    protected function isComment(string $line): bool
    {
        return strpos(trim($line), '#') === 0;
    }

    protected function isEmptyLine(string $line): bool
    {
        return trim($line) === '';
    }

    protected function isUserAgentLine(string $line): bool
    {
        return strpos(trim(strtolower($line)), 'user-agent') === 0;
    }

    protected function parseUserAgent(string $line): string
    {
        return trim(str_replace('user-agent', '', strtolower(trim($line))), ': ');
    }

    protected function parseDisallow(string $line): string
    {
        return trim(substr_replace(strtolower(trim($line)), '', 0, 8), ': ');
    }

    protected function isDisallowLine(string $line): string
    {
        return trim(substr(str_replace(' ', '', strtolower(trim($line))), 0, 8), ': ') === 'disallow';
    }

    protected function isAllowLine(string $line): string
    {
        return trim(substr(str_replace(' ', '', strtolower(trim($line))), 0, 6), ': ') === 'allow';
    }
}
