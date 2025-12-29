# Testing Checklist

Step-by-step guide to test if the package actually works.

## Prerequisites

- Laravel 10.x or 11.x app with Inertia + Vue
- PHP 8.1+
- Node 18+

---

## Test Installation

### 1. Create Test Laravel App

```bash
# New Laravel app
laravel new test-inertia-content
cd test-inertia-content

# Install Inertia
composer require inertiajs/inertia-laravel
npm install @inertiajs/vue3
```

### 2. Install Our Package (Local)

```bash
# Link package locally
composer config repositories.inertia-content path ../inertia-content
composer require farsi/inertia-content:@dev

# Install JS dependencies in package
cd vendor/farsi/inertia-content
npm install
cd ../../..

# Run installer
php artisan inertia-content:install
```

**Expected output:**
```
✓ Config published to config/inertia-content.php
✓ Content directory created at resources/content
✓ Sample content created
✓ Sample pages created

Next steps:
1. Add the Vite plugin...
```

### 3. Configure Vite

Edit `vite.config.js`:

```javascript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        inertiaContent(),
    ],
    resolve: {
        alias: {
            '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
        }
    }
})
```

### 4. Add Route

Edit `routes/web.php`:

```php
use Farsi\InertiaContent\Facades\Content;

Route::get('/{path?}', function ($path = 'index') {
    return Content::pageOrFail($path);
})->where('path', '.*');
```

### 5. Update app.js

Edit `resources/js/app.js` to add Vue:

```javascript
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
```

### 6. Build

```bash
npm run build
```

**Expected:**
- ✅ Build succeeds
- ✅ `public/build/inertia-content-manifest.json` created
- ✅ No errors

**Check manifest:**
```bash
cat public/build/inertia-content-manifest.json
```

Should see:
```json
{
  "version": "...",
  "generatedAt": 1234567890,
  "entries": {
    "index": {
      "path": "index",
      "meta": {
        "title": "Welcome to Inertia Content",
        ...
      }
    }
  }
}
```

### 7. Test Runtime

```bash
php artisan serve
```

Visit: `http://localhost:8000`

**Expected:**
- ✅ Page loads
- ✅ Content renders
- ✅ No console errors

---

## Test Features

### Test 1: Basic Rendering

Visit `http://localhost:8000/index`

**Expected:**
- ✅ Page title shows
- ✅ Markdown content renders
- ✅ Headings have IDs

### Test 2: Query API

```php
// In tinker
php artisan tinker

>>> $entries = Content::query()->get();
>>> $entries->count()
// Should return number of content files

>>> $entry = Content::find('index');
>>> $entry->getTitle()
// "Welcome to Inertia Content"
```

### Test 3: HMR

```bash
npm run dev
```

1. Visit page
2. Edit `resources/content/index.md`
3. Save file
4. Browser should auto-update (no reload!)

**Expected:**
- ✅ Changes appear instantly
- ✅ No page reload
- ✅ Console shows HMR update

### Test 4: Create New Content

```markdown
<!-- resources/content/test.md -->
---
title: Test Page
description: Testing the system
---

## Hello

This is a test!
```

```bash
# Rebuild or wait for HMR
npm run dev
```

Visit: `http://localhost:8000/test`

**Expected:**
- ✅ New page renders
- ✅ Markdown compiles
- ✅ Metadata displays

### Test 5: Query in Controller

Create `app/Http/Controllers/ContentController.php`:

```php
<?php

namespace App\Http\Controllers;

use Farsi\InertiaContent\Facades\Content;
use Inertia\Inertia;

class ContentController extends Controller
{
    public function index()
    {
        $entries = Content::query()
            ->where('draft', false)
            ->orderBy('_createdAt', 'desc')
            ->get();

        return Inertia::render('ContentList', [
            'entries' => $entries->toInertiaProps()
        ]);
    }
}
```

**Expected:**
- ✅ Query executes
- ✅ Entries returned
- ✅ Props passed to Inertia

---

## Common Issues

### Issue 1: Module not found

```
Error: Cannot find module './vendor/farsi/inertia-content/resources/js/vite'
```

**Fix:**
```bash
cd vendor/farsi/inertia-content
npm install
```

### Issue 2: Manifest not found

```
ManifestNotFoundException: Content manifest not found
```

**Fix:**
```bash
npm run build
```

### Issue 3: TypeScript errors

```
Cannot find module '@inertia-content'
```

**Fix:** Add alias to `vite.config.js`:
```javascript
resolve: {
  alias: {
    '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
  }
}
```

### Issue 4: Dependencies missing

```
Error: Cannot find package 'markdown-it'
```

**Fix:**
```bash
cd vendor/farsi/inertia-content
npm install
cd ../../..
```

---

## Expected Behavior

### ✅ Working System

1. **Install** - No errors
2. **Build** - Manifest created
3. **Runtime** - Content renders
4. **HMR** - Updates instantly
5. **Query** - Results returned

### ❌ Known Limitations

- Need to run `npm install` in vendor (documented)
- TypeScript source (not pre-built) - by design
- No separate npm package - by design

---

## Next Steps After Testing

If everything works:
- [ ] Write actual tests
- [ ] Test in production build
- [ ] Test with real content
- [ ] Benchmark performance
- [ ] Document edge cases

If issues found:
- [ ] Document in GitHub Issues
- [ ] Fix bugs
- [ ] Update tests
- [ ] Re-test

---

**Status**: Ready for manual testing
**Automated Tests**: Still needed
