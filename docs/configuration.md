# Configuration

All configuration options in `config/inertia-content.php`.

## Main Settings

### Content Directory

```php
'content_dir' => resource_path('content'),
```

Path to your content folder. You can change it:

```php
'content_dir' => base_path('content'), // Project root
```

### Manifest Path

```php
'manifest_path' => public_path('build/inertia-content-manifest.json'),
```

Where the manifest file will be generated.

## Cache

```php
'cache' => [
    'enabled' => env('INERTIA_CONTENT_CACHE', true),
    'store' => env('INERTIA_CONTENT_CACHE_STORE', 'file'),
    'ttl' => 3600, // 1 hour
    'prefix' => 'inertia_content_',
],
```

### Disable Cache (Development)

```env
# .env
INERTIA_CONTENT_CACHE=false
```

## Drafts

```php
'show_drafts' => env('INERTIA_CONTENT_SHOW_DRAFTS', false),
```

Show draft content:

```env
# .env.local (development only)
INERTIA_CONTENT_SHOW_DRAFTS=true
```

## Default Component

```php
'default_component' => 'Content/Page',
```

Vue component used to render content pages.

```php
// Usage
Content::page('docs/intro'); // Uses Content/Page
Content::page('docs/intro', 'Docs/CustomPage'); // Custom
```

## Route Settings

```php
'routes' => [
    'enabled' => false,  // Usually false
    'prefix' => '',
    'middleware' => ['web'],
],
```

⚠️ It's recommended to define routes yourself.

## Vite Plugin Settings

In `vite.config.ts`:

```typescript
// Import from vendor directory
import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite'

inertiaContent({
  // Content source directory (relative to root)
  contentDir: 'resources/content',

  // Manifest output path
  manifestPath: 'public/build/inertia-content-manifest.json',

  // Markdown-it options
  markdown: {
    linkify: true,
    typographer: true,
    html: false, // Allow HTML in markdown
  },

  // Heading extraction
  headings: {
    depth: [2, 3, 4], // H2, H3, H4
    anchorLinks: true, // Add IDs to headings
  },

  // Excerpt generation
  excerpt: {
    maxLength: 200,
    separator: '\n\n',
  },

  // Ignored files
  ignore: [
    '**/.*',        // Dot files
    '**/_*',        // Underscore prefix
    '**/node_modules/**',
  ],
})
```

## Default Frontmatter

You can use these fields in your content:

```yaml
---
title: Page Title       # Required
description: ...        # Optional
draft: false           # Default: false
navigation: true       # Default: true
order: 1               # For sorting
excerpt: "..."         # Manual excerpt
# + any custom fields
---
```

## Environment Variables

```env
# Cache
INERTIA_CONTENT_CACHE=true
INERTIA_CONTENT_CACHE_STORE=file

# Drafts (development only)
INERTIA_CONTENT_SHOW_DRAFTS=false
```

## Production Optimization

```php
// config/inertia-content.php
'cache' => [
    'enabled' => true,
    'store' => 'redis', // Faster than file
    'ttl' => 86400, // 24 hours
],
```

```env
# .env.production
INERTIA_CONTENT_CACHE=true
INERTIA_CONTENT_CACHE_STORE=redis
INERTIA_CONTENT_SHOW_DRAFTS=false
```
