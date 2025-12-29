# Inertia Content

[![Latest Version on Packagist](https://img.shields.io/packagist/v/farsi/inertia-content.svg?style=flat-square)](https://packagist.org/packages/farsi/inertia-content)
[![GitHub Tests Status](https://img.shields.io/github/actions/workflow/status/farsidev/inertia-content/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farsidev/inertia-content/actions/workflows/tests.yml)
[![Code Coverage](https://img.shields.io/codecov/c/github/farsidev/inertia-content?style=flat-square)](https://codecov.io/gh/farsidev/inertia-content)
[![Total Downloads](https://img.shields.io/packagist/dt/farsi/inertia-content.svg?style=flat-square)](https://packagist.org/packages/farsi/inertia-content)
[![License](https://img.shields.io/packagist/l/farsi/inertia-content?style=flat-square)](https://packagist.org/packages/farsi/inertia-content)

A **Nuxt Content-compatible** content management system for Laravel + Inertia.js + Vue applications. Write Markdown, get Vue components, query with Laravel – all with build-time compilation and zero runtime overhead.

## Why Inertia Content?

Traditional CMS solutions force you to pass HTML through Inertia props or parse Markdown at runtime. Inertia Content takes a different approach: **compile Markdown to Vue components at build time**, while Laravel maintains full control over routing and access.

### Key Benefits

- 📝 **File-based content** - Write Markdown files, get compiled Vue components
- ⚡ **Build-time compilation** - Zero runtime parsing, optimal performance
- 🔍 **Powerful queries** - Nuxt Content-style query API in PHP
- 🎯 **Full TypeScript support** - Type safety from PHP to Vue
- 🔥 **Hot Module Replacement** - Instant updates during development
- 🎨 **Familiar API** - If you know Nuxt Content, you already know this
- 🔐 **Laravel-first** - Server-side access control and routing
- 🚀 **Production-ready** - Caching, optimization, and security built-in

## Installation

Inertia Content is a **single Composer package** that includes both PHP and JavaScript code.

```bash
# 1. Install via Composer
composer require farsi/inertia-content

# 2. Install JavaScript dependencies (IMPORTANT!)
cd vendor/farsi/inertia-content && npm install && cd -

# 3. Run installer
php artisan inertia-content:install
```

The JavaScript/Vue components are TypeScript source files that **your Vite will compile** - no pre-built JavaScript.

📖 See [How It Works](./docs/how-it-works.md) for detailed explanation.

## Setup

### 1. Add Vite Plugin

Add the Vite plugin to your `vite.config.ts`:

```typescript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
    inertiaContent(),
  ],
  resolve: {
    alias: {
      '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
    }
  }
})
```

### 2. Create Content

Create markdown files in `resources/content`:

```markdown
---
title: Getting Started
description: Learn how to use Inertia Content
order: 1
---

## Introduction

Welcome to **Inertia Content**!
```

### 3. Add Routes

Add content routes to `routes/web.php`:

```php
use Farsi\InertiaContent\Facades\Content;

Route::get('/docs/{path?}', function ($path = 'index') {
    return Content::pageOrFail("docs/$path");
})->where('path', '.*');
```

### 4. Render Content

Create a Vue component to render content:

```vue
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <ContentDoc :content-key="page.props.contentKey" />
</template>
```

## Usage

### Query Content (PHP)

```php
use Farsi\InertiaContent\Facades\Content;

// Find a single entry
$entry = Content::find('docs/intro');

// Check if content exists
if (Content::exists('docs/intro')) {
    // ...
}

// Query multiple entries
$entries = Content::query()
    ->where('_dir', 'docs')
    ->where('draft', false)
    ->orderBy('order', 'asc')
    ->get();

// Get navigation tree
$nav = Content::navigation('docs');
```

### Render Content (Vue)

#### ContentRenderer Component

Simple content rendering:

```vue
<script setup lang="ts">
import { ContentRenderer } from 'farsi-inertia-content'
</script>

<template>
  <ContentRenderer content-key="docs/intro" />
</template>
```

#### ContentDoc Component

Full document with header, TOC, and footer:

```vue
<script setup lang="ts">
import { ContentDoc } from 'farsi-inertia-content'
</script>

<template>
  <ContentDoc content-key="docs/intro">
    <template #header="{ meta }">
      <h1>{{ meta.title }}</h1>
      <p>{{ meta.description }}</p>
    </template>

    <template #toc="{ headings }">
      <nav>
        <a v-for="h in headings" :key="h.id" :href="`#${h.id}`">
          {{ h.text }}
        </a>
      </nav>
    </template>
  </ContentDoc>
</template>
```

#### ContentList Component

List multiple content entries:

```vue
<script setup lang="ts">
import { ContentList } from 'farsi-inertia-content'
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

#### useContent Composable

For advanced use cases:

```vue
<script setup lang="ts">
import { useContent } from 'farsi-inertia-content'

const { component, meta, headings, isLoading, error } = useContent('docs/intro')
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

## Frontmatter

Supported frontmatter fields:

```yaml
---
title: Page Title              # Required
description: Page description  # Optional
draft: false                   # Optional, default: false
navigation: true               # Optional, default: true
order: 1                       # Optional, for sorting
excerpt: Custom excerpt        # Optional, auto-generated if not provided
# ... any custom fields
---
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=inertia-content-config
```

Available options in `config/inertia-content.php`:

```php
return [
    'content_dir' => resource_path('content'),
    'manifest_path' => public_path('build/inertia-content-manifest.json'),
    'show_drafts' => env('INERTIA_CONTENT_SHOW_DRAFTS', false),
    'default_component' => 'Content/Page',
    // ... more options
];
```

## Commands

```bash
# Install the package
php artisan inertia-content:install

# Clear content cache
php artisan inertia-content:clear
```

## Testing

```bash
# PHP tests
composer test

# JavaScript tests
npm test
```

## How It Works

Inertia Content follows a unique architecture that respects both Laravel and Inertia.js philosophies:

### Build Time
1. Vite plugin scans `resources/content/**/*.md`
2. Parses frontmatter, extracts headings, generates excerpts
3. Compiles Markdown → Vue components
4. Generates JSON manifest with metadata

### Runtime
1. Laravel queries the manifest (cached)
2. Passes only the content **key** through Inertia (never HTML/Markdown)
3. Vue dynamically imports the pre-compiled component
4. Component renders instantly (already compiled)

### Key Innovation

**No HTML through Inertia props. No runtime Markdown parsing.**

Laravel maintains authority over:
- ✅ Content queries and filtering
- ✅ Access control and permissions
- ✅ Routing decisions

Vue handles:
- ✅ Component rendering (pre-compiled)
- ✅ User interactions
- ✅ Client-side navigation

## Comparison

| Feature | Inertia Content | Traditional CMS | Static Site Generators |
|---------|----------------|-----------------|------------------------|
| **Runtime Parsing** | ❌ No | ✅ Yes | ❌ No |
| **Build Performance** | ⚡ Fast | N/A | ⚡ Fast |
| **Laravel Integration** | ✅ Native | ⚠️ API-based | ❌ Separate |
| **Dynamic Queries** | ✅ Yes | ✅ Yes | ❌ Limited |
| **Type Safety** | ✅ Full | ⚠️ Partial | ⚠️ Partial |
| **HMR** | ✅ Yes | ⚠️ Sometimes | ✅ Yes |
| **Server Authority** | ✅ Complete | ✅ Complete | ❌ No server |

## Nuxt Content Compatibility

This package implements **80% of Nuxt Content v2 core features**, adapted for Laravel + Inertia:

✅ **Implemented:**
- Query API (`Content::query()` ≈ `queryContent()`)
- Markdown parsing with frontmatter
- Heading extraction & TOC
- Navigation generation
- HMR support
- Vue components
- TypeScript support

⏳ **Planned for v1.1:**
- MDC (Markdown Components)
- Full-text search
- Syntax highlighting (Shiki)
- YAML/JSON file support

See [Nuxt Content Comparison](./docs/nuxt-content-comparison.md) for detailed feature parity analysis.

## Roadmap

- [x] Markdown compilation
- [x] Query API (Nuxt Content-compatible)
- [x] Vue components
- [x] HMR support
- [x] TypeScript support
- [ ] MDC (Markdown Components) - v1.1
- [ ] Full-text search - v1.1
- [ ] Shiki syntax highlighting - v1.1
- [ ] YAML/JSON support - v1.1
- [ ] Content versioning - v1.2
- [ ] Multi-language support - v1.2

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email dev@farsi.dev instead of using the issue tracker.

See [Security Policy](./docs/security.md) for more details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Farsi Dev](https://github.com/farsidev)
- Inspired by [Nuxt Content](https://content.nuxtjs.org/)
- Built with [Spatie's Laravel Package Skeleton](https://github.com/spatie/package-skeleton-laravel)
- All contributors

## Package Architecture

This is a **single Composer package** that includes both PHP and JavaScript code.

📦 **Installation**: `composer require farsi/inertia-content`
📂 **JavaScript**: Included in `vendor/farsi/inertia-content/resources/js/`
🔌 **Vite Plugin**: Import from vendor directory

See [Package Structure](./docs/structure.md) and [Architecture](./docs/architecture.md) for details.

## Support

- ⭐ Star the repo
- 🐛 Report bugs via [GitHub Issues](https://github.com/farsidev/inertia-content/issues)
- 💬 Discuss features via [GitHub Discussions](https://github.com/farsidev/inertia-content/discussions)
- 📖 Read the [full documentation](./docs/README.md)
