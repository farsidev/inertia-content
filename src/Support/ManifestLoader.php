<?php

namespace Farsi\InertiaContent\Support;

use Farsi\InertiaContent\Exceptions\ManifestNotFoundException;

class ManifestLoader
{
    private ?array $manifest = null;

    public function __construct(
        private string $manifestPath,
        private ContentCache $cache
    ) {
    }

    /**
     * Load the full manifest.
     *
     * @throws ManifestNotFoundException
     */
    public function load(): array
    {
        if ($this->manifest !== null) {
            return $this->manifest;
        }

        // Try to get from cache
        $cacheKey = 'manifest';
        if ($cached = $this->cache->get($cacheKey)) {
            $this->manifest = $cached;
            return $this->manifest;
        }

        // Load from file
        if (! file_exists($this->manifestPath)) {
            // In development, return empty manifest instead of throwing
            if (app()->environment('local')) {
                $this->manifest = [
                    'version' => 'dev',
                    'generatedAt' => time() * 1000,
                    'entries' => [],
                ];
                return $this->manifest;
            }

            throw new ManifestNotFoundException($this->manifestPath);
        }

        $content = file_get_contents($this->manifestPath);
        $this->manifest = json_decode($content, true);

        if (! $this->isValidManifest($this->manifest)) {
            throw new \RuntimeException('Invalid manifest structure');
        }

        // Cache it
        $this->cache->put($cacheKey, $this->manifest);

        return $this->manifest;
    }

    /**
     * Get a single entry by path.
     */
    public function getEntry(string $path): ?array
    {
        $manifest = $this->load();
        return $manifest['entries'][$path] ?? null;
    }

    /**
     * Check if a content path exists.
     */
    public function exists(string $path): bool
    {
        return $this->getEntry($path) !== null;
    }

    /**
     * Get the manifest version hash.
     */
    public function getVersion(): string
    {
        $manifest = $this->load();
        return $manifest['version'] ?? 'unknown';
    }

    /**
     * Get all content paths.
     */
    public function getAllPaths(): array
    {
        $manifest = $this->load();
        return array_keys($manifest['entries'] ?? []);
    }

    /**
     * Clear cached manifest.
     */
    public function clearCache(): void
    {
        $this->manifest = null;
        $this->cache->forget('manifest');
    }

    /**
     * Validate manifest structure.
     */
    private function isValidManifest(?array $manifest): bool
    {
        if ($manifest === null) {
            return false;
        }

        return isset($manifest['version'])
            && isset($manifest['generatedAt'])
            && isset($manifest['entries'])
            && is_array($manifest['entries']);
    }
}
