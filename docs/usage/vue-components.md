# Vue Components & Composables

Complete guide to Vue components and composables.

## useContent Composable

### Basic Usage

```vue
<script setup lang="ts">
import { useContent } from '@inertia-content'

const { component, meta, headings, isLoading, error } =
  useContent('docs/intro')
</script>

<template>
  <div v-if="isLoading">Loading...</div>
  <div v-else-if="error">Error: {{ error.message }}</div>
  <div v-else>
    <h1>{{ meta.title }}</h1>
    <component :is="component" />
  </div>
</template>
```

### Reactive Key

```vue
<script setup lang="ts">
import { ref } from 'vue'
import { useContent } from '@inertia-content'

const path = ref('docs/intro')
const { component, meta } = useContent(path)

// Change path - auto reloads
path.value = 'docs/guide'
</script>
```

### Return Values

```typescript
const {
  component,    // Vue component (reactive)
  meta,         // ContentEntry metadata
  headings,     // Heading[] for TOC
  isLoading,    // Loading state
  error,        // Error | null
  refresh       // () => Promise<void>
} = useContent(contentKey)
```

## ContentRenderer

Simplest way to render content.

### Basic Usage

```vue
<script setup lang="ts">
import { ContentRenderer } from '@inertia-content'
</script>

<template>
  <ContentRenderer :content-key="$page.props.contentKey" />
</template>
```

### With Props

```vue
<ContentRenderer
  :content-key="contentKey"
  prose-class="prose prose-lg"
  tag="article"
/>
```

### Slots

```vue
<ContentRenderer :content-key="contentKey">
  <!-- Loading -->
  <template #loading>
    <div class="spinner">Loading...</div>
  </template>

  <!-- Error -->
  <template #error="{ error }">
    <div class="alert-error">{{ error.message }}</div>
  </template>

  <!-- Not Found -->
  <template #not-found>
    <div>Content not found</div>
  </template>

  <!-- Content (override) -->
  <template #default="{ component, meta }">
    <h1>{{ meta.title }}</h1>
    <component :is="component" class="prose" />
  </template>
</ContentRenderer>
```

## ContentDoc

Full document display with header, TOC, content, and footer.

### Basic Usage

```vue
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'
</script>

<template>
  <ContentDoc :content-key="$page.props.contentKey" />
</template>
```

### All Slots

```vue
<ContentDoc :content-key="contentKey" tag="article">
  <!-- Header -->
  <template #header="{ meta }">
    <header>
      <h1>{{ meta.title }}</h1>
      <p>{{ meta.description }}</p>
      <time>{{ new Date(meta._updatedAt).toLocaleDateString() }}</time>
    </header>
  </template>

  <!-- Table of Contents -->
  <template #toc="{ headings }">
    <nav class="toc">
      <h4>On this page</h4>
      <ul>
        <li v-for="h in headings" :key="h.id">
          <a :href="`#${h.id}`">{{ h.text }}</a>
          <!-- Nested headings -->
          <ul v-if="h.children">
            <li v-for="child in h.children" :key="child.id">
              <a :href="`#${child.id}`">{{ child.text }}</a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </template>

  <!-- Content -->
  <template #default="{ component }">
    <component :is="component" class="prose prose-lg" />
  </template>

  <!-- Footer -->
  <template #footer="{ meta }">
    <footer>
      <p>Author: {{ meta.author }}</p>
      <p>Last updated: {{ formatDate(meta._updatedAt) }}</p>
    </footer>
  </template>
</ContentDoc>
```

## ContentList

Display list of content entries.

### Basic Usage

```vue
<script setup lang="ts">
import { ContentList } from '@inertia-content'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <ContentList :entries="$page.props.entries">
    <template #default="{ entries }">
      <article v-for="entry in entries" :key="entry._id">
        <Link :href="`/${entry._path}`">
          <h2>{{ entry.title }}</h2>
          <p>{{ entry._excerpt }}</p>
        </Link>
      </article>
    </template>
  </ContentList>
</template>
```

### Grid Layout

```vue
<ContentList :entries="entries" tag="div">
  <template #default="{ entries }">
    <div class="grid grid-cols-3 gap-4">
      <div v-for="entry in entries" :key="entry._id" class="card">
        <h3>{{ entry.title }}</h3>
        <p>{{ entry.description }}</p>
        <Link :href="`/${entry._path}`">Read more</Link>
      </div>
    </div>
  </template>

  <template #empty>
    <p>No content found</p>
  </template>
</ContentList>
```

### Item Slot

```vue
<ContentList :entries="entries">
  <template #item="{ entry, index }">
    <article :class="{ featured: index === 0 }">
      <span class="badge">{{ entry._dir }}</span>
      <h2>{{ entry.title }}</h2>
      <p>{{ entry._excerpt }}</p>
      <time>{{ formatDate(entry._createdAt) }}</time>
    </article>
  </template>
</ContentList>
```

## TypeScript Types

```typescript
import type {
  ContentEntry,
  Heading,
  ContentManifest
} from '@inertia-content'

// ContentEntry
interface ContentEntry {
  _id: string
  _path: string
  _slug: string
  _dir: string
  title: string
  description?: string
  _excerpt?: string
  _headings: Heading[]
  draft?: boolean
  navigation?: boolean
  order?: number
  // ... custom fields
}

// Heading
interface Heading {
  depth: 2 | 3 | 4
  text: string
  id: string
  children?: Heading[]
}
```

## Complete Examples

### Blog Post Page

```vue
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'

const formatDate = (timestamp: number) => {
  return new Date(timestamp).toLocaleDateString()
}
</script>

<template>
  <div class="container">
    <ContentDoc :content-key="$page.props.contentKey">
      <template #header="{ meta }">
        <header class="mb-8">
          <h1 class="text-4xl font-bold">{{ meta.title }}</h1>
          <div class="flex gap-4 mt-4 text-gray-600">
            <span>{{ meta.author }}</span>
            <time>{{ formatDate(meta._createdAt) }}</time>
          </div>
        </header>
      </template>

      <template #default="{ component }">
        <component :is="component" class="prose prose-lg" />
      </template>
    </ContentDoc>
  </div>
</template>
```

### Posts List with Pagination

```vue
<script setup lang="ts">
import { ContentList } from '@inertia-content'
import { computed } from 'vue'

const props = defineProps<{
  entries: ContentEntry[]
  page: number
  perPage: number
}>()

const paginatedEntries = computed(() => {
  const start = (props.page - 1) * props.perPage
  return props.entries.slice(start, start + props.perPage)
})
</script>

<template>
  <ContentList :entries="paginatedEntries">
    <template #default="{ entries }">
      <article v-for="entry in entries" :key="entry._id">
        <h2>{{ entry.title }}</h2>
        <p>{{ entry._excerpt }}</p>
      </article>
    </template>
  </ContentList>

  <div class="pagination">
    <!-- pagination controls -->
  </div>
</template>
```

## HMR Support

The composable and components automatically support Hot Module Replacement:

```vue
<script setup lang="ts">
// HMR works automatically
const { component, meta } = useContent('docs/intro')

// When you edit docs/intro.md, the component auto-updates
// No page reload needed!
</script>
```

This works in development mode when you:
1. Edit a Markdown file
2. Save the file
3. See instant updates in the browser
