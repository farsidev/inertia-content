<?php

namespace Farsi\InertiaContent;

use Farsi\InertiaContent\Support\ManifestLoader;

class ContentQuery
{
    private array $wheres = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $skip = null;
    private array $only = [];
    private array $without = [];

    public function __construct(
        private ManifestLoader $loader,
        private bool $showDrafts = false
    ) {
    }

    /**
     * Filter by field value.
     */
    public function where(string $field, mixed $operatorOrValue, mixed $value = null): self
    {
        $clone = clone $this;

        if ($value === null) {
            $operator = '=';
            $value = $operatorOrValue;
        } else {
            $operator = $operatorOrValue;
        }

        $clone->wheres[] = compact('field', 'operator', 'value');

        return $clone;
    }

    /**
     * Filter by field in array of values.
     */
    public function whereIn(string $field, array $values): self
    {
        $clone = clone $this;
        $clone->wheres[] = [
            'field' => $field,
            'operator' => 'in',
            'value' => $values,
        ];

        return $clone;
    }

    /**
     * Filter by field not equal to value.
     */
    public function whereNot(string $field, mixed $value): self
    {
        return $this->where($field, '!=', $value);
    }

    /**
     * Select only specific fields.
     */
    public function only(array $fields): self
    {
        $clone = clone $this;
        $clone->only = $fields;

        return $clone;
    }

    /**
     * Exclude specific fields.
     */
    public function without(array $fields): self
    {
        $clone = clone $this;
        $clone->without = $fields;

        return $clone;
    }

    /**
     * Sort results.
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $clone = clone $this;
        $clone->orderBy[] = compact('field', 'direction');

        return $clone;
    }

    /**
     * Limit number of results.
     */
    public function limit(int $count): self
    {
        $clone = clone $this;
        $clone->limit = $count;

        return $clone;
    }

    /**
     * Skip first N results.
     */
    public function skip(int $count): self
    {
        $clone = clone $this;
        $clone->skip = $count;

        return $clone;
    }

    /**
     * Execute query and return collection.
     */
    public function get(): ContentCollection
    {
        $entries = $this->executeQuery();

        return new ContentCollection($entries);
    }

    /**
     * Get first result or null.
     */
    public function first(): ?ContentEntry
    {
        $results = $this->limit(1)->get();

        return $results->first();
    }

    /**
     * Get count of matching entries.
     */
    public function count(): int
    {
        return count($this->executeQuery());
    }

    /**
     * Get just the paths of matching entries.
     */
    public function paths(): array
    {
        return array_map(
            fn (ContentEntry $entry) => $entry->getPath(),
            $this->executeQuery()
        );
    }

    /**
     * Execute the query and return entries.
     */
    private function executeQuery(): array
    {
        $manifest = $this->loader->load();
        $entries = [];

        foreach ($manifest['entries'] as $path => $manifestEntry) {
            $entry = ContentEntry::fromManifestEntry($manifestEntry);

            // Filter drafts
            if (! $this->showDrafts && $entry->isDraft()) {
                continue;
            }

            // Apply where clauses
            if (! $this->matchesWheres($entry)) {
                continue;
            }

            $entries[] = $entry;
        }

        // Apply ordering
        $entries = $this->applyOrdering($entries);

        // Apply skip/limit
        if ($this->skip !== null) {
            $entries = array_slice($entries, $this->skip);
        }

        if ($this->limit !== null) {
            $entries = array_slice($entries, 0, $this->limit);
        }

        return $entries;
    }

    /**
     * Check if entry matches all where clauses.
     */
    private function matchesWheres(ContentEntry $entry): bool
    {
        foreach ($this->wheres as $where) {
            if (! $this->matchesWhere($entry, $where)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if entry matches a single where clause.
     */
    private function matchesWhere(ContentEntry $entry, array $where): bool
    {
        $field = $where['field'];
        $operator = $where['operator'];
        $value = $where['value'];

        $entryValue = $entry->get($field);

        return match ($operator) {
            '=' => $entryValue == $value,
            '!=' => $entryValue != $value,
            '>' => $entryValue > $value,
            '<' => $entryValue < $value,
            '>=' => $entryValue >= $value,
            '<=' => $entryValue <= $value,
            'in' => in_array($entryValue, $value),
            'contains' => is_string($entryValue) && str_contains($entryValue, $value),
            'startsWith' => is_string($entryValue) && str_starts_with($entryValue, $value),
            'endsWith' => is_string($entryValue) && str_ends_with($entryValue, $value),
            default => false,
        };
    }

    /**
     * Apply ordering to entries.
     */
    private function applyOrdering(array $entries): array
    {
        if (empty($this->orderBy)) {
            return $entries;
        }

        usort($entries, function (ContentEntry $a, ContentEntry $b) {
            foreach ($this->orderBy as $order) {
                $field = $order['field'];
                $direction = strtolower($order['direction']);

                $aValue = $a->get($field);
                $bValue = $b->get($field);

                $comparison = $aValue <=> $bValue;

                if ($comparison !== 0) {
                    return $direction === 'desc' ? -$comparison : $comparison;
                }
            }

            return 0;
        });

        return $entries;
    }
}
