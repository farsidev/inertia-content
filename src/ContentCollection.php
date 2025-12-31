<?php

namespace Farsi\InertiaContent;

use Illuminate\Support\Collection;

class ContentCollection extends Collection
{
    /**
     * Get just the paths from the entries.
     */
    public function paths(): array
    {
        return $this->map(fn (ContentEntry $entry) => $entry->getPath())->all();
    }

    /**
     * Convert to navigation tree structure.
     */
    public function toNavigation(): array
    {
        $tree = [];

        foreach ($this->items as $entry) {
            if (! $entry->isNavigable()) {
                continue;
            }

            $parts = explode('/', $entry->getDir());
            $current = &$tree;

            foreach ($parts as $part) {
                if (empty($part)) {
                    continue;
                }

                if (! isset($current[$part])) {
                    $current[$part] = ['children' => []];
                }

                $current = &$current[$part]['children'];
            }

            $current[] = [
                'title' => $entry->getTitle(),
                'path' => $entry->getPath(),
                'order' => $entry->getOrder(),
                'description' => $entry->getDescription(),
            ];
        }

        return $this->sortNavigationTree($tree);
    }

    /**
     * Group entries by directory.
     */
    public function groupByDir(): self
    {
        return $this->groupBy(fn (ContentEntry $entry) => $entry->getDir());
    }

    /**
     * Filter drafts based on visibility setting.
     */
    public function filterDrafts(bool $includeDrafts = false): self
    {
        if ($includeDrafts) {
            return $this;
        }

        return $this->filter(fn (ContentEntry $entry) => ! $entry->isDraft());
    }

    /**
     * Convert all entries to Inertia props format.
     */
    public function toInertiaProps(): array
    {
        return $this->map(fn (ContentEntry $entry) => $entry->getMeta())->all();
    }

    /**
     * Sort navigation tree by order field.
     */
    private function sortNavigationTree(array $tree): array
    {
        foreach ($tree as &$node) {
            if (isset($node['children']) && is_array($node['children'])) {
                $node['children'] = $this->sortNavigationTree($node['children']);

                usort($node['children'], function ($a, $b) {
                    $orderA = $a['order'] ?? 999;
                    $orderB = $b['order'] ?? 999;

                    if ($orderA === $orderB) {
                        return ($a['title'] ?? '') <=> ($b['title'] ?? '');
                    }

                    return $orderA <=> $orderB;
                });
            }
        }

        return $tree;
    }
}

