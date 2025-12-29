# Package Structure

## Root Level (Clean & Organized)

```
inertia-content/
├── 📄 Essential Files
│   ├── README.md              # Main documentation
│   ├── LICENSE.md             # MIT License
│   ├── CHANGELOG.md           # Version history
│   ├── CONTRIBUTING.md        # Contribution guide
│   └── SECURITY.md            # Security policy
│
├── 🏗️ Source Code
│   ├── src/                   # PHP source (13 files)
│   ├── resources/
│   │   ├── js/               # TypeScript/Vue source (12 files)
│   │   └── stubs/            # Publishable stubs
│   ├── config/               # Laravel configuration
│   └── routes/               # Laravel routes
│
├── 📚 Documentation
│   └── docs/
│       ├── README.md                  # Documentation index
│       ├── getting-started.md         # Quick start
│       ├── installation.md            # Installation guide
│       ├── configuration.md           # Configuration
│       ├── architecture.md            # Package design
│       ├── publishing.md              # Publishing guide
│       ├── usage/                     # Usage guides
│       │   ├── php-api.md
│       │   ├── vue-components.md
│       │   └── querying.md
│       ├── examples/                  # Examples
│       │   ├── blog.md
│       │   └── documentation.md
│       └── development/               # Dev docs (excluded from package)
│           ├── README.md
│           ├── implementation-status.md
│           ├── ready-for-release.md
│           └── final-summary.md
│
├── 🧪 Tests & CI
│   ├── tests/                 # PHP & JS tests
│   ├── phpunit.xml           # PHPUnit config
│   └── .github/              # GitHub Actions & templates
│
└── ⚙️ Config Files
    ├── composer.json         # Composer package
    ├── tsconfig.json         # TypeScript config
    └── tsup.config.ts        # Build config
```

---

## Documentation Organization

### Public Documentation (Included in package)

```
docs/
├── README.md                  # Main docs index
├── getting-started.md         # For new users
├── installation.md            # Installation steps
├── configuration.md           # Config reference
├── architecture.md            # Package design
├── publishing.md              # For maintainers
├── usage/                     # How-to guides
│   ├── php-api.md
│   ├── vue-components.md
│   └── querying.md
└── examples/                  # Real-world examples
    ├── blog.md
    └── documentation.md
```

### Development Documentation (Excluded from package)

```
docs/development/              # Internal docs
├── README.md                  # Dev docs index
├── implementation-status.md   # What's complete
├── ready-for-release.md       # Release checklist
└── final-summary.md           # Package summary

docs/plan/                     # Architecture specs (if exists)
└── (design documents)
```

---

## What Gets Published

### Via Composer (Packagist)

✅ **Included:**
- `src/` - PHP source code
- `config/` - Configuration files
- `routes/` - Route definitions
- `resources/stubs/` - Publishable stubs
- `resources/js/` - TypeScript/Vue source
- `docs/` (except `development/` and `plan/`)
- `README.md`, `LICENSE.md`, `CHANGELOG.md`
- `CONTRIBUTING.md`, `SECURITY.md`

❌ **Excluded** (via `.gitattributes`):
- `tests/` - Test files
- `.github/` - GitHub-specific files
- `docs/development/` - Internal docs
- `docs/plan/` - Design documents
- `phpunit.xml`, `tsconfig.json`, `tsup.config.ts`
- Development configuration files

---

## File Purposes

| File/Directory | Purpose | Audience |
|----------------|---------|----------|
| `README.md` | Package overview | Everyone |
| `docs/` | User documentation | Users |
| `docs/development/` | Development docs | Maintainers |
| `src/` | PHP source | Laravel |
| `resources/js/` | JS/Vue source | Vite |
| `resources/stubs/` | Publishable files | Laravel artisan |
| `tests/` | Test suite | Developers |
| `.github/` | GitHub automation | GitHub |

---

## Clean Root Directory

The root directory contains only essential files:

```
./
├── README.md          # Main docs
├── LICENSE.md         # License
├── CHANGELOG.md       # Changes
├── CONTRIBUTING.md    # How to contribute
├── SECURITY.md        # Security policy
├── composer.json      # PHP package
├── tsconfig.json      # TypeScript
├── tsup.config.ts     # Build config
└── phpunit.xml        # Tests
```

All other documentation is in `docs/`.

---

## Benefits of This Structure

✅ **Clean Root** - Only essential files
✅ **Organized Docs** - Everything in `docs/`
✅ **Clear Separation** - User vs Developer docs
✅ **Proper Distribution** - Only ship what's needed
✅ **Easy Navigation** - Logical folder structure

---

**Last Updated**: December 2025
