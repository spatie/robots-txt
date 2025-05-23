<?php

namespace Spatie\Robots;

class WhyDisallows
{
    /**
     * @param  Disallow[]  $reasons
     */
    public function __construct(public readonly array $reasons) {}
}
