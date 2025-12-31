/**
 * Inertia Content - Main exports
 */

// Runtime composables and components
export { useContent } from './runtime/useContent'
export { default as ContentRenderer } from './runtime/ContentRenderer.vue'
export { default as ContentDoc } from './runtime/ContentDoc.vue'
export { default as ContentList } from './runtime/ContentList.vue'

// Types
export type {
  ContentEntry,
  ContentManifest,
  ManifestEntry,
  Heading,
  InertiaContentOptions,
  MarkdownOptions,
  HeadingOptions,
  ExcerptOptions,
} from './runtime/types'

// Re-export manifest utilities (for advanced usage)
// @ts-expect-error - virtual module
export { manifest, version, getEntry, getAllPaths } from 'virtual:inertia-content/manifest'

