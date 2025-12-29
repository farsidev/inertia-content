# Quick Start

Get up and running with Inertia Content in 5 minutes.

## Installation

```bash
# 1. Install package
composer require farsi/inertia-content

# 2. Install JavaScript dependencies
cd vendor/farsi/inertia-content && npm install && cd -

# 3. Run installer
php artisan inertia-content:install
```

## Setup

### 1. Add Vite Plugin

```typescript
// vite.config.ts
import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
  plugins: [
    laravel({ input: ['resources/js/app.ts'] }),
    vue(),
    inertiaContent(), // ✅ Add this
  ],
  resolve: {
    alias: {
      '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
    }
  }
})
```

### 2. Create Content

```markdown
<!-- resources/content/hello.md -->
---
title: Hello World
description: My first content page
---

## Welcome

This is my first content page!
```

### 3. Add Route

```php
// routes/web.php
use Farsi\InertiaContent\Facades\Content;

Route::get('/{path?}', function ($path = 'index') {
    return Content::pageOrFail($path);
})->where('path', '.*');
```

### 4. Display Content

```vue
<!-- resources/js/Pages/ContentPage.vue -->
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'
import { usePage } from '@inertiajs/vue3'
</script>

<template>
  <ContentDoc :content-key="$page.props.contentKey" />
</template>
```

### 5. Build and Run

```bash
npm run build
npm run dev
```

✅ **Done!** Visit `http://localhost:5173/hello`

---

## Complete Example

### PHP (Laravel)

```php
// List content
$docs = Content::query()
    ->where('_dir', 'docs')
    ->orderBy('order')
    ->get();

return Inertia::render('Docs/Index', [
    'entries' => $docs->toInertiaProps()
]);
```

### Vue

```vue
<script setup lang="ts">
import { ContentList } from '@inertia-content'
</script>

<template>
  <ContentList :entries="$page.props.entries">
    <template #default="{ entries }">
      <article v-for="entry in entries" :key="entry._id">
        <Link :href="`/${entry._path}`">
          <h2>{{ entry.title }}</h2>
        </Link>
      </article>
    </template>
  </ContentList>
</template>
```

---

## Next Steps

- [Complete Installation](./installation.md)
- [PHP API](./usage/php-api.md)
- [Vue Components](./usage/vue-components.md)
- [Examples](./examples/)
