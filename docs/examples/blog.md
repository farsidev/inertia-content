# Example: Blog System

Complete blog system built with Inertia Content.

## File Structure

```
resources/content/blog/
├── 2024-01-15-first-post.md
├── 2024-01-20-second-post.md
└── 2024-02-01-latest-post.md
```

## Sample Content

```markdown
---
title: My First Post
description: Starting a new journey
author: John Doe
category: Programming
tags: [laravel, vue, inertia]
image: /images/posts/first-post.jpg
order: 1
---

## Introduction

This is my first post...
```

## Routes

```php
// routes/web.php
use Farsi\InertiaContent\Facades\Content;

// Post list
Route::get('/blog', function () {
    $posts = Content::query()
        ->where('_dir', 'blog')
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->get();

    return Inertia::render('Blog/Index', [
        'posts' => $posts->toInertiaProps()
    ]);
});

// Single post
Route::get('/blog/{slug}', function ($slug) {
    $post = Content::findOrFail("blog/$slug");

    // Related posts
    $related = Content::query()
        ->where('_dir', 'blog')
        ->where('_path', '!=', $post->getPath())
        ->where('category', $post->category)
        ->limit(3)
        ->get();

    return Inertia::render('Blog/Post', [
        'contentKey' => $post->getPath(),
        'contentMeta' => $post->getMeta(),
        'related' => $related->toInertiaProps()
    ]);
});

// Category filter
Route::get('/blog/category/{category}', function ($category) {
    $posts = Content::query()
        ->where('_dir', 'blog')
        ->where('category', $category)
        ->orderBy('_createdAt', 'desc')
        ->get();

    return Inertia::render('Blog/Category', [
        'category' => $category,
        'posts' => $posts->toInertiaProps()
    ]);
});
```

## Vue Components

### Blog Index

```vue
<!-- resources/js/Pages/Blog/Index.vue -->
<script setup lang="ts">
import { ContentList } from '@inertia-content'
import { Link } from '@inertiajs/vue3'
import type { ContentEntry } from '@inertia-content'

defineProps<{
  posts: ContentEntry[]
}>()
</script>

<template>
  <div class="container mx-auto py-12">
    <h1 class="text-4xl font-bold mb-8">Blog</h1>

    <ContentList :entries="posts">
      <template #default="{ entries }">
        <div class="grid md:grid-cols-2 gap-6">
          <article
            v-for="post in entries"
            :key="post._id"
            class="bg-white rounded-lg shadow-md overflow-hidden"
          >
            <img
              v-if="post.image"
              :src="post.image"
              :alt="post.title"
              class="w-full h-48 object-cover"
            />
            <div class="p-6">
              <div class="flex gap-2 mb-2">
                <Link
                  :href="`/blog/category/${post.category}`"
                  class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded"
                >
                  {{ post.category }}
                </Link>
              </div>
              <Link :href="`/blog/${post._slug}`">
                <h2 class="text-2xl font-semibold hover:text-blue-600">
                  {{ post.title }}
                </h2>
              </Link>
              <p class="text-gray-600 mt-2">{{ post._excerpt }}</p>
              <div class="flex justify-between items-center mt-4">
                <span class="text-sm text-gray-500">{{ post.author }}</span>
                <time class="text-sm text-gray-500">
                  {{ new Date(post._createdAt).toLocaleDateString() }}
                </time>
              </div>
            </div>
          </article>
        </div>
      </template>
    </ContentList>
  </div>
</template>
```

### Blog Post

