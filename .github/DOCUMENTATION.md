# Documentation Guide

## For Users

All user documentation is in the [`docs/`](../docs/) directory:

- **Quick Start**: [docs/getting-started.md](../docs/getting-started.md)
- **Installation**: [docs/installation.md](../docs/installation.md)
- **Usage Guides**: [docs/usage/](../docs/usage/)
- **Examples**: [docs/examples/](../docs/examples/)

## For Contributors

Contributing documentation is in the root:

- **Contributing**: [CONTRIBUTING.md](../CONTRIBUTING.md)
- **Code of Conduct**: [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md)
- **Security**: [SECURITY.md](../SECURITY.md)

## For Maintainers

Development and architecture docs:

- **Architecture**: [docs/architecture.md](../docs/architecture.md)
- **Publishing**: [docs/publishing.md](../docs/publishing.md)
- **Development**: [docs/development/](../docs/development/)

---

## Documentation Structure

```
Root Level (Public-facing)
├── README.md              # Main package README
├── LICENSE.md             # MIT License
├── CHANGELOG.md           # Version history
├── CONTRIBUTING.md        # How to contribute
└── SECURITY.md            # Security policy

docs/ (User Documentation)
├── README.md              # Docs index
├── getting-started.md     # Quick start
├── installation.md        # Installation guide
├── configuration.md       # Config reference
├── architecture.md        # Package design
├── publishing.md          # Publishing guide
├── usage/                 # Usage guides
│   ├── php-api.md
│   ├── vue-components.md
│   └── querying.md
├── examples/              # Complete examples
│   ├── blog.md
│   └── documentation.md
└── development/           # Dev docs
    ├── implementation-status.md
    ├── ready-for-release.md
    └── final-summary.md

.github/ (GitHub-specific)
├── CODE_OF_CONDUCT.md     # Community guidelines
├── ISSUE_TEMPLATE/        # Issue templates
├── PULL_REQUEST_TEMPLATE.md
└── workflows/             # CI/CD
```

---

## Writing Documentation

### Style Guide

- Use **clear, concise** language
- Include **code examples** for every feature
- Add **practical use cases**
- Keep examples **up-to-date** with package version

### Format

- Use **Markdown** for all docs
- Use **code fences** with language tags
- Add **headings** for navigation
- Include **links** to related docs

### Examples

```markdown
# Feature Name

Brief description of the feature.

## Usage

```php
// Code example
Content::query()->get();
```

## Practical Example

Real-world use case...
```

---

## Updating Documentation

When making changes:

1. **Update user docs** if API changes
2. **Add examples** for new features
3. **Update CHANGELOG.md** with changes
4. **Test all code examples** before committing
5. **Check links** are not broken

---

## Documentation Priorities

1. **README.md** - First impression, keep concise
2. **Getting Started** - Must work perfectly
3. **Usage Guides** - Complete API reference
4. **Examples** - Real-world applications
5. **Development Docs** - For maintainers only

