# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

If you discover any security-related issues, please email **dev@farsi.dev** instead of using the issue tracker.

All security vulnerabilities will be promptly addressed.

## Security Considerations

### Content Compilation

Inertia Content compiles Markdown to Vue components at build time. This means:

✅ **Safe**: No runtime Markdown parsing or HTML rendering in PHP
✅ **Safe**: No user-generated content is evaluated at runtime
⚠️ **Note**: All compiled content is bundled in JavaScript assets

### Private Content

**Important**: Content marked as `private` or `draft` is still compiled into JavaScript bundles. While Laravel controls access, the compiled JavaScript exists in your public build directory.

**Best Practices**:
1. Use server-side middleware to protect routes
2. Don't rely solely on `draft: true` for security
3. For truly sensitive content, use separate builds or databases
4. Review `public/build/` contents before deployment

### Path Traversal

The `PathResolver` class prevents path traversal attacks:

```php
// These are blocked
Content::find('../../../etc/passwd')  // null
Content::find('../../config')          // null
```

### Manifest Integrity

The manifest file (`public/build/inertia-content-manifest.json`) contains metadata only - no content body. It's safe to expose publicly.

### Cache Security

If using Redis or other shared cache stores, ensure proper key prefixing to prevent cache poisoning attacks.

## Best Practices

1. **Keep Dependencies Updated**
   ```bash
   composer update
   npm update
   ```

2. **Use Environment Variables**
   ```env
   INERTIA_CONTENT_SHOW_DRAFTS=false  # Always in production
   ```

3. **Implement Access Control**
   ```php
   Route::middleware(['auth', 'can:view-docs'])
       ->get('/docs/{path}', ...);
   ```

4. **Validate User Input**
   ```php
   $validated = $request->validate([
       'q' => 'string|max:255'
   ]);
   ```

5. **Review Build Output**
   - Check `public/build/` for sensitive content
   - Use `.gitignore` appropriately
   - Audit manifest contents

## Disclosure Policy

- Report → Acknowledgment within 48 hours
- Fix → Released within 7 days for critical issues
- Credit → Security researchers credited in CHANGELOG

Thank you for helping keep Inertia Content secure!
