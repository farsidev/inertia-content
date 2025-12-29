# Nuxt Content Comparison

Detailed comparison between our implementation and [Nuxt Content v2](https://github.com/nuxt/content).

## Feature Parity Matrix

| Feature | Nuxt Content | Inertia Content | Status | Notes |
|---------|--------------|-----------------|--------|-------|
| **Core Features** |
| File-based content | ✅ | ✅ | ✅ Complete | |
| Markdown parsing | ✅ | ✅ | ✅ Complete | Using markdown-it |
| YAML frontmatter | ✅ | ✅ | ✅ Complete | Using gray-matter |
| Heading extraction | ✅ | ✅ | ✅ Complete | H2-H4 with IDs |
| Excerpt generation | ✅ | ✅ | ✅ Complete | Auto or manual |
| Draft support | ✅ | ✅ | ✅ Complete | `draft: true` |
| Navigation | ✅ | ✅ | ✅ Complete | Tree structure |
| HMR | ✅ | ✅ | ✅ Complete | Full support |
| **Query API** |
| `queryContent()` | ✅ | ✅ | ✅ Complete | `Content::query()` |
| `where()` | ✅ | ✅ | ✅ Complete | Multiple operators |
| `whereIn()` | ✅ | ✅ | ✅ Complete | |
| `only()` / `without()` | ✅ | ✅ | ✅ Complete | Field selection |
| `orderBy()` | ✅ | ✅ | ✅ Complete | Multiple sorts |
| `limit()` / `skip()` | ✅ | ✅ | ✅ Complete | Pagination |
| `find()` | ✅ | ✅ | ✅ Complete | |
| `findOne()` | ✅ | ✅ | ✅ Complete | As `first()` |
| **File Formats** |
| Markdown (.md) | ✅ | ✅ | ✅ Complete | Full support |
| YAML (.yml) | ✅ | ❌ | ⏳ v1.1 | Planned |
| JSON (.json) | ✅ | ❌ | ⏳ v1.1 | Planned |
| CSV (.csv) | ✅ | ❌ | ⏳ v1.1 | Planned |
| **Advanced Features** |
| MDC (components in MD) | ✅ | ❌ | ⏳ v1.1 | Planned |
| Syntax highlighting | ✅ Shiki | ❌ | ⏳ v1.1 | Can add to markdown-it |
| Full-text search | ✅ | ❌ | ⏳ v1.1 | MiniSearch planned |
| SQLite query engine | ✅ | ❌ | 🚫 | N/A - in-memory |
| **Components** |
| `<ContentRenderer>` | ✅ | ✅ | ✅ Complete | |
| `<ContentDoc>` | ✅ | ✅ | ✅ Complete | |
| `<ContentList>` | ✅ | ✅ | ✅ Complete | |
| `useContent()` | ✅ | ✅ | ✅ Complete | Composable |
| **Build & Runtime** |
| Build-time compilation | ✅ | ✅ | ✅ Complete | |
| Runtime parsing | ✅ | ❌ | ✅ Better | No runtime parsing |
| Virtual modules | ✅ | ✅ | ✅ Complete | Vite plugin |
| Edge/Serverless | ✅ | ✅ | ✅ Complete | Laravel compatible |

---

## What We Implemented ✅

### 1. Core Content System
```php
// Identical mental model to Nuxt Content
Content::query()
    ->where('_dir', 'docs')
    ->where('draft', false)
    ->orderBy('order')
    ->limit(10)
    ->get()
```

### 2. File-based Markdown
```
resources/content/
├── docs/
│   ├── intro.md
│   └── guide.md
└── blog/
    └── post.md
```

### 3. Frontmatter Parsing
```yaml
---
title: My Page
description: Description
draft: false
navigation: true
order: 1
---
```

### 4. Query Operators
- ✅ `=`, `!=`, `>`, `<`, `>=`, `<=`
- ✅ `contains`, `startsWith`, `endsWith`
- ✅ `in` (whereIn)

### 5. Vue Components
- ✅ `<ContentRenderer>` - Simple rendering
- ✅ `<ContentDoc>` - Full document with slots
- ✅ `<ContentList>` - List rendering
- ✅ `useContent()` - Composable

### 6. Build-time Compilation
- ✅ Markdown → Vue components
- ✅ Manifest generation
- ✅ Metadata extraction
- ✅ Heading extraction
- ✅ Excerpt generation

### 7. HMR Support
- ✅ Watch file changes
- ✅ Recompile on save
- ✅ Auto-update in browser
- ✅ No page reload needed

---

## What We DON'T Have (Yet) ⏳

### 1. MDC (Markdown Components)

**Nuxt Content:**
```markdown
::alert
This is an alert component in Markdown!
::
```

**Our Package:** ❌ Not implemented (v1.1)

### 2. Multiple File Formats

**Nuxt Content:** `.md`, `.yml`, `.json`, `.csv`
**Our Package:** Only `.md` (v1.0)

### 3. Syntax Highlighting

**Nuxt Content:** Shiki integration
**Our Package:** Basic markdown-it (can be extended)

**Can be added:**
```typescript
// In vite.config.ts
import shiki from 'shiki'

inertiaContent({
  markdown: {
    highlight: async (code, lang) => {
      const highlighter = await shiki.getHighlighter({ theme: 'nord' })
      return highlighter.codeToHtml(code, { lang })
    }
  }
})
```

### 4. Full-Text Search

**Nuxt Content:** Built-in search
**Our Package:** Manual filter (v1.0), MiniSearch planned (v1.1)

### 5. SQLite Query Engine

**Nuxt Content:** Uses SQLite for queries
**Our Package:** In-memory manifest queries

**Why different:**
- Nuxt Content needs database for SSR/Edge
- We use build-time manifest (simpler, faster for our use case)

---

## Key Differences

### Architecture

| Aspect | Nuxt Content | Inertia Content |
|--------|--------------|-----------------|
| **Platform** | Nuxt.js (SSR) | Laravel + Inertia |
| **Rendering** | Server-side | Build-time → Client |
| **Query Layer** | SQLite runtime | JSON manifest |
| **Components** | Nuxt components | Vue 3 components |
| **Backend** | Nitro server | Laravel |
| **Routing** | Nuxt router | Laravel routes |

### Mental Model

**Both use same query API:**

```javascript
// Nuxt Content
const posts = await queryContent('blog')
  .where('draft', false)
  .limit(10)
  .find()

// Inertia Content (PHP)
$posts = Content::query()
    ->where('draft', false)
    ->limit(10)
    ->get()
```

### Compilation

**Nuxt Content:**
- Runtime compilation possible
- SQLite database for queries
- Server-side rendering

**Inertia Content:**
- Build-time only
- JSON manifest for queries
- Client-side rendering (pre-compiled)

**Result:** Inertia Content is **faster at runtime** (no compilation needed)

---

## Advantages Over Nuxt Content

### 1. No Runtime Parsing
✅ All Markdown compiled at build time
✅ Zero parsing overhead in production

### 2. Laravel-First
✅ Native Laravel integration
✅ Eloquent-style query API
✅ Server-side access control

### 3. Simpler Architecture
✅ No database required
✅ JSON manifest (fast reads)
✅ Pre-compiled Vue components

### 4. Type Safety
✅ PHP types (Laravel)
✅ TypeScript types (Vue)
✅ End-to-end type safety

---

## What Nuxt Content Has That We Don't

### 1. MDC Syntax
```markdown
::card
---
title: My Card
---
Card content here
::
```

**Status:** Planned for v1.1

### 2. Multi-Format Support
- YAML data files
- JSON content
- CSV parsing

**Status:** Planned for v1.1

### 3. Built-in Search
- Full-text indexing
- Search API

**Status:** Planned for v1.1 with MiniSearch

### 4. Shiki Integration
- Advanced syntax highlighting
- Multiple themes

**Status:** Can be added via markdown-it plugin

---

## Implementation Completeness

### v1.0 (Current) - 80% Parity

✅ **Complete:**
- Core query API
- Markdown parsing
- Frontmatter
- Headings & TOC
- Excerpts
- Navigation
- HMR
- Vue components
- TypeScript support

❌ **Missing:**
- MDC syntax
- Multiple formats
- Full-text search
- Shiki highlighting

### v1.1 (Planned) - 95% Parity

Will add:
- MDC component syntax
- YAML/JSON support
- Full-text search (MiniSearch)
- Shiki highlighting

### v2.0 (Future) - 100%+ Parity

Potential additions:
- Advanced search
- Content relationships
- Multi-language
- Content versioning

---

## Conclusion

### ✅ We Have Core Parity

The **essential Nuxt Content experience** is fully implemented:
- Same query API
- Same frontmatter structure
- Same component approach
- Same mental model

### 🎯 Adapted for Laravel + Inertia

We made smart changes for our ecosystem:
- Build-time only (better performance)
- Laravel query builder style
- No database needed
- Inertia.js integration

### 📈 Path Forward

**v1.0** is production-ready with core features.
**v1.1** will add advanced features (MDC, search).
**v2.0** will match and exceed Nuxt Content.

---

## References

- [Nuxt Content Repository](https://github.com/nuxt/content)
- [Nuxt Content Documentation](https://content.nuxt.com)
- [Our Architecture](./architecture.md)
- [Our Roadmap](../CHANGELOG.md)

---

**Last Updated**: December 2025
**Nuxt Content Version Compared**: v3.10.0
**Our Version**: 1.0.0 (Unreleased)
