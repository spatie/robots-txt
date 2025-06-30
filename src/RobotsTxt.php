<?php

namespace Spatie\Robots;

class RobotsTxt
{
    protected static array $robotsCache = [];

    protected array $disallowsPerUserAgent = [];

    protected array $allowsPerUserAgent = [];

    protected array $crawlDelaysPerUserAgent = [];

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
        $this->allowsPerUserAgent = $this->getAllowsPerUserAgent($content);
        $this->crawlDelaysPerUserAgent = $this->getCrawlDelaysPerUserAgent($content);
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

    public function allows(string $url, ?string $userAgent = '*'): bool
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

        $allowsPerUserAgent = $this->includeGlobalGroup
            ? $this->allowsPerUserAgent
            : array_filter($this->allowsPerUserAgent, fn ($key) => $key !== '*', ARRAY_FILTER_USE_KEY);

        $normalizedUserAgent = strtolower(trim($userAgent ?? ''));

        $disallows = $this->matchExactly
            ? $this->getDisallowsExactly($normalizedUserAgent, $disallowsPerUserAgent)
            : $this->getDisallowsContaining($normalizedUserAgent, $disallowsPerUserAgent);

        $allows = $this->matchExactly
            ? $this->getDisallowsExactly($normalizedUserAgent, $allowsPerUserAgent)
            : $this->getDisallowsContaining($normalizedUserAgent, $allowsPerUserAgent);

        if ($this->pathIsExplicitAllowed($url, $allows)) {
            return true;
        }

        $isDenied = $this->pathIsDenied($requestUri, $disallows);
        if ($isDenied) {
            $isAllowedWeight = $this->pathMatchWeight($requestUri, $allows);
            if ($isAllowedWeight === 0) {
                return false;
            }
            $isDisallowedWeight = $this->pathMatchWeight($requestUri, $disallows);

            return $isAllowedWeight > $isDisallowedWeight;
        }

        return ! $isDenied;
    }

    /**
     * @return Disallow[]|null Null if path is allowed, or a list of reasons if path is disallowed
     */
    public function whyDisallows(string $path, string $userAgent): ?array
    {
        if ($this->allows($path, $userAgent)) {
            return null;
        }
        $reasons = $this->getDisallows($path, $userAgent);
        if ($userAgent !== '*') {
            $newDisallows = $this->getDisallows($path, '*');
            $reasons = array_merge($reasons, $newDisallows);
        }

        return $reasons;
    }

    /**
     * @return Disallow[]
     */
    protected function getDisallows(string $path, string $userAgent): array
    {
        $reasons = [];
        foreach (array_keys($this->disallowsPerUserAgent[$userAgent] ?? []) as $disallowedPath) {
            if (str_starts_with($path, $disallowedPath)) {
                $reasons[] = new Disallow($userAgent, $disallowedPath);
            }
        }

        return $reasons;
    }

    public function crawlDelay(string $userAgent = '*'): ?string
    {
        $crawlDelaysPerUserAgent = $this->includeGlobalGroup
            ? $this->crawlDelaysPerUserAgent
            : array_filter($this->crawlDelaysPerUserAgent, fn ($key) => $key !== '*', ARRAY_FILTER_USE_KEY);

        $normalizedUserAgent = strtolower(trim($userAgent ?? ''));

        if (isset($crawlDelaysPerUserAgent[$normalizedUserAgent])) {
            return $crawlDelaysPerUserAgent[$normalizedUserAgent];
        } elseif (isset($crawlDelaysPerUserAgent['*'])) {
            return $crawlDelaysPerUserAgent['*'];
        }

        return null;
    }

    protected function pathMatchWeight(string $requestUri, array $itemsPerUseragent): int
    {
        $weight = 0;
        foreach ($itemsPerUseragent as $item => $strlen) {
            $startsWith = str_starts_with($requestUri, $item);
            if ($startsWith && $strlen > $weight) {
                $weight = (int) $strlen;
            }
        }

        return $weight;
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

    protected function pathIsExplicitAllowed(string $requestUri, array $allows): bool
    {
        return in_array($requestUri, array_keys($allows));
    }

    protected function pathIsDenied(string $requestUri, array $disallows): bool
    {
        foreach (array_keys($disallows) as $disallow) {
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
                $currentUserAgent[$disallowUrl] = strlen($disallowUrl);
            }
        }

        return $disallowsPerUserAgent;
    }

    protected function getAllowsPerUserAgent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines);

        $allowsPerUserAgent = [];

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
                $allowsPerUserAgent[$this->parseUserAgent($line)] = [];

                $currentUserAgents[] = &$allowsPerUserAgent[$this->parseUserAgent($line)];

                continue;
            }

            if ($this->isDisallowLine($line)) {
                $treatAllowDisallowLine = true;

                continue;
            }

            if ($this->isAllowLine($line)) {
                $treatAllowDisallowLine = true;

            }

            $allowUrl = $this->parseAllow($line);

            foreach ($currentUserAgents as &$currentUserAgent) {
                $currentUserAgent[$allowUrl] = strlen($allowUrl);
            }
        }
        $allowsPerUserAgent = array_filter($allowsPerUserAgent, function ($item) {
            return count($item) > 0;
        });

        return $allowsPerUserAgent;
    }

    protected function getCrawlDelaysPerUserAgent(string $content): array
    {
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines);

        $crawlDelaysPerUserAgent = [];

        $currentUserAgents = [];
        $isUserAgentListGoing = false;

        foreach ($lines as $line) {
            if ($this->isComment($line)) {
                continue;
            }

            if ($this->isEmptyLine($line)) {
                continue;
            }

            if ($this->isUserAgentLine($line)) {
                if (! $isUserAgentListGoing) {
                    $isUserAgentListGoing = true;
                    $currentUserAgents = [];
                }
                $userAgent = $this->parseUserAgent($line);

                $currentUserAgents[] = $userAgent;

                continue;
            }
            $isUserAgentListGoing = false;

            if ($this->isCrawlDelayLine($line)) {
                $crawlDelay = $this->parseCrawlDelay($line);
                foreach ($currentUserAgents as $currentUserAgent) {
                    $crawlDelaysPerUserAgent[$currentUserAgent] = $crawlDelay;
                }
            }

        }

        return $crawlDelaysPerUserAgent;
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
        return trim(substr_replace(trim($line), '', 0, 8), ': ');
    }

    protected function parseAllow(string $line): string
    {
        return trim(substr_replace(trim($line), '', 0, 6), ': ');
    }

    protected function parseCrawlDelay(string $line): string
    {
        return trim(substr_replace(trim($line), '', 0, 11), ': ');
    }

    protected function isDisallowLine(string $line): string
    {
        return trim(substr(str_replace(' ', '', strtolower(trim($line))), 0, 8), ': ') === 'disallow';
    }

    protected function isAllowLine(string $line): string
    {
        return trim(substr(str_replace(' ', '', strtolower(trim($line))), 0, 6), ': ') === 'allow';
    }

    protected function isCrawlDelayLine(string $line): string
    {
        return trim(substr(str_replace(' ', '', strtolower(trim($line))), 0, 11), ': ') === 'crawl-delay';
    }
}
