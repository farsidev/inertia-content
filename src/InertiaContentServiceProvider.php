<?php

namespace Farsi\InertiaContent;

use Farsi\InertiaContent\Support\ContentCache;
use Farsi\InertiaContent\Support\ManifestLoader;
use Farsi\InertiaContent\Support\PathResolver;
use Illuminate\Support\ServiceProvider;

class InertiaContentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/inertia-content.php',
            'inertia-content'
        );

        $this->app->singleton('inertia-content', function ($app) {
            return new ContentManager(
                $app->make(ManifestLoader::class),
                $app->make(ContentCache::class),
                $app->make(PathResolver::class)
            );
        });

        $this->app->alias('inertia-content', ContentManager::class);

        $this->app->singleton(ManifestLoader::class, function ($app) {
            return new ManifestLoader(
                config('inertia-content.manifest_path'),
                $app->make(ContentCache::class)
            );
        });

        $this->app->singleton(ContentCache::class, function ($app) {
            return new ContentCache(config('inertia-content.cache'));
        });

        $this->app->singleton(PathResolver::class, function ($app) {
            return new PathResolver(config('inertia-content.content_dir'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/inertia-content.php' => config_path('inertia-content.php'),
        ], 'inertia-content-config');

        // Publish stubs
        $this->publishes([
            __DIR__.'/../resources/stubs/content' => resource_path('content'),
        ], 'inertia-content-stubs');

        $this->publishes([
            __DIR__.'/../resources/stubs/Pages' => resource_path('js/Pages/Content'),
        ], 'inertia-content-pages');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\ClearCacheCommand::class,
            ]);
        }

        // Load routes if enabled
        if (config('inertia-content.routes.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/inertia-content.php');
        }
    }
}

