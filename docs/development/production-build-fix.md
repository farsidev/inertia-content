# Production Build Fix

## Problem

Virtual modules don't work in production build - Vite doesn't emit them as physical chunks.

## Solution Implemented

### Hybrid Approach

**Development**: Use virtual modules (fast HMR)
**Production**: Write to temporary directory, import real files

### How It Works

#### Build Time

```typescript
// 1. Scan and compile markdown
buildStart() {
  compiledEntries = scanAndCompile(...)

  // 2. Write to temp directory for production
  if (config.command === 'build') {
    writeVirtualModulesToDisk(
      compiledEntries,
      'node_modules/.vite-inertia-content'
    )
  }
}
```

Creates files like:
```
node_modules/.vite-inertia-content/
├── docs/index.vue
├── docs/getting-started.vue
└── blog/first-post.vue
```

#### Runtime

```typescript
// useContent.ts
if (import.meta.env.DEV) {
  // Dev: Virtual modules
  module = await import(`virtual:inertia-content/entry/${path}`)
} else {
  // Prod: Physical files
  module = await import(`/node_modules/.vite-inertia-content/${path}.vue`)
}
```

#### Cleanup

```typescript
closeBundle() {
  // Remove temp files after build
  fs.rmSync('node_modules/.vite-inertia-content', { recursive: true })
}
```

---

## Benefits

✅ Development: Fast HMR with virtual modules
✅ Production: Reliable with real files
✅ No runtime parsing needed
✅ Vite bundles everything correctly

---

## Testing

```bash
# Build
npm run build

# Check temp files created (during build)
ls node_modules/.vite-inertia-content/

# Check chunks in output
ls public/build/assets/ | grep -E '(content|vue)'

# Should see .vue component chunks bundled

# Test in production
npm run preview
# Visit site - should work!
```

---

## Alternative Solutions Considered

### ❌ Solution 1: emitFile in generateBundle

**Problem**: Too late in build process, chunks already finalized

### ❌ Solution 2: Modify rollup.input

**Problem**: Complex, doesn't work well with virtual modules

### ✅ Solution 3: Write to Disk (CHOSEN)

**Why**: Simple, reliable, works with Vite's normal flow

---

**Status**: Implemented
**Testing**: Required
**Impact**: Fixes critical production bug