```vue
<!-- resources/js/Pages/Blog/Post.vue -->
<script setup lang="ts">
import { ContentDoc, ContentList } from '@inertia-content'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <div class="container mx-auto py-12">
    <ContentDoc :content-key="page.props.contentKey">
      <template #header="{ meta }">
        <header class="mb-12">
          <Link
            :href="`/blog/category/${meta.category}`"
            class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded mb-4"
          >
            {{ meta.category }}
          </Link>
          <h1 class="text-5xl font-bold mb-4">{{ meta.title }}</h1>
          <p class="text-xl text-gray-600">{{ meta.description }}</p>
          <div class="flex gap-4 mt-6 text-gray-500">
            <span>By {{ meta.author }}</span>
            <time>{{ new Date(meta._createdAt).toLocaleDateString() }}</time>
          </div>
          <img
            v-if="meta.image"
            :src="meta.image"
            :alt="meta.title"
            class="w-full rounded-lg mt-8"
          />
        </header>
      </template>

      <template #toc="{ headings }">
        <aside v-if="headings.length" class="mb-8 p-6 bg-gray-50 rounded-lg">
          <h4 class="font-semibold mb-3">Table of Contents</h4>
          <ul class="space-y-2">
            <li v-for="h in headings" :key="h.id">
              <a :href="`#${h.id}`" class="text-blue-600 hover:underline">
                {{ h.text }}
              </a>
            </li>
          </ul>
        </aside>
      </template>

      <template #default="{ component }">
        <component :is="component" class="prose prose-lg max-w-none" />
      </template>

      <template #footer="{ meta }">
        <footer class="mt-12 pt-8 border-t">
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in meta.tags"
              :key="tag"
              class="bg-gray-100 px-3 py-1 rounded-full text-sm"
            >
              #{{ tag }}
            </span>
          </div>
        </footer>
      </template>
    </ContentDoc>

    <!-- Related posts -->
    <div v-if="page.props.related?.length" class="mt-16">
      <h3 class="text-2xl font-bold mb-6">Related Posts</h3>
      <ContentList :entries="page.props.related">
        <template #default="{ entries }">
          <div class="grid md:grid-cols-3 gap-6">
            <Link
              v-for="post in entries"
              :key="post._id"
              :href="`/blog/${post._slug}`"
              class="block bg-white p-4 rounded-lg shadow hover:shadow-lg transition"
            >
              <h4 class="font-semibold">{{ post.title }}</h4>
              <p class="text-sm text-gray-600 mt-2">{{ post._excerpt }}</p>
            </Link>
          </div>
        </template>
      </ContentList>
    </div>
  </div>
</template>
```

## Adding Search

```vue
<!-- components/BlogSearch.vue -->
<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const search = ref('')

watch(search, (value) => {
  router.get('/blog', { q: value }, {
    preserveState: true,
    replace: true
  })
})
</script>

<template>
  <input
    v-model="search"
    type="search"
    placeholder="Search posts..."
    class="w-full px-4 py-2 border rounded-lg"
  />
</template>
```

```php
// In route
Route::get('/blog', function (Request $request) {
    $query = Content::query()
        ->where('_dir', 'blog')
        ->where('draft', false);

    if ($q = $request->input('q')) {
        $posts = $query->get()->filter(fn($post) =>
            str_contains(strtolower($post->title), strtolower($q))
        );
    } else {
        $posts = $query->orderBy('_createdAt', 'desc')->get();
    }

    return Inertia::render('Blog/Index', [
        'posts' => $posts->toInertiaProps(),
        'search' => $q
    ]);
});
```

## RSS Feed

```php
Route::get('/blog/feed', function () {
    $posts = Content::query()
        ->where('_dir', 'blog')
        ->where('draft', false)
        ->orderBy('_createdAt', 'desc')
        ->limit(20)
        ->get();

    return response()->view('blog.feed', [
        'posts' => $posts
    ])->header('Content-Type', 'application/xml');
});
```

```xml
<!-- resources/views/blog/feed.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>My Blog</title>
    <link>{{ url('/blog') }}</link>
    <description>Latest posts</description>
    @foreach($posts as $post)
    <item>
      <title>{{ $post->title }}</title>
      <link>{{ url("/blog/{$post->_slug}") }}</link>
      <description>{{ $post->_excerpt }}</description>
      <pubDate>{{ date('r', $post->_createdAt) }}</pubDate>
    </item>
    @endforeach
  </channel>
</rss>
```
