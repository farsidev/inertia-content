<?php

namespace Farsi\InertiaContent\Tests;

use Farsi\InertiaContent\InertiaContentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InertiaContentServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Content' => \Farsi\InertiaContent\Facades\Content::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default configuration
        config()->set('inertia-content.content_dir', __DIR__.'/fixtures/content');
        config()->set('inertia-content.manifest_path', __DIR__.'/fixtures/manifest.json');
        config()->set('inertia-content.cache.enabled', false);
    }
}

