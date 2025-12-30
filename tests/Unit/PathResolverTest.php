<?php

use Farsi\InertiaContent\Support\PathResolver;

beforeEach(function () {
    $this->resolver = new PathResolver(__DIR__.'/../fixtures/content');
});

test('resolves valid paths', function () {
    $resolved = $this->resolver->resolve('docs/intro');

    expect($resolved)->toBe('docs/intro');
});

test('normalizes paths with slashes', function () {
    $resolved = $this->resolver->resolve('/docs/intro/');

    expect($resolved)->toBe('docs/intro');
});

test('rejects path traversal attempts', function () {
    $resolved = $this->resolver->resolve('../../../etc/passwd');

    expect($resolved)->toBeNull();
});

test('rejects paths with double dots', function () {
    $resolved = $this->resolver->resolve('docs/../config');

    expect($resolved)->toBeNull();
});

test('normalizes multiple slashes', function () {
    $path = $this->resolver->normalize('docs//intro///page');

    expect($path)->toBe('docs/intro/page');
});
