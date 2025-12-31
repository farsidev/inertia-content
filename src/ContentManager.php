<?php

namespace Farsi\InertiaContent;

use Farsi\InertiaContent\Exceptions\ContentNotFoundException;
use Farsi\InertiaContent\Support\ContentCache;
use Farsi\InertiaContent\Support\ManifestLoader;
use Farsi\InertiaContent\Support\PathResolver;
use Inertia\Inertia;
use Inertia\Response;

class ContentManager
{
    public function __construct(
        private ManifestLoader $loader,
        private ContentCache $cache,
        private PathResolver $resolver
    ) {
    }

    /**
     * Find a content entry by path.
     */
    public function find(string $path): ?ContentEntry
    {
        $resolved = $this->resolver->resolve($path);

        if ($resolved === null) {
            return null;
        }

        $manifestEntry = $this->loader->getEntry($resolved);

        if ($manifestEntry === null) {
            return null;
        }

        $entry = ContentEntry::fromManifestEntry($manifestEntry);

        // Filter drafts based on config
        if ($entry->isDraft() && ! config('inertia-content.show_drafts', false)) {
            return null;
        }

        return $entry;
    }

    /**
     * Find a content entry or throw 404.
     *
     * @throws ContentNotFoundException
     */
    public function findOrFail(string $path): ContentEntry
    {
        $entry = $this->find($path);

        if ($entry === null) {
            throw new ContentNotFoundException($path);
        }

        return $entry;
    }

    /**
     * Check if content exists.
     */
    public function exists(string $path): bool
    {
        return $this->find($path) !== null;
    }

    /**
     * Create a query builder.
     */
    public function query(): ContentQuery
    {
        return new ContentQuery(
            $this->loader,
            config('inertia-content.show_drafts', false)
        );
    }

    /**
     * Render content page via Inertia.
     */
    public function page(string $path, ?string $component = null): ?Response
    {
        $entry = $this->find($path);

        if ($entry === null) {
            return null;
        }

        return Inertia::render(
            $component ?? config('inertia-content.default_component', 'Content/Page'),
            [
                'contentKey' => $entry->getPath(),
                'contentMeta' => $entry->getMeta(),
                'contentVersion' => $this->getVersion(),
            ]
        );
    }

    /**
     * Render content page or throw 404.
     *
     * @throws ContentNotFoundException
     */
    public function pageOrFail(string $path, ?string $component = null): Response
    {
        $response = $this->page($path, $component);

        if ($response === null) {
            throw new ContentNotFoundException($path);
        }

        return $response;
    }

    /**
     * Get navigation tree for a directory.
     */
    public function navigation(?string $dir = null): array
    {
        $query = $this->query()
            ->where('navigation', '!=', false)
            ->orderBy('order', 'asc')
            ->orderBy('title', 'asc');

        if ($dir !== null) {
            $query = $query->where('_dir', 'startsWith', $dir);
        }

        return $query->get()->toNavigation();
    }

    /**
     * Clear all caches.
     */
    public function clearCache(): void
    {
        $this->loader->clearCache();
        $this->cache->flush();
    }

    /**
     * Get manifest version.
     */
    public function getVersion(): string
    {
        return $this->loader->getVersion();
    }
}

