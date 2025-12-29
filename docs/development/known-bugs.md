# Known Issues

## 🐛 Critical: Virtual Modules Not Emitted in Production Build

**Status**: 🔴 **CRITICAL** - Blocks production use  
**Reported**: December 29, 2025  
**Affects**: v1.0.0-alpha  
**Priority**: P0 - Must fix before beta

### Problem

Virtual content modules work in development but fail in production build:

```javascript
// This works in dev, fails in production
const module = await import(`virtual:inertia-content/entry/${path}`)
```

**Error in production:**
```
Failed to fetch dynamically imported module: virtual:inertia-content/entry/docs/index
```

### Root Cause

The Vite plugin's `load()` hook returns Vue component code but doesn't emit it as a physical chunk during build.

```typescript
// Current implementation (BROKEN in production)
load(id) {
  if (id.startsWith('\0' + VIRTUAL_ENTRY_PREFIX)) {
    const entry = compiledEntries.get(contentPath)
    return entry.vueComponent  // ❌ Not emitted as chunk
  }
}
```

### Impact

- ✅ Works: Development (HMR, Vite dev server)
- ❌ Broken: Production build
- ❌ Broken: Preview mode (`npm run preview`)
- ❌ Blocks: Any production deployment

---

## 🔧 Fix Required

### Solution: Change Architecture

Instead of virtual modules + dynamic imports, use **glob imports**:

#### 1. Update Vite Plugin

Generate actual .js files or use Vite's glob import:

```typescript
// resources/js/vite/plugin.ts

generateBundle(options, bundle) {
  // Emit each content entry as a chunk
  for (const [path, entry] of compiledEntries) {
    this.emitFile({
      type: 'chunk',
      id: `\0${VIRTUAL_ENTRY_PREFIX}${path}`,
      name: `content-${entry.meta._hash}`,
      fileName: `assets/content-${entry.meta._hash}.js`
    })
  }
}
```

#### 2. Alternative: Pre-compile to Files

Generate actual files instead of virtual modules:

```typescript
buildStart() {
  const tempDir = path.join(config.root, 'node_modules/.inertia-content')
  
  for (const [path, entry] of compiledEntries) {
    const filePath = path.join(tempDir, `${path}.js`)
    fs.writeFileSync(filePath, entry.vueComponent)
  }
}
```

#### 3. Best Solution: Eager Imports

Use Vite's glob imports instead of virtual modules:

```typescript
// resources/js/runtime/useContent.ts

// Replace dynamic import with eager glob
const contentModules = import.meta.glob('/resources/content/**/*.md', {
  eager: false,
  import: 'default'
})

export function useContent(key: string) {
  const modulePath = `/resources/content/${key}.md`
  const loader = contentModules[modulePath]
  
  if (loader) {
    const module = await loader()
    component.value = module.default
  }
}
```

But this requires content files to be actual .js/.vue files, not virtual...

---

## 🎯 Recommended Fix (Hybrid Approach)

### Step 1: Write Compiled Components to Temp Directory

```typescript
// vite/plugin.ts - buildStart()
const cacheDir = path.join(config.root, 'node_modules/.vite/inertia-content')

for (const [contentPath, entry] of compiledEntries) {
  const componentPath = path.join(cacheDir, `${contentPath}.vue`)
  fs.mkdirSync(path.dirname(componentPath), { recursive: true })
  fs.writeFileSync(componentPath, entry.vueComponent)
}
```

### Step 2: Update useContent to Import Real Files

```typescript
// runtime/useContent.ts
const components = import.meta.glob(
  '/node_modules/.vite/inertia-content/**/*.vue',
  { eager: false }
)

async function load() {
  const modulePath = `/node_modules/.vite/inertia-content/${path}.vue`
  const loader = components[modulePath]
  
  if (loader) {
    component.value = await loader()
  }
}
```

### Step 3: Clean Up After Build

```typescript
// vite/plugin.ts - closeBundle()
closeBundle() {
  const cacheDir = path.join(config.root, 'node_modules/.vite/inertia-content')
  if (fs.existsSync(cacheDir)) {
    fs.rmSync(cacheDir, { recursive: true })
  }
}
```

---

## 🚨 Temporary Workaround (Until Fixed)

**For testing only** - Pass HTML through Inertia props:

```php
// ContentManager.php
public function page(string $path, ?string $component = null): Response
{
    $entry = $this->findOrFail($path);
    
    // Temporary: Render markdown server-side
    $parser = new \Parsedown();
    $html = $parser->text($entry->getBody()); // Need to add getBody()
    
    return Inertia::render($component, [
        'contentKey' => $entry->getPath(),
        'contentMeta' => $entry->getMeta(),
        'contentHtml' => $html,  // ⚠️ Temporary workaround
    ]);
}
```

```vue
<!-- ContentRenderer.vue -->
<div v-if="$page.props.contentHtml" v-html="$page.props.contentHtml" />
```

**⚠️ Warning**: This defeats the purpose of build-time compilation. Only use for testing.

---

## 📋 Fix Checklist

- [ ] Implement one of the recommended solutions
- [ ] Test in production build
- [ ] Verify chunks are emitted
- [ ] Test dynamic imports work
- [ ] Update documentation
- [ ] Add test cases
- [ ] Release v1.0.0-beta with fix

---

## 🔬 How to Reproduce

```bash
# 1. Clone and install
git clone https://github.com/farsidev/inertia-content.git
cd inertia-content
npm install

# 2. Link to Laravel app
composer require farsi/inertia-content:@dev
cd vendor/farsi/inertia-content && npm install && cd -

# 3. Build for production
npm run build

# 4. Check build output
ls public/build/assets/ | grep content
# Result: No content-*.js files ❌

# 5. Test in browser
php artisan serve
# Visit site - dynamic import fails ❌
```

---

## 📚 References

- [Vite Virtual Modules](https://vitejs.dev/guide/api-plugin.html#virtual-modules-convention)
- [Vite emitFile](https://rollupjs.org/plugin-development/#this-emitfile)
- [Vite Glob Import](https://vitejs.dev/guide/features.html#glob-import)

---

**Status**: 🔴 **BLOCKING**  
**Severity**: Critical  
**Next**: Implement fix immediately

