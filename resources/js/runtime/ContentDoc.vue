<script setup lang="ts">
import { toRef, computed } from 'vue'
import { useContent } from './useContent'

const props = withDefaults(
  defineProps<{
    contentKey: string
    tag?: string
  }>(),
  {
    tag: 'article',
  }
)

const { component, meta, headings, isLoading, error } = useContent(toRef(props, 'contentKey'))

// Format date helper
const formattedDate = computed(() => {
  if (!meta.value?._updatedAt) return null
  return new Date(meta.value._updatedAt).toLocaleDateString()
})
</script>

<template>
  <component :is="tag" class="content-doc">
    <!-- Loading -->
    <template v-if="isLoading">
      <slot name="loading">
        <div class="content-doc-loading">
          <div class="animate-pulse space-y-4">
            <div class="h-8 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="space-y-2 mt-6">
              <div class="h-4 bg-gray-200 rounded"></div>
              <div class="h-4 bg-gray-200 rounded"></div>
              <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
          </div>
        </div>
      </slot>
    </template>

    <!-- Error -->
    <template v-else-if="error">
      <slot v-if="error.message?.includes('not found')" name="not-found">
        <div class="content-doc-not-found">
          <h1>Content Not Found</h1>
          <p>The requested content could not be found.</p>
        </div>
      </slot>
      <slot v-else name="error" :error="error">
        <div class="content-doc-error">
          <h1>Error</h1>
          <p>{{ error.message }}</p>
        </div>
      </slot>
    </template>

    <!-- Content -->
    <template v-else>
      <!-- Header -->
      <slot name="header" :meta="meta">
        <header class="content-doc-header mb-8">
          <h1 class="text-4xl font-bold">{{ meta?.title }}</h1>
          <p v-if="meta?.description" class="text-xl text-gray-600 mt-2">
            {{ meta.description }}
          </p>
        </header>
      </slot>

      <!-- Table of Contents -->
      <slot name="toc" :headings="headings" />

      <!-- Main Content -->
      <div class="content-doc-body">
        <slot :component="component" :meta="meta" :headings="headings">
          <component :is="component" class="prose prose-lg max-w-none" />
        </slot>
      </div>

      <!-- Footer -->
      <slot name="footer" :meta="meta">
        <footer v-if="formattedDate" class="content-doc-footer mt-12 pt-6 border-t">
          <time class="text-gray-500">Last updated: {{ formattedDate }}</time>
        </footer>
      </slot>
    </template>
  </component>
</template>
