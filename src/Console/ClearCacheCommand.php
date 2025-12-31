<?php

namespace Farsi\InertiaContent\Console;

use Farsi\InertiaContent\Facades\Content;
use Illuminate\Console\Command;

class ClearCacheCommand extends Command
{
    protected $signature = 'inertia-content:clear';

    protected $description = 'Clear Inertia Content cache';

    public function handle(): int
    {
        Content::clearCache();

        $this->info('Content cache cleared successfully.');

        return self::SUCCESS;
    }
}

