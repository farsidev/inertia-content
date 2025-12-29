<?php

namespace Farsi\InertiaContent\Support;

class PathResolver
{
    public function __construct(
        private string $contentDir
    ) {
    }

    /**
     * Resolve and normalize a content path.
     * Returns null if the path is invalid or contains path traversal attempts.
     */
    public function resolve(string $path): ?string
    {
        $normalized = $this->normalize($path);

        // Reject path traversal attempts
        if (str_contains($normalized, '..')) {
            return null;
        }

        // Remove leading slash
        $normalized = ltrim($normalized, '/');

        return $normalized;
    }

    /**
     * Normalize a path by removing extra slashes and dots.
     */
    public function normalize(string $path): string
    {
        // Replace backslashes with forward slashes
        $path = str_replace('\\', '/', $path);

        // Remove multiple consecutive slashes
        $path = preg_replace('#/+#', '/', $path);

        // Remove trailing slash
        $path = rtrim($path, '/');

        return $path;
    }

    /**
     * Get the full file system path for a content key.
     */
    public function getFilePath(string $path): ?string
    {
        $resolved = $this->resolve($path);

        if ($resolved === null) {
            return null;
        }

        $fullPath = $this->contentDir.'/'.$resolved.'.md';
        $realPath = realpath($fullPath);

        // Ensure the real path is within the content directory
        if ($realPath === false || ! str_starts_with($realPath, realpath($this->contentDir))) {
            return null;
        }

        return $realPath;
    }

    /**
     * Convert a file path to a content key.
     */
    public function fileToContentKey(string $filePath): string
    {
        $relativePath = str_replace(realpath($this->contentDir).'/', '', realpath($filePath));
        return $this->normalize(preg_replace('/\.md$/', '', $relativePath));
    }
}
