# Build Approaches - Analysis

Different approaches to handle Markdown compilation in Inertia Content.

## Current Approach (Problematic)

**Virtual Modules + emitFile**

❌ Problems:
- Virtual modules complex to handle in production
- emitFile timing issues
- Non-standard approach
- Hard to debug

---

## Recommended Approaches

### ✅ Approach 1: Compile to Real Vue Files (BEST)

**How it works:**

1. **During Vite buildStart:** Compile Markdown → Real .vue files
2. **Write to:** `resources/js/.content-compiled/`
3. **Vite naturally bundles** these files like any other Vue component
4. **Use glob imports** to load them

**Implementation:**

```typescript
// vite/plugin.ts
buildStart() {
  const outputDir = path.join(config.root, 'resources/js/.content-compiled')

  // Compile markdown to .vue files
  for (const [contentPath, entry] of compiledEntries) {
    const vueFilePath = path.join(outputDir, `${contentPath}.vue`)
    fs.mkdirSync(path.dirname(vueFilePath), { recursive: true })
    fs.writeFileSync(vueFilePath, entry.vueComponent)
  }

  // Write manifest module
  const manifestPath = path.join(outputDir, '_manifest.ts')
  fs.writeFileSync(manifestPath, generateManifestCode(manifest))
}
```

```typescript
// runtime/useContent.ts
// Use Vite's glob import
const contentModules = import.meta.glob(
  '/resources/js/.content-compiled/**/*.vue',
  { eager: false }
)

async function load() {
  const modulePath = `/resources/js/.content-compiled/${path}.vue`
  const loader = contentModules[modulePath]

  if (loader) {
    const module = await loader()
    component.value = module.default
  }
}
```

**Benefits:**
- ✅ استاندارد Vite
- ✅ کار می‌کنه در dev و prod
- ✅ HMR رایگان
- ✅ Code splitting خودکار
- ✅ بدون virtual module complexity

**Cleanup:**
```typescript
closeBundle() {
  // حذف فایل‌های compiled بعد از build
  fs.rmSync('resources/js/.content-compiled', { recursive: true })
}
```

---

### ✅ Approach 2: Vite Preprocessing Plugin

**How it works:**

Plugin قبل از Vite اصلی اجرا میشه و فایل‌های واقعی می‌سازه.

```typescript
export default function inertiaContentPreprocess() {
  return {
    name: 'vite-plugin-inertia-content-preprocess',
    enforce: 'pre',  // قبل از بقیه plugins

    configResolved(config) {
      // Compile all markdown before Vite processes files
      compileAllMarkdownToVueFiles(config.root)
    }
  }
}
```

**Benefits:**
- ✅ کاملاً استاندارد
- ✅ Vite فایل‌های واقعی رو می‌بینه
- ✅ همه ابزارهای Vite کار می‌کنن

---

### ✅ Approach 3: Custom File Extension Handler

**How it works:**

`.md` files رو مثل `.vue` handle کنیم:

```typescript
export default function inertiaContent() {
  return {
    name: 'vite-plugin-inertia-content',

    transform(code, id) {
      // فایل‌های .md در content directory رو transform کن
      if (id.endsWith('.md') && id.includes('/content/')) {
        const compiled = compileMarkdown(code, id)
        return {
          code: compiled.vueComponent,
          map: null
        }
      }
    }
  }
}
```

```vue
<!-- استفاده -->
<script setup>
import IntroContent from '@/content/intro.md'
</script>

<template>
  <component :is="IntroContent" />
</template>
```

**Benefits:**
- ✅ استاندارد (مثل @vitejs/plugin-vue)
- ✅ Import واقعی از فایل‌های واقعی
- ✅ تمام features Vite

---

## Comparison

| Approach | Complexity | Standard | HMR | Production | Recommendation |
|----------|-----------|----------|-----|------------|----------------|
| **Virtual Modules** | High | ❌ | ✅ | ❌ | ❌ Don't use |
| **Compiled Vue Files** | Low | ✅ | ✅ | ✅ | ✅ **BEST** |
| **Preprocessing** | Medium | ✅ | ✅ | ✅ | ✅ Good |
| **Transform Hook** | Medium | ✅ | ✅ | ✅ | ✅ Good |

---

## Recommended Solution: Compiled Vue Files

### Implementation Plan

#### 1. Update Plugin

```typescript
export default function inertiaContent(options = {}) {
  const contentDir = options.contentDir || 'resources/content'
  const compiledDir = 'resources/js/.content-compiled'

  return {
    name: 'vite-plugin-inertia-content',

    configResolved(config) {
      // Compile markdown to Vue files
      compileMarkdownToVueFiles(contentDir, compiledDir)
    },

    handleHotUpdate({ file, server }) {
      if (file.endsWith('.md') && file.includes(contentDir)) {
        // Recompile changed file
        recompileSingleFile(file, compiledDir)

        // Trigger HMR
        const vueFile = convertMdPathToVuePath(file, contentDir, compiledDir)
        const mod = server.moduleGraph.getModuleById(vueFile)
        if (mod) {
          server.reloadModule(mod)
        }
      }
    },

    closeBundle() {
      // Clean up compiled files after build
      if (fs.existsSync(compiledDir)) {
        fs.rmSync(compiledDir, { recursive: true })
      }
    }
  }
}
```

#### 2. Update useContent

```typescript
// Use glob imports
const contentModules = import.meta.glob(
  '/resources/js/.content-compiled/**/*.vue',
  { eager: false }
)

export function useContent(key: string) {
  const load = async () => {
    const modulePath = `/resources/js/.content-compiled/${key}.vue`
    const loader = contentModules[modulePath]

    if (loader) {
      const module = await loader()
      component.value = module.default
      meta.value = module.meta
    }
  }

  // ...
}
```

#### 3. Update .gitignore

```
resources/js/.content-compiled/
```

---

## Benefits of This Approach

### ✅ Standard Vite Workflow

- فایل‌های واقعی Vue → Vite می‌بینه
- Import واقعی → نه virtual
- Code splitting → خودکار
- Tree shaking → خودکار

### ✅ Works Everywhere

- ✅ Development (با HMR)
- ✅ Production build
- ✅ Preview mode
- ✅ SSR (اگه بخوایم)

### ✅ Simple & Maintainable

- کد ساده‌تر
- کمتر bug
- قابل debug
- استاندارد

### ✅ Performance

- Vite optimization ها
- Code splitting بهینه
- Lazy loading
- Chunk هوشمند

---

## Alternative: Transform Hook Approach

اگه نمیخوای فایل بنویسی:

```typescript
export default function inertiaContent() {
  return {
    name: 'vite-plugin-inertia-content',

    // .md files رو مثل .vue handle کن
    transform(code, id) {
      if (id.endsWith('.md') && id.includes('/content/')) {
        const compiled = compileMarkdown(code, id)
        return {
          code: compiled.vueComponent,
          map: null
        }
      }
    }
  }
}
```

استفاده:
```typescript
// Direct import
import DocsIntro from '~/content/docs/intro.md'

// Glob import
const contentFiles = import.meta.glob('~/content/**/*.md')
```

---

## My Recommendation

**Use Approach 1: Compiled Vue Files**

**Why:**
1. استاندارد و simple
2. کار می‌کنه همه‌جا
3. Vite خودش همه چیز رو handle می‌کنه
4. No virtual module complexity
5. قابل debug و test

**Implementation:**
- 2-3 ساعت کار
- ساده و واضح
- بدون edge case

---

**Next Steps:**
1. Implement Approach 1
2. Test in dev & prod
3. Verify chunks created
4. Tag v1.0.0-beta.4
5. Production ready! ✅
