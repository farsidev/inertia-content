# Missing Features & Roadmap

Features from Nuxt Content that we haven't implemented yet.

## v1.0 Status

**Implemented:** 80% of core Nuxt Content features
**Missing:** Advanced features planned for future versions

---

## Missing from v1.0

### 1. MDC (Markdown Components) ⏳ v1.1

**What is MDC:**
```markdown
::alert{type="warning"}
This is a Vue component in Markdown!
::

::card
---
title: Card Title
---
Card content goes here
::
```

**Status:** Not implemented
**Priority:** High
**Planned:** v1.1
**Difficulty:** Medium

**Implementation Plan:**
- Parse MDC syntax in compiler
- Register component mapping
- Transform to Vue components
- Support slots and props

### 2. Multiple File Formats ⏳ v1.1

**Missing formats:**
- `.yml` / `.yaml` - Data files
- `.json` - JSON content
- `.csv` - Tabular data

**Current:** Only `.md` files supported

**Priority:** Medium
**Planned:** v1.1
**Difficulty:** Low

**Implementation Plan:**
```typescript
// Add to compiler.ts
export function compileYAML(source: string): CompileResult
export function compileJSON(source: string): CompileResult
export function compileCSV(source: string): CompileResult
```

### 3. Syntax Highlighting (Shiki) ⏳ v1.1

**Nuxt Content:** Uses Shiki for beautiful code highlighting
**Our Package:** Basic markdown-it (no highlighting)

**Status:** Partially available
**Priority:** Medium
**Planned:** v1.1

**Can be added NOW:**
```typescript
// User can add in vite.config.ts
import { getHighlighter } from 'shiki'

const highlighter = await getHighlighter({ theme: 'nord' })

inertiaContent({
  markdown: {
    highlight: (code, lang) => {
      return highlighter.codeToHtml(code, { lang })
    }
  }
})
```

### 4. Full-Text Search ⏳ v1.1

**Nuxt Content:** Built-in search API
**Our Package:** Manual filtering

**Priority:** High
**Planned:** v1.1
**Difficulty:** Medium

**Implementation Plan:**
- Integrate MiniSearch or FlexSearch
- Build search index at compile time
- Add search() method to ContentQuery
- Provide Vue search component

```php
// Future API
Content::query()
    ->search('laravel inertia')
    ->get()
```

### 5. Content Relationships ⏳ v1.2

**Example:**
```yaml
---
title: Post
related: [post-1, post-2]
---
```

**Status:** Not implemented
**Priority:** Low
**Planned:** v1.2

### 6. Content Transformers ⏳ v1.2

**Nuxt Content:** Can transform content before/after parsing

**Status:** Not implemented
**Priority:** Low
**Planned:** v1.2

### 7. Multi-Language Support ⏳ v1.2

**Example:**
```
content/
├── en/
│   └── intro.md
└── fa/
    └── intro.md
```

**Status:** Not implemented
**Priority:** Medium
**Planned:** v1.2

---

## Comparison with Nuxt Content

### What We Do BETTER

✅ **Performance:** No runtime parsing, build-time only
✅ **Laravel Integration:** Native, not API-based
✅ **Simplicity:** No database needed
✅ **Type Safety:** PHP + TypeScript
✅ **Server Authority:** Laravel controls access

### What Nuxt Content Does BETTER

✅ **MDC Syntax:** Vue components in Markdown
✅ **File Formats:** Multiple formats supported
✅ **Search:** Built-in full-text search
✅ **Highlighting:** Shiki integration
✅ **SQLite:** Advanced query capabilities

### Different Philosophy

| Nuxt Content | Inertia Content |
|--------------|-----------------|
| Runtime flexibility | Build-time optimization |
| SQLite database | JSON manifest |
| SSR-friendly | SPA-optimized |
| Nitro server | Laravel server |

---

## Roadmap

### v1.1 (Next Release)

**Target:** Q1 2026

- [ ] MDC syntax support
- [ ] YAML/JSON file support
- [ ] Full-text search (MiniSearch)
- [ ] Shiki syntax highlighting
- [ ] Enhanced markdown-it plugins
- [ ] Content validation

**Estimated:** 2-3 months development

### v1.2 (Future)

**Target:** Q2 2026

- [ ] Multi-language content
- [ ] Content relationships
- [ ] Content versioning
- [ ] Advanced caching strategies
- [ ] Content transformers
- [ ] CSV parsing

**Estimated:** 2-3 months development

### v2.0 (Long-term)

**Target:** Q3-Q4 2026

- [ ] GraphQL API (optional)
- [ ] REST API endpoints
- [ ] Admin UI (optional)
- [ ] Content scheduling
- [ ] Media management
- [ ] Content workflows

**Estimated:** 4-6 months development

---

## How to Add Missing Features

Users can extend functionality:

### 1. Custom Syntax Highlighting

```typescript
// vite.config.ts
import { getHighlighter } from 'shiki'

const highlighter = await getHighlighter({
  themes: ['nord', 'github-light'],
  langs: ['javascript', 'typescript', 'php', 'vue']
})

inertiaContent({
  markdown: {
    highlight: (code, lang) => highlighter.codeToHtml(code, { lang })
  }
})
```

### 2. Custom Markdown Plugins

```typescript
import markdownItAnchor from 'markdown-it-anchor'
import markdownItToc from 'markdown-it-toc-done-right'

// Fork compiler.ts and add:
md.use(markdownItAnchor)
md.use(markdownItToc)
```

### 3. Client-Side Search

```vue
<script setup>
import MiniSearch from 'minisearch'

const entries = $page.props.allEntries
const searchIndex = new MiniSearch({
  fields: ['title', '_excerpt'],
  storeFields: ['title', '_path']
})

searchIndex.addAll(entries)

const results = searchIndex.search(query)
</script>
```

---

## Contributing

Want to help add these features?

1. Check [Implementation Status](./development/implementation-status.md)
2. Read [Contributing Guide](../../CONTRIBUTING.md)
3. Open an issue to discuss
4. Submit a PR!

Priority features:
- 🔥 MDC syntax
- 🔥 Full-text search
- ⭐ Shiki highlighting
- ⭐ YAML/JSON support

---

## Conclusion

**v1.0 is production-ready** with 80% feature parity.

We've implemented all **essential features** for content management:
- ✅ Query API
- ✅ Markdown parsing
- ✅ Vue components
- ✅ HMR
- ✅ TypeScript

Advanced features (MDC, search, multi-format) are **planned for v1.1+**.

---

**References:**
- [Nuxt Content](https://github.com/nuxt/content)
- [Nuxt Content Docs](https://content.nuxt.com)
- [Our Changelog](../../CHANGELOG.md)
