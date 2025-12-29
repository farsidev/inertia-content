# Changelog

All notable changes to `inertia-content` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- Fixed critical bug where virtual modules weren't emitted in production build
- Content entries now written to temporary directory for production builds
- Dynamic imports now work correctly in production mode

## [1.0.0-alpha] - 2025-12-29

**Status**: Alpha release - Code complete, needs testing

This is the initial alpha release of Inertia Content. The package is feature-complete but has not been tested in production environments.

### Known Issues
- ⚠️ Virtual modules don't work in production build (fixed in unreleased)

### Added
- Initial package structure
- File-based content management with Markdown
- Build-time compilation to Vue components
- Nuxt Content-compatible PHP query API
- Vue composables and components
  - `useContent` composable
  - `ContentRenderer` component
  - `ContentDoc` component
  - `ContentList` component
- Vite plugin with HMR support
- TypeScript support (strict mode)
- Artisan install command
- Artisan cache clear command
- Content caching system
- Navigation tree generation
- Draft content support
- Frontmatter parsing (YAML)
- Automatic heading extraction (H2-H4)
- Automatic excerpt generation
- Table of Contents support
- Path traversal protection
- Virtual module system for Vite
- Manifest generation and loading
- Complete documentation (English)
- Example implementations (Blog + Documentation site)
- GitHub Actions CI workflow
- Security policy
- Contributing guidelines
- Code of Conduct

### Features
- Query content with fluent API (`where`, `orderBy`, `limit`, etc.)
- Filter by directory, draft status, custom fields
- Sort by any field with multiple sort criteria
- Pagination support (skip/limit)
- Content Entry with full metadata
- Content Collection with navigation helpers
- Inertia.js integration (`Content::page()`)
- Hot Module Replacement in development
- Production-ready caching
- Automatic content hash for cache busting
- Server-side access control
- Client-side component rendering

### Developer Experience
- Single Composer installation
- JavaScript included in package
- No separate npm package needed
- Vite alias support (`@inertia-content`)
- Full TypeScript types
- IDE autocomplete support
- Clear error messages

---

## Release Notes

### [1.0.0-alpha] - December 29, 2025

**First alpha release!** 🎉

This release includes complete implementation of core features but requires testing before production use.

**Installation (Alpha):**
```bash
composer require farsi/inertia-content:@dev
```

**⚠️ Warning**: This is an alpha release. Not recommended for production use.

---

## Versioning Strategy

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (x.0.0): Breaking changes
- **MINOR** (1.x.0): New features (backward compatible)
- **PATCH** (1.0.x): Bug fixes

---

## Future Roadmap

### v1.1 (Planned)
- [ ] MDC (Markdown Components) support
- [ ] Full-text search integration
- [ ] Code syntax highlighting
- [ ] Custom markdown-it plugins support
- [ ] Content relationships (prev/next)

### v1.2 (Planned)
- [ ] Multi-language content support
- [ ] Content versioning
- [ ] Content collections API
- [ ] Advanced caching strategies
- [ ] Content transformers

### v2.0 (Future)
- [ ] Multiple content formats (JSON, YAML)
- [ ] Content API endpoints
- [ ] GraphQL integration
- [ ] Advanced search with indexing

---

## Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for how to contribute to this changelog.

When making changes:
1. Add entry under `[Unreleased]` section
2. Use categories: Added, Changed, Deprecated, Removed, Fixed, Security
3. Link to issues/PRs when applicable

---

**Maintained by**: [Farsi Dev](https://github.com/farsidev)
**License**: [MIT](./LICENSE.md)
