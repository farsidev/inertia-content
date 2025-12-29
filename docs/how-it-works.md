# How It Works

Clear explanation of how Inertia Content works.

## Package Type

**This is a Laravel Composer package with TypeScript source code.**

- ✅ Published to **Packagist only** (not npm)
- ✅ Contains PHP classes
- ✅ Contains TypeScript/Vue source (NOT pre-built)
- ✅ User's Vite compiles the TypeScript

## Installation Flow

```bash
# 1. Install via Composer
composer require farsi/inertia-content
# → Installs to vendor/farsi/inertia-content/

# 2. Install JS dependencies IN THE PACKAGE
cd vendor/farsi/inertia-content
npm install  # Installs markdown-it, gray-matter, etc.
cd ../../..

# 3. Run installer
php artisan inertia-content:install
# → Publishes config, creates content directory
```

## What Happens at Build Time

### User's Vite Config

```typescript
// vite.config.ts
import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
  plugins: [
    inertiaContent()  // ← Plugin from vendor
  ]
})
```

### Build Process

```
1. User runs: npm run build

2. Vite loads plugin from vendor:
   vendor/farsi/inertia-content/resources/js/vite/plugin.ts

3. Plugin scans: resources/content/**/*.md

4. Plugin compiles:
   intro.md → Vue component (in-memory)

5. Plugin generates:
   public/build/inertia-content-manifest.json

6. Vite bundles everything:
   - User's app.js
   - Vue components (including ours)
   - Compiled content components
```

## What Happens at Runtime

### PHP (Laravel)

```php
use Farsi\InertiaContent\Facades\Content;

// 1. Load manifest (cached)
$entry = Content::find('docs/intro');

// 2. Pass to Inertia
return Inertia::render('Page', [
    'contentKey' => 'docs/intro',  // ← Just the KEY
    'contentMeta' => $entry->getMeta()
]);
```

### Vue (Frontend)

```vue
<script setup lang="ts">
import { useContent } from '@inertia-content'

// 1. Get content key from Inertia
const { component } = useContent($page.props.contentKey)

// 2. Dynamic import (already compiled!)
// → Loads: public/build/content-abc123.js

// 3. Render pre-compiled component
</script>

<template>
  <component :is="component" />
</template>
```

## File Locations

```
vendor/farsi/inertia-content/
├── src/                    # PHP classes (used by Laravel)
│   └── ContentManager.php  # Laravel autoloads this
│
├── resources/js/           # TypeScript source (compiled by USER's Vite)
│   ├── vite/
│   │   └── plugin.ts      # Vite plugin
│   ├── runtime/
│   │   └── useContent.ts  # Vue composables
│   └── node_modules/      # Dependencies (after npm install)
│       ├── markdown-it/
│       └── gray-matter/
│
└── config/
    └── inertia-content.php
```

## Why This Structure?

### ✅ Advantages

1. **Single package** - Only `composer require`
2. **No version conflicts** - PHP and JS always in sync
3. **User's Vite compiles** - Optimizes for user's setup
4. **Standard pattern** - Like Spatie packages with assets

### 🎯 User's Vite Does:

- Compile our TypeScript → JavaScript
- Bundle our Vue components
- Optimize for production
- Handle HMR in development

### 📦 We Provide:

- PHP classes (pre-ready)
- TypeScript source (to be compiled)
- Vite plugin (to compile content)
- Vue components (to be bundled)

## Development vs Production

### Development

```bash
npm run dev
```

User's Vite:
- Watches `resources/content/**/*.md`
- Our plugin compiles changes
- HMR updates browser
- No page reload needed

### Production

```bash
npm run build
```

User's Vite:
- Compiles all TypeScript (including ours)
- Minifies everything
- Generates manifest
- Optimizes bundles

## No Separate npm Package!

```
❌ npm install farsi-inertia-content  # This doesn't exist!
✅ composer require farsi/inertia-content  # This works!
   └── Then: cd vendor/farsi/inertia-content && npm install
```

## Summary

```
1. User installs via Composer
2. User runs npm install in vendor directory
3. User's Vite compiles our TypeScript
4. Everything works together seamlessly
```

**This is a Composer package with TypeScript source, not a dual package.**
