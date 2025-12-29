<script setup lang="ts">
import type { ContentEntry } from './types'

const props = withDefaults(
  defineProps<{
    entries: ContentEntry[]
    tag?: string
  }>(),
  {
    tag: 'div',
  }
)
</script>

<template>
  <component :is="tag" class="content-list">
    <slot v-if="entries.length === 0" name="empty">
      <p class="text-gray-500">No content found.</p>
    </slot>

    <slot v-else :entries="entries">
      <template v-for="(entry, index) in entries" :key="entry._id">
        <slot name="item" :entry="entry" :index="index">
          <article class="content-list-item mb-6">
            <h2 class="text-2xl font-semibold">{{ entry.title }}</h2>
            <p v-if="entry._excerpt" class="text-gray-600 mt-2">{{ entry._excerpt }}</p>
          </article>
        </slot>
      </template>
    </slot>
  </component>
</template>
