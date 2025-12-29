<script setup lang="ts">
import { ContentDoc } from 'farsi-inertia-content'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-12">
      <ContentDoc :content-key="page.props.contentKey">
        <template #header="{ meta }">
          <header class="mb-12">
            <h1 class="text-5xl font-bold text-gray-900">
              {{ meta.title }}
            </h1>
            <p v-if="meta.description" class="text-xl text-gray-600 mt-4">
              {{ meta.description }}
            </p>
          </header>
        </template>

        <template #toc="{ headings }">
          <aside v-if="headings.length > 0" class="mb-8 p-6 bg-white rounded-lg shadow-sm">
            <h4 class="text-lg font-semibold mb-4">On this page</h4>
            <ul class="space-y-2">
              <li v-for="h in headings" :key="h.id">
                <a
                  :href="`#${h.id}`"
                  class="text-gray-600 hover:text-gray-900 transition-colors"
                >
                  {{ h.text }}
                </a>
                <ul v-if="h.children && h.children.length > 0" class="ml-4 mt-1 space-y-1">
                  <li v-for="child in h.children" :key="child.id">
                    <a
                      :href="`#${child.id}`"
                      class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
                    >
                      {{ child.text }}
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </aside>
        </template>

        <template #default="{ component }">
          <div class="prose prose-lg prose-gray max-w-none">
            <component :is="component" />
          </div>
        </template>

        <template #footer="{ meta }">
          <footer class="mt-16 pt-8 border-t border-gray-200">
            <time class="text-sm text-gray-500">
              Last updated: {{ new Date(meta._updatedAt).toLocaleDateString() }}
            </time>
          </footer>
        </template>
      </ContentDoc>
    </div>
  </div>
</template>
