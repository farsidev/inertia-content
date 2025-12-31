<?php

namespace Farsi\InertiaContent\Support;

use Illuminate\Support\Facades\Cache;

class ContentCache
{
    private bool $enabled;
    private string $store;
    private int $ttl;
    private string $prefix;

    public function __construct(array $config)
    {
        $this->enabled = $config['enabled'] ?? true;
        $this->store = $config['store'] ?? 'file';
        $this->ttl = $config['ttl'] ?? 3600;
        $this->prefix = $config['prefix'] ?? 'inertia_content_';
    }

    /**
     * Get a value from cache.
     */
    public function get(string $key): mixed
    {
        if (! $this->enabled) {
            return null;
        }

        return Cache::store($this->store)->get($this->makeCacheKey($key));
    }

    /**
     * Put a value in cache.
     */
    public function put(string $key, mixed $value): void
    {
        if (! $this->enabled) {
            return;
        }

        Cache::store($this->store)->put(
            $this->makeCacheKey($key),
            $value,
            $this->ttl
        );
    }

    /**
     * Remove a value from cache.
     */
    public function forget(string $key): void
    {
        if (! $this->enabled) {
            return;
        }

        Cache::store($this->store)->forget($this->makeCacheKey($key));
    }

    /**
     * Clear all content cache.
     */
    public function flush(): void
    {
        if (! $this->enabled) {
            return;
        }

        // This will only work with file/array cache stores
        // For other stores, you may need to implement a different strategy
        Cache::store($this->store)->flush();
    }

    /**
     * Make a cache key with prefix.
     */
    private function makeCacheKey(string $key): string
    {
        return $this->prefix.$key;
    }
}

