<?php

namespace Farsi\InertiaContent\Exceptions;

use Exception;

class ManifestNotFoundException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("Content manifest not found at: {$path}. Have you run 'npm run build'?");
    }
}
