---
title: Getting Started
description: Learn how to set up Inertia Content in your Laravel application
navigation: true
order: 1
---

## Installation

Install the package via Composer and NPM:

```bash
composer require farsi/inertia-content
npm install farsi-inertia-content
```

Run the installation command:

```bash
php artisan inertia-content:install
```

## Configuration

### Add Vite Plugin

Add the Vite plugin to your `vite.config.ts`:

```typescript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaContent from 'farsi-inertia-content/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
    inertiaContent(), // Add this
  ],
})
```

### Configure Routes

Add content routes to your `routes/web.php`:

```php
use Farsi\InertiaContent\Facades\Content;

Route::get('/docs/{path?}', function ($path = 'index') {
    return Content::pageOrFail("docs/$path");
})->where('path', '.*');
```

## Creating Content

Create markdown files in `resources/content`:

```markdown
---
title: My Page
description: This is my page
---

## Welcome

This is the content of my page.
```

## Next Steps

- Learn about [querying content](/docs/querying)
- Explore [Vue components](/docs/components)
- Understand [frontmatter options](/docs/frontmatter)

