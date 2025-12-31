<?php

namespace Farsi\InertiaContent;

use ArrayAccess;
use JsonSerializable;

class ContentEntry implements ArrayAccess, JsonSerializable
{
    private array $attributes;

    private function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Create a ContentEntry from a manifest entry.
     */
    public static function fromManifestEntry(array $entry): self
    {
        return new self($entry['meta'] ?? []);
    }

    /**
     * Get the unique identifier.
     */
    public function getId(): string
    {
        return $this->get('_id', '');
    }

    /**
     * Get the full content path.
     */
    public function getPath(): string
    {
        return $this->get('_path', '');
    }

    /**
     * Get the slug (last segment of path).
     */
    public function getSlug(): string
    {
        return $this->get('_slug', '');
    }

    /**
     * Get the directory path.
     */
    public function getDir(): string
    {
        return $this->get('_dir', '');
    }

    /**
     * Get the filename without extension.
     */
    public function getFile(): string
    {
        return $this->get('_file', '');
    }

    /**
     * Get the file extension.
     */
    public function getExtension(): string
    {
        return $this->get('_extension', 'md');
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->get('title', '');
    }

    /**
     * Get the description.
     */
    public function getDescription(): ?string
    {
        return $this->get('description');
    }

    /**
     * Get the excerpt.
     */
    public function getExcerpt(): ?string
    {
        return $this->get('_excerpt');
    }

    /**
     * Get the headings array.
     */
    public function getHeadings(): array
    {
        return $this->get('_headings', []);
    }

    /**
     * Get the order.
     */
    public function getOrder(): ?int
    {
        return $this->get('order');
    }

    /**
     * Get the content hash.
     */
    public function getHash(): string
    {
        return $this->get('_hash', '');
    }

    /**
     * Get the created timestamp.
     */
    public function getCreatedAt(): ?int
    {
        return $this->get('_createdAt');
    }

    /**
     * Get the updated timestamp.
     */
    public function getUpdatedAt(): ?int
    {
        return $this->get('_updatedAt');
    }

    /**
     * Check if the entry is a draft.
     */
    public function isDraft(): bool
    {
        return $this->get('draft', false) === true;
    }

    /**
     * Check if the entry should appear in navigation.
     */
    public function isNavigable(): bool
    {
        return $this->get('navigation', true) !== false;
    }

    /**
     * Get a specific attribute.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Check if an attribute exists.
     */
    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get all attributes as array.
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Get metadata (all attributes).
     */
    public function getMeta(): array
    {
        return $this->attributes;
    }

    /**
     * Convert to Inertia props format.
     */
    public function toInertiaProps(): array
    {
        return [
            'contentKey' => $this->getPath(),
            'contentMeta' => $this->getMeta(),
        ];
    }

    /**
     * Magic getter for attributes.
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Magic isset for attributes.
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * ArrayAccess: Check if offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * ArrayAccess: Get offset value.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * ArrayAccess: Set offset value (not allowed).
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('ContentEntry is immutable');
    }

    /**
     * ArrayAccess: Unset offset (not allowed).
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('ContentEntry is immutable');
    }

    /**
     * JsonSerializable: Specify data which should be serialized to JSON.
     */
    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }
}

