# Querying Content

Complete guide to querying and filtering content.

## Basic Query

```php
use Farsi\InertiaContent\Facades\Content;

$entries = Content::query()->get();
```

## Filtering

### By Directory

```php
// All content in docs directory
$docs = Content::query()
    ->where('_dir', 'docs')
    ->get();

// Starts with docs
$allDocs = Content::query()
    ->where('_dir', 'startsWith', 'docs')
    ->get();
```

### By Draft Status

```php
// Only published
$published = Content::query()
    ->where('draft', false)
    ->get();

// Only drafts (if show_drafts is enabled)
$drafts = Content::query()
    ->where('draft', true)
    ->get();
```

### By Custom Fields

```php
// Posts by author
$posts = Content::query()
    ->where('author', 'John Doe')
    ->get();

// Posts with tag
$tagged = Content::query()
    ->where('tags', 'contains', 'laravel')
    ->get();
```

## Sorting

### Manual Order

```markdown
---
title: First Page
order: 1
---
```

```php
$sorted = Content::query()
    ->orderBy('order', 'asc')
    ->get();
```

### By Date

```php
// Latest first
$latest = Content::query()
    ->orderBy('_createdAt', 'desc')
    ->limit(5)
    ->get();

// Recently updated
$updated = Content::query()
    ->orderBy('_updatedAt', 'desc')
    ->get();
```

### Multiple Sorts

```php
$sorted = Content::query()
    ->orderBy('_dir', 'asc')
    ->orderBy('order', 'asc')
    ->orderBy('title', 'asc')
    ->get();
```

## Limiting

### Limit

```php
// First 10
$first10 = Content::query()
    ->limit(10)
    ->get();
```

### Skip (Offset)

```php
// Page 2 (items 11-20)
$page2 = Content::query()
    ->skip(10)
    ->limit(10)
    ->get();
```

### First

```php
$first = Content::query()
    ->where('_dir', 'docs')
    ->first();
```

## Operators

### Comparison

```php
// Greater than
->where('order', '>', 5)

// Less than or equal
->where('views', '<=', 100)

// Not equal
->where('status', '!=', 'archived')
```

### String

```php
// Contains
->where('title', 'contains', 'Laravel')

// Starts with
->where('_path', 'startsWith', 'blog/')

// Ends with
->where('_path', 'endsWith', '/intro')
```

### Array

```php
// In list
->whereIn('_path', ['docs/intro', 'docs/guide', 'docs/api'])

// Not in list
->whereNot('status', 'deleted')
```

## Field Selection

### Select Specific Fields

```php
$entries = Content::query()
    ->only(['title', '_path', '_excerpt'])
    ->get();
```

### Exclude Fields

```php
$entries = Content::query()
    ->without(['_source', '_hash'])
    ->get();
```

## Navigation

### Build Menu

```php
// All navigable content
$nav = Content::query()
    ->where('navigation', true)
    ->orderBy('order')
    ->get()
    ->toNavigation();
```

### Section Navigation

```php
$docsNav = Content::query()
    ->where('_dir', 'startsWith', 'docs')
    ->where('navigation', true)
    ->orderBy('order')
    ->get()
    ->toNavigation();
```

## Practical Examples

### Blog Index with Pagination

```php
public function index(Request $request)
{
    $page = $request->input('page', 1);
    $perPage = 10;

    $allPosts = Content::query()
        ->where('_dir', 'blog')
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->get();

    $total = $allPosts->count();
    $posts = $allPosts->skip(($page - 1) * $perPage)
                      ->take($perPage);

    return Inertia::render('Blog/Index', [
        'posts' => $posts->toInertiaProps(),
        'pagination' => [
            'current' => $page,
            'total' => ceil($total / $perPage)
        ]
    ]);
}
```

### Filter by Category

```php
public function category($category)
{
    $posts = Content::query()
        ->where('_dir', 'blog')
        ->where('category', $category)
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->get();

    return Inertia::render('Blog/Category', [
        'category' => $category,
        'posts' => $posts->toInertiaProps()
    ]);
}
```

### Related Posts

```php
public function show($slug)
{
    $post = Content::findOrFail("blog/$slug");

    $related = Content::query()
        ->where('_dir', 'blog')
        ->where('_path', '!=', $post->getPath())
        ->where('category', $post->category)
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->limit(3)
        ->get();

    return Content::page("blog/$slug", 'Blog/Post')
        ->with('related', $related->toInertiaProps());
}
```

### Simple Search

```php
public function search(Request $request)
{
    $query = $request->input('q');

    $results = Content::query()
        ->where('draft', false)
        ->get()
        ->filter(fn($entry) =>
            str_contains(strtolower($entry->title), strtolower($query)) ||
            str_contains(strtolower($entry->_excerpt ?? ''), strtolower($query))
        );

    return response()->json($results->values());
}
```

### Sitemap Generation

```php
public function sitemap()
{
    $pages = Content::query()
        ->where('draft', false)
        ->get();

    return response()->view('sitemap', [
        'pages' => $pages
    ])->header('Content-Type', 'application/xml');
}
```

## Optimization

### Cache Frequent Queries

```php
use Illuminate\Support\Facades\Cache;

$nav = Cache::remember('site_navigation', 3600, function () {
    return Content::navigation();
});
```

### Eager Loading for Inertia

```php
// Instead of this:
$posts = Content::query()->get();
foreach ($posts as $post) {
    // Reads meta each time
}

// Do this:
$posts = Content::query()->get();
$postsData = $posts->toInertiaProps(); // Single operation
```

## Advanced Filtering

### Combining Conditions

```php
$posts = Content::query()
    ->where('_dir', 'blog')
    ->where('category', 'tutorials')
    ->where('draft', false)
    ->where('_createdAt', '>', strtotime('-30 days'))
    ->orderBy('views', 'desc')
    ->limit(10)
    ->get();
```

### Custom Filtering

```php
$featured = Content::query()
    ->where('_dir', 'blog')
    ->get()
    ->filter(function ($post) {
        return $post->get('featured', false) === true &&
               count($post->get('tags', [])) >= 3;
    });
```

### Grouping

```php
$postsByCategory = Content::query()
    ->where('_dir', 'blog')
    ->get()
    ->groupBy('category');

foreach ($postsByCategory as $category => $posts) {
    echo "$category: " . $posts->count() . " posts\n";
}
```
