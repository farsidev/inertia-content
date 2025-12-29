# Package Architecture

## Single Laravel Package

**Inertia Content is a single Composer package** that includes both PHP and JavaScript code.

```
┌─────────────────────────────────────────┐
│  Composer Package                       │
│  farsi/inertia-content                  │
│                                         │
│  Contains:                              │
│  ├─ PHP Code (src/)                    │
│  ├─ JavaScript/Vue (resources/js/)     │
│  └─ Vite Plugin (resources/js/vite/)   │
└─────────────────────────────────────────┘
```

## Installation

**Users install only via Composer:**

```bash
composer require farsi/inertia-content
php artisan inertia-content:install
```

**No separate npm install needed!**

---

## How It Works

### 1. Installation

```bash
composer require farsi/inertia-content
```

This installs:
- PHP classes in `vendor/farsi/inertia-content/src/`
- JavaScript source in `vendor/farsi/inertia-content/resources/js/`
- Vite plugin in `vendor/farsi/inertia-content/resources/js/vite/`

### 2. Usage in User's Project

**PHP (Laravel):**
```php
use Farsi\InertiaContent\Facades\Content;

Content::query()->get();
```

**Vite Plugin (vite.config.ts):**
```typescript
import { defineConfig } from 'vite'
import inertiaContent from '../../vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
  plugins: [
    inertiaContent()
  ]
})
```

**Vue Components:**
```vue
<script setup lang="ts">
import { ContentDoc } from '../../vendor/farsi/inertia-content/resources/js'
</script>
```

### 3. Aliasing (Recommended)

To avoid long paths, users can add alias in `vite.config.ts`:

```typescript
export default defineConfig({
  resolve: {
    alias: {
      '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
    }
  }
})
```

Then use:
```typescript
import { ContentDoc } from '@inertia-content'
import inertiaContent from '@inertia-content/vite'
```

---

## Package Structure

```
vendor/farsi/inertia-content/
├── src/                        # PHP Classes
│   ├── ContentManager.php
│   ├── ContentQuery.php
│   └── ...
│
├── resources/
│   ├── js/                     # JavaScript/TypeScript
│   │   ├── index.ts           # Main export
│   │   ├── runtime/
│   │   │   ├── useContent.ts
│   │   │   ├── ContentRenderer.vue
│   │   │   └── ...
│   │   └── vite/
│   │       ├── plugin.ts      # Vite plugin
│   │       ├── compiler.ts
│   │       └── ...
│   └── stubs/                  # Publishable files
│
├── config/
│   └── inertia-content.php
│
└── composer.json
```

---

## User Workflow

### Step 1: Install

```bash
composer require farsi/inertia-content
php artisan inertia-content:install
```

### Step 2: Configure Vite

```typescript
// vite.config.ts
import inertiaContent from '../../vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
  plugins: [
    laravel({ ... }),
    vue(),
    inertiaContent()
  ],
  resolve: {
    alias: {
      '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
    }
  }
})
```

### Step 3: Use in Vue

```vue
<script setup lang="ts">
import { ContentDoc } from '@inertia-content'
</script>

<template>
  <ContentDoc :content-key="$page.props.contentKey" />
</template>
```

### Step 4: Build

```bash
npm run build
```

---

## Similar Packages

This structure is similar to:

- **Spatie Laravel packages** with frontend assets
- **Laravel Breeze** (before it became a separate npm package)
- **Any Laravel package** that includes JavaScript/Vue components

---

## Development

### For Package Maintainers

When developing this package:

1. **PHP code** - Edit directly in `src/`
2. **TypeScript code** - Edit in `resources/js/`
3. **No build step for distribution** - Source files are published
4. **Users' Vite handles TypeScript compilation**

### Publishing

Only publish via Composer:

```bash
git tag v1.0.0
git push origin v1.0.0
# Packagist auto-updates
```

What gets published:
- ✅ `src/` - PHP source
- ✅ `resources/js/` - TypeScript source (users compile it)
- ✅ `resources/stubs/` - Publishable files
- ✅ `config/` - Configuration
- ✅ `routes/` - Routes
- ❌ `tests/`, `.github/`, `docs/` (via .gitattributes)

---

## Why This Approach?

### ✅ Advantages

1. **Single install command** - Just `composer require`
2. **No version mismatch** - PHP and JS always in sync
3. **Simpler for users** - No npm package to manage
4. **Standard Laravel pattern** - Familiar to Laravel developers
5. **Vite handles compilation** - Users' Vite compiles TypeScript

### 🎯 How It Works

- **During development**: Users' Vite compiles our TypeScript on-the-fly
- **In production**: Users' `npm run build` compiles everything
- **No pre-compilation needed**: We ship TypeScript source, not built JS

---

## TypeScript in Vendor

**Question**: Can users import TypeScript from vendor?

**Answer**: Yes! Modern Vite handles this automatically:

```typescript
// This works - Vite compiles it
import { ContentDoc } from '../../vendor/farsi/inertia-content/resources/js'
```

Vite's config typically includes:

```typescript
optimizeDeps: {
  include: [
    'vendor/farsi/inertia-content/resources/js'
  ]
}
```

---

## Comparison

### ❌ NOT Like This (Dual Package)

```bash
composer require farsi/inertia-content    # ❌
npm install farsi-inertia-content         # ❌
```

### ✅ Like This (Single Laravel Package)

```bash
composer require farsi/inertia-content    # ✅
# JavaScript is already inside!
```

---

## Summary

```
ONE Composer Package
├── PHP Code (compiled via Composer autoload)
├── TypeScript Code (compiled by user's Vite)
└── Vite Plugin (loaded by user's Vite)

Installation:
└── composer require farsi/inertia-content

Usage:
├── PHP: use Farsi\InertiaContent\...
├── Vite: import ... from 'vendor/farsi/.../js/vite'
└── Vue: import ... from 'vendor/farsi/.../js'
```

This is a **standard Laravel package with frontend assets**. 🎉
