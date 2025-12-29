# Installation Guide

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- Node.js 18 or higher
- Inertia.js with Vue 3

## Installation

### 1. Install Package

```bash
composer require farsi/inertia-content
```

### 2. Run Install Command

```bash
php artisan inertia-content:install
```

This command will:
- ✅ Publish config file
- ✅ Create `resources/content` directory
- ✅ Add sample content files
- ✅ Add sample Vue pages

### 3. Configure Vite

Edit your `vite.config.ts`:

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
    inertiaContent(), // 👈 Add this
  ],
  resolve: {
    alias: {
      '@inertia-content': '/vendor/farsi/inertia-content/resources/js'
    }
  }
})
```

### 4. Initial Build

```bash
npm run build
```

This will:
- Compile Markdown files to Vue components
- Generate manifest in `public/build/`
- Prepare Vue components

## Optional Configuration

### Publish Config

```bash
php artisan vendor:publish --tag=inertia-content-config
```

This creates `config/inertia-content.php`.

### Environment Variables

```env
# .env
INERTIA_CONTENT_CACHE=true
INERTIA_CONTENT_SHOW_DRAFTS=false
```

## Verify Installation

### 1. Check Files

```bash
ls resources/content/        # Should see index.md
ls resources/js/Pages/Content/  # Should see Page.vue
```

### 2. Check Manifest

```bash
# After build
cat public/build/inertia-content-manifest.json
```

### 3. Test in Browser

```bash
npm run dev
```

Visit `http://localhost:5173` to see sample content.

## Troubleshooting

### Manifest Not Found

```bash
# Make sure to build
npm run build

# Check path
php artisan tinker
>>> config('inertia-content.manifest_path')
```

### Content Not Showing

```bash
# Clear cache
php artisan inertia-content:clear

# Rebuild
npm run build
```

### TypeScript Errors

```bash
# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

## Next Steps

- [Quick Start](./getting-started.md)
- [Configuration](./configuration.md)
- [Usage Guide](./usage/)
