<script setup lang="ts">
import { toRef } from 'vue'
import { useContent } from './useContent'

const props = withDefaults(
  defineProps<{
    contentKey: string
    proseClass?: string
    tag?: string
  }>(),
  {
    proseClass: 'prose',
    tag: 'article',
  }
)

const { component, meta, headings, isLoading, error } = useContent(toRef(props, 'contentKey'))
</script>

<template>
  <component :is="tag" :class="proseClass">
    <!-- Loading state -->
    <slot v-if="isLoading" name="loading">
      <div class="animate-pulse">Loading content...</div>
    </slot>

    <!-- Not found state -->
    <slot v-else-if="error?.message?.includes('not found')" name="not-found">
      <div class="text-gray-500">Content not found</div>
    </slot>

    <!-- Error state -->
    <slot v-else-if="error" name="error" :error="error">
      <div class="text-red-500">Error loading content: {{ error.message }}</div>
    </slot>

    <!-- Content -->
    <slot v-else :component="component" :meta="meta" :headings="headings">
      <component :is="component" />
    </slot>
  </component>
</template>
