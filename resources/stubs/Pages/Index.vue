<script setup lang="ts">
import { ContentList } from 'farsi-inertia-content'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-12">
      <header class="mb-12">
        <h1 class="text-5xl font-bold text-gray-900">Documentation</h1>
        <p class="text-xl text-gray-600 mt-4">
          Browse our collection of articles and guides
        </p>
      </header>

      <ContentList :entries="page.props.entries">
        <template #default="{ entries }">
          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="entry in entries"
              :key="entry._id"
              class="p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
            >
              <Link :href="`/${entry._path}`" class="group">
                <h2 class="text-2xl font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                  {{ entry.title }}
                </h2>
                <p v-if="entry._excerpt" class="mt-3 text-gray-600 line-clamp-3">
                  {{ entry._excerpt }}
                </p>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                  <time>{{ new Date(entry._updatedAt).toLocaleDateString() }}</time>
                </div>
              </Link>
            </article>
          </div>
        </template>

        <template #empty>
          <div class="text-center py-12">
            <p class="text-xl text-gray-500">No content found.</p>
            <p class="mt-2 text-gray-400">
              Create your first markdown file in <code>resources/content</code>
            </p>
          </div>
        </template>
      </ContentList>
    </div>
  </div>
</template>
