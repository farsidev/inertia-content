<?php

use Farsi\InertiaContent\ContentEntry;

test('creates entry from manifest data', function () {
    $data = [
        'path' => 'docs/intro',
        'meta' => [
            '_id' => 'abc123',
            '_path' => 'docs/intro',
            '_slug' => 'intro',
            '_dir' => 'docs',
            '_file' => 'intro',
            '_extension' => 'md',
            'title' => 'Introduction',
            'description' => 'Getting started guide',
            'draft' => false,
            'navigation' => true,
            '_excerpt' => 'Welcome to...',
            '_headings' => [],
            '_hash' => 'sha256hash',
            '_createdAt' => 1703836800000,
            '_updatedAt' => 1703836800000,
        ],
        'chunk' => 'content-abc123.js',
    ];

    $entry = ContentEntry::fromManifestEntry($data);

    expect($entry->getPath())->toBe('docs/intro')
        ->and($entry->getSlug())->toBe('intro')
        ->and($entry->getDir())->toBe('docs')
        ->and($entry->getTitle())->toBe('Introduction')
        ->and($entry->isDraft())->toBeFalse();
});

test('provides array access', function () {
    $entry = ContentEntry::fromManifestEntry([
        'path' => 'test',
        'meta' => [
            '_path' => 'test',
            '_slug' => 'test',
            'title' => 'Test',
        ],
        'chunk' => 'test.js',
    ]);

    expect($entry['title'])->toBe('Test')
        ->and($entry['_path'])->toBe('test')
        ->and(isset($entry['title']))->toBeTrue()
        ->and(isset($entry['nonexistent']))->toBeFalse();
});

test('generates inertia props', function () {
    $entry = ContentEntry::fromManifestEntry([
        'path' => 'docs/intro',
        'meta' => [
            '_path' => 'docs/intro',
            '_slug' => 'intro',
            'title' => 'Intro',
            'description' => 'Desc',
        ],
        'chunk' => 'test.js',
    ]);

    $props = $entry->toInertiaProps();

    expect($props)->toHaveKey('contentKey')
        ->and($props)->toHaveKey('contentMeta')
        ->and($props['contentKey'])->toBe('docs/intro');
});

