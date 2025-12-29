---
title: Welcome to Inertia Content
description: A Nuxt Content-compatible content engine for Laravel + Inertia
navigation: true
order: 1
---

## Introduction

Welcome to **Inertia Content**, a powerful content management system that brings the developer experience of Nuxt Content to Laravel + Inertia applications.

### Features

- 📝 **Write content in Markdown** - Simple, familiar syntax
- ⚡ **Build-time compilation** - Markdown compiles to Vue components
- 🔍 **Query content with a fluent API** - Filter, sort, and paginate easily
- 🎯 **Full TypeScript support** - Type-safe from PHP to Vue
- 🔥 **Hot Module Replacement** - Instant feedback during development

## Getting Started

Create markdown files in your `resources/content` directory:

```markdown
---
title: My First Post
description: This is my first content page
---

# Hello World

This is my first content page!
```

Then render it in your Vue component:

```vue
<script setup lang="ts">
import { ContentRenderer } from 'farsi-inertia-content'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <ContentRenderer :content-key="page.props.contentKey" />
</template>
```

## Querying Content

You can query content from PHP using the fluent API:

```php
use Farsi\InertiaContent\Facades\Content;

// Find a single entry
$intro = Content::find('docs/intro');

// Query multiple entries
$docs = Content::query()
    ->where('_dir', 'docs')
    ->where('draft', false)
    ->orderBy('order', 'asc')
    ->get();
```

## Learn More

Check out the [documentation](https://github.com/farsidev/inertia-content) to learn more about Inertia Content.
