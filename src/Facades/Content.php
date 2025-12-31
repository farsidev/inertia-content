<?php

namespace Farsi\InertiaContent\Facades;

use Farsi\InertiaContent\ContentCollection;
use Farsi\InertiaContent\ContentEntry;
use Farsi\InertiaContent\ContentQuery;
use Illuminate\Support\Facades\Facade;
use Inertia\Response;

/**
 * @method static ContentEntry|null find(string $path)
 * @method static ContentEntry findOrFail(string $path)
 * @method static bool exists(string $path)
 * @method static ContentQuery query()
 * @method static Response|null page(string $path, ?string $component = null)
 * @method static Response pageOrFail(string $path, ?string $component = null)
 * @method static array navigation(?string $dir = null)
 * @method static void clearCache()
 * @method static string getVersion()
 *
 * @see \Farsi\InertiaContent\ContentManager
 */
class Content extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'inertia-content';
    }
}

