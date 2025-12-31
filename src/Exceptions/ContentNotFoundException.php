<?php

namespace Farsi\InertiaContent\Exceptions;

use Exception;

class ContentNotFoundException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("Content not found: {$path}");
    }
}

