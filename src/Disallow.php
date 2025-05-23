<?php

namespace Spatie\Robots;

/**
 * A specific Disallow entry in a robots.txt
 */
class Disallow
{
    public function __construct(
        public readonly string $userAgent,
        public readonly string $basePath
    ) {}
}
