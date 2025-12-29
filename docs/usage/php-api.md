# PHP API Reference

Complete guide to using Content in Laravel.

## Facade

```php
use Farsi\InertiaContent\Facades\Content;
```

## Finding Content

### find()

```php
// Find a single entry
$entry = Content::find('docs/intro');

if ($entry) {
    echo $entry->getTitle(); // "Introduction"
}
```

Returns `null` if not found or is draft (unless drafts are enabled).

### findOrFail()

```php
// Find or throw 404
$entry = Content::findOrFail('docs/intro');
// Throws ContentNotFoundException if not found
```

### exists()

```php
// Check existence
if (Content::exists('docs/intro')) {
    // Content exists
}
```

## Query Builder

### where()

```php
$docs = Content::query()
    ->where('_dir', 'docs')
    ->where('draft', false)
    ->get();
```

**Supported Operators:**
- `=` (default)
- `!=`
- `>`, `<`, `>=`, `<=`
- `contains` - String contains
- `startsWith` - String starts with
- `endsWith` - String ends with

```php
$posts = Content::query()
    ->where('title', 'contains', 'Laravel')
    ->where('order', '>', 5)
    ->get();
```

### whereIn()

```php
$entries = Content::query()
    ->whereIn('_path', ['docs/intro', 'docs/guide'])
    ->get();
```

### whereNot()

```php
$visible = Content::query()
    ->whereNot('navigation', false)
    ->get();
```

### orderBy()

```php
$sorted = Content::query()
    ->orderBy('order', 'asc')
    ->orderBy('title') // Default: asc
    ->get();
```

### limit() and skip()

```php
// Manual pagination
$page2 = Content::query()
    ->skip(10)
    ->limit(10)
    ->get();
```

### first()

```php
$latest = Content::query()
    ->orderBy('_createdAt', 'desc')
    ->first();
```

### count()

```php
$total = Content::query()
    ->where('_dir', 'blog')
    ->count();
```

### paths()

```php
// Get only paths
$paths = Content::query()
    ->where('draft', false)
    ->paths();
// ['docs/intro', 'docs/guide', ...]
```

## ContentEntry

### Core Methods

```php
$entry = Content::find('docs/intro');

$entry->getPath();        // "docs/intro"
$entry->getSlug();        // "intro"
$entry->getDir();         // "docs"
$entry->getTitle();       // "Introduction"
$entry->getDescription(); // "..."
$entry->getExcerpt();     // Auto-generated excerpt
$entry->getHeadings();    // [...]
$entry->isDraft();        // false
$entry->isNavigable();    // true
```

### Accessing Fields

```php
// Magic getter
$title = $entry->title;

// get() method
$custom = $entry->get('author', 'Anonymous');

// ArrayAccess
$title = $entry['title'];
```

### Converting to Array

```php
$data = $entry->toArray();
$meta = $entry->getMeta(); // Same as toArray
```

## Inertia Integration

### page()

```php
Route::get('/docs/{path}', function ($path) {
    return Content::page("docs/$path");
});
```

Returns an Inertia response with:
```php
[
    'contentKey' => 'docs/intro',
    'contentMeta' => [...],
    'contentVersion' => 'abc123'
]
```

### pageOrFail()

```php
Route::get('/docs/{path}', function ($path) {
    return Content::pageOrFail("docs/$path");
    // Throws 404 if not found
});
```

### Custom Component

```php
return Content::page('docs/intro', 'Docs/CustomPage');
```

## Navigation

```php
// All content
$nav = Content::navigation();

// Specific directory
$docsNav = Content::navigation('docs');
```

Returns:
```php
[
    [
        'title' => 'Introduction',
        'path' => 'docs/intro',
        'order' => 1,
        'children' => [...]
    ],
    // ...
]
```

## Cache Management

```php
// Clear cache
Content::clearCache();

// Get version
$version = Content::getVersion();
```

## Practical Examples

### Blog Post List

```php
public function index()
{
    $posts = Content::query()
        ->where('_dir', 'blog')
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->limit(10)
        ->get();

    return Inertia::render('Blog/Index', [
        'posts' => $posts->toInertiaProps()
    ]);
}
```

### Single Post Page

```php
Route::get('/blog/{slug}', function ($slug) {
    return Content::pageOrFail("blog/$slug", 'Blog/Post');
});
```

### Search

```php
public function search(Request $request)
{
    $query = $request->input('q');

    $results = Content::query()
        ->where('draft', false)
        ->get()
        ->filter(function ($entry) use ($query) {
            return str_contains($entry->title, $query) ||
                   str_contains($entry->_excerpt, $query);
        });

    return response()->json($results);
}
```

### Navigation Menu

```php
View::composer('layouts.app', function ($view) {
    $view->with('navigation', Content::navigation('docs'));
});
```

## Collection Methods

The `ContentCollection` class extends Laravel's Collection with additional methods:

```php
$entries = Content::query()->get();

// Get paths
$paths = $entries->paths();

// Convert to navigation tree
$tree = $entries->toNavigation();

// Group by directory
$grouped = $entries->groupByDir();

// Filter drafts
$published = $entries->filterDrafts();

// Convert for Inertia
$props = $entries->toInertiaProps();
```
