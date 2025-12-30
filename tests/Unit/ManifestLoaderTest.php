<?php

use Farsi\InertiaContent\Support\ManifestLoader;
use Farsi\InertiaContent\Support\ContentCache;
use Farsi\InertiaContent\Exceptions\ManifestNotFoundException;

beforeEach(function () {
    $this->cache = new ContentCache(['enabled' => false]);
});

test('loads valid manifest', function () {
    $manifestPath = __DIR__.'/../fixtures/valid-manifest.json';
    $loader = new ManifestLoader($manifestPath, $this->cache);

    $manifest = $loader->load();

    expect($manifest)->toBeArray()
        ->and($manifest)->toHaveKey('version')
        ->and($manifest)->toHaveKey('entries');
});

test('throws exception for missing manifest in production', function () {
    app()->detectEnvironment(fn () => 'production');

    $loader = new ManifestLoader('/nonexistent/manifest.json', $this->cache);

    $loader->load();
})->throws(ManifestNotFoundException::class);

test('returns empty manifest in local environment', function () {
    app()->detectEnvironment(fn () => 'local');

    $loader = new ManifestLoader('/nonexistent/manifest.json', $this->cache);
    $manifest = $loader->load();

    expect($manifest)->toBeArray()
        ->and($manifest['entries'])->toBeEmpty();
});

test('gets single entry', function () {
    $manifestPath = __DIR__.'/../fixtures/valid-manifest.json';
    $loader = new ManifestLoader($manifestPath, $this->cache);

    $entry = $loader->getEntry('docs/intro');

    expect($entry)->toBeArray()
        ->and($entry)->toHaveKey('path')
        ->and($entry['path'])->toBe('docs/intro');
});

test('checks entry existence', function () {
    $manifestPath = __DIR__.'/../fixtures/valid-manifest.json';
    $loader = new ManifestLoader($manifestPath, $this->cache);

    expect($loader->exists('docs/intro'))->toBeTrue()
        ->and($loader->exists('nonexistent'))->toBeFalse();
});
