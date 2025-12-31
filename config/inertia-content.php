<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Directory
    |--------------------------------------------------------------------------
    |
    | The directory where your markdown content files are stored.
    | This path is relative to your application's base path.
    |
    */

    'content_dir' => resource_path('content'),

    /*
    |--------------------------------------------------------------------------
    | Manifest Path
    |--------------------------------------------------------------------------
    |
    | Location of the generated manifest file created during build.
    | This file contains metadata for all compiled content entries.
    |
    */

    'manifest_path' => public_path('build/inertia-content-manifest.json'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how content manifest and queries are cached.
    | Set 'enabled' to false to disable caching during development.
    |
    */

    'cache' => [
        'enabled' => env('INERTIA_CONTENT_CACHE', true),
        'store' => env('INERTIA_CONTENT_CACHE_STORE', 'file'),
        'ttl' => 3600, // 1 hour
        'prefix' => 'inertia_content_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Enable automatic content routing. When enabled, the package will
    | register catch-all routes for serving content pages.
    |
    */

    'routes' => [
        'enabled' => false, // User should define their own routes
        'prefix' => '',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Draft Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, content marked as draft will be accessible.
    | Useful during development. Should be false in production.
    |
    */

    'show_drafts' => env('INERTIA_CONTENT_SHOW_DRAFTS', false),

    /*
    |--------------------------------------------------------------------------
    | Default Component
    |--------------------------------------------------------------------------
    |
    | The default Vue component to render content pages when using
    | Content::page() without specifying a component.
    |
    */

    'default_component' => 'Content/Page',

    /*
    |--------------------------------------------------------------------------
    | Heading Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which heading levels to extract and index.
    |
    */

    'headings' => [
        'depth' => [2, 3, 4], // H2, H3, H4
    ],

];

