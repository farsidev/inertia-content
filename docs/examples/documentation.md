# Example: Documentation Site

Complete documentation site with navigation and search.

## Structure

```
resources/content/docs/
├── index.md
├── getting-started/
│   ├── installation.md
│   └── configuration.md
├── guide/
│   ├── basics.md
│   └── advanced.md
└── api/
    ├── classes.md
    └── functions.md
```

## Frontmatter

```markdown
---
title: Installation
description: Installation guide
navigation: true
order: 1
category: Getting Started
---
```

## Controller

```php
<?php

namespace App\Http\Controllers;

use Farsi\InertiaContent\Facades\Content;
use Inertia\Inertia;

class DocsController extends Controller
{
    public function index()
    {
        return Content::pageOrFail('docs/index', 'Docs/Page');
    }

    public function show($path = null)
    {
        $fullPath = $path ? "docs/$path" : 'docs/index';

        return Inertia::render('Docs/Page', [
            'contentKey' => $fullPath,
            'contentMeta' => Content::findOrFail($fullPath)->getMeta(),
            'navigation' => $this->getNavigation(),
        ]);
    }

    private function getNavigation()
    {
        return cache()->remember('docs_nav', 3600, function () {
            return Content::query()
                ->where('_dir', 'startsWith', 'docs')
                ->where('navigation', true)
                ->orderBy('order')
                ->get()
                ->toNavigation();
        });
    }
}
```

## Routes

```php
// routes/web.php
Route::get('/docs', [DocsController::class, 'index']);
Route::get('/docs/{path}', [DocsController::class, 'show'])
    ->where('path', '.*');
```

## Complete Layout

```vue
<!-- resources/js/Pages/Docs/Page.vue -->
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'
import { ref } from 'vue'
import DocsNavigation from '@/Components/DocsNavigation.vue'
import DocsTOC from '@/Components/DocsTOC.vue'

const props = defineProps<{
  contentKey: string
  contentMeta: any
  navigation: any[]
}>()

const sidebarOpen = ref(false)
</script>

<template>
  <div class="docs-layout">
    <!-- Header -->
    <header class="border-b bg-white sticky top-0 z-50">
      <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-bold">Documentation</Link>
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="md:hidden"
        >
          Menu
        </button>
      </div>
    </header>

    <div class="container mx-auto px-4 flex">
      <!-- Sidebar Navigation -->
      <aside
        class="w-64 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto border-r p-4"
        :class="{ hidden: !sidebarOpen, 'md:block': true }"
      >
        <DocsNavigation :items="navigation" />
      </aside>

      <!-- Main Content -->
      <main class="flex-1 px-8 py-8 max-w-4xl">
        <ContentDoc :content-key="contentKey">
          <template #header="{ meta }">
            <div class="mb-8">
              <div class="text-sm text-blue-600 mb-2">{{ meta.category }}</div>
              <h1 class="text-4xl font-bold">{{ meta.title }}</h1>
              <p class="text-xl text-gray-600 mt-2">{{ meta.description }}</p>
            </div>
          </template>

          <template #default="{ component }">
            <div class="prose prose-lg max-w-none">
              <component :is="component" />
            </div>
          </template>
        </ContentDoc>

        <!-- Prev/Next Navigation -->
        <div class="flex justify-between mt-12 pt-8 border-t">
          <Link
            v-if="contentMeta.prev"
            :href="`/docs/${contentMeta.prev}`"
            class="btn"
          >
            ← Previous
          </Link>
          <Link
            v-if="contentMeta.next"
            :href="`/docs/${contentMeta.next}`"
            class="btn ml-auto"
          >
            Next →
          </Link>
        </div>
      </main>

      <!-- TOC -->
      <aside class="hidden xl:block w-64 sticky top-16 h-[calc(100vh-4rem)] p-4">
        <DocsTOC :content-key="contentKey" />
      </aside>
    </div>
  </div>
</template>
```

## Navigation Component

```vue
<!-- resources/js/Components/DocsNavigation.vue -->
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  items: any[]
  level?: number
}>()
</script>

<template>
  <ul :class="level > 0 ? 'ml-4 mt-2' : ''">
    <li v-for="item in items" :key="item.path">
      <Link
        :href="`/${item.path}`"
        class="block py-2 px-3 rounded hover:bg-gray-100"
        :class="{ 'font-semibold': level === 0 }"
      >
        {{ item.title }}
      </Link>
      <DocsNavigation
        v-if="item.children?.length"
        :items="item.children"
        :level="(level || 0) + 1"
      />
    </li>
  </ul>
</template>
```

## TOC Component

```vue
<!-- resources/js/Components/DocsTOC.vue -->
<script setup lang="ts">
import { useContent } from '@inertia-content'
import { toRef } from 'vue'

const props = defineProps<{
  contentKey: string
}>()

const { headings } = useContent(toRef(props, 'contentKey'))
</script>

<template>
  <div v-if="headings.length">
    <h4 class="font-semibold mb-3">On this page</h4>
    <ul class="space-y-2 text-sm">
      <li v-for="h in headings" :key="h.id">
        <a
          :href="`#${h.id}`"
          class="text-gray-600 hover:text-gray-900"
        >
          {{ h.text }}
        </a>
        <ul v-if="h.children" class="ml-3 mt-1 space-y-1">
          <li v-for="child in h.children" :key="child.id">
            <a
              :href="`#${child.id}`"
              class="text-gray-500 hover:text-gray-700"
            >
              {{ child.text }}
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
```

## Search Component

```vue
<!-- components/DocsSearch.vue -->
<script setup lang="ts">
import { ref } from 'vue'

const query = ref('')
const results = ref([])

const search = async () => {
  if (!query.value) {
    results.value = []
    return
  }

  const response = await fetch(`/api/docs/search?q=${query.value}`)
  results.value = await response.json()
}
</script>

<template>
  <div class="relative">
    <input
      v-model="query"
      @input="search"
      type="search"
      placeholder="Search documentation..."
      class="w-full px-4 py-2 border rounded-lg"
    />
    <div v-if="results.length" class="absolute w-full mt-2 bg-white border rounded-lg shadow-lg">
      <Link
        v-for="result in results"
        :key="result._id"
        :href="`/${result._path}`"
        class="block p-3 hover:bg-gray-50"
      >
        <div class="font-semibold">{{ result.title }}</div>
        <div class="text-sm text-gray-600">{{ result._excerpt }}</div>
      </Link>
    </div>
  </div>
</template>
```

## Versioning

```php
// config/docs.php
return [
    'versions' => ['1.0', '2.0', '3.0'],
    'default' => '3.0'
];

// Controller
public function show($version = null, $path = null)
{
    $version = $version ?? config('docs.default');
    $fullPath = "docs/$version/" . ($path ?? 'index');

    return Inertia::render('Docs/Page', [
        'version' => $version,
        'versions' => config('docs.versions'),
        'contentKey' => $fullPath,
        'navigation' => $this->getNavigation($version),
    ]);
}
```

## Search API

```php
// routes/api.php
Route::get('/docs/search', function (Request $request) {
    $query = $request->input('q');

    if (!$query) {
        return response()->json([]);
    }

    $results = Content::query()
        ->where('_dir', 'startsWith', 'docs')
        ->where('draft', false)
        ->get()
        ->filter(fn($doc) =>
            str_contains(strtolower($doc->title), strtolower($query)) ||
            str_contains(strtolower($doc->_excerpt ?? ''), strtolower($query))
        )
        ->take(10);

    return response()->json($results->values());
});
```
