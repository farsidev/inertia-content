import {
  ref,
  shallowRef,
  toRef,
  watch,
  unref,
  type Ref,
  type ShallowRef,
  type MaybeRef,
} from 'vue'
import type { ContentEntry, Heading } from './types'

// Import virtual manifest module
// @ts-expect-error - virtual module
import { manifest } from 'virtual:inertia-content/manifest'

interface UseContentReturn {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  component: ShallowRef<any | null>
  meta: Ref<ContentEntry | null>
  headings: Ref<Heading[]>
  isLoading: Ref<boolean>
  error: Ref<Error | null>
  refresh: () => Promise<void>
}

/**
 * Composable to load and use content by key
 */
export function useContent(contentKey: MaybeRef<string>): UseContentReturn {
  const key = toRef(contentKey)
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const component = shallowRef<any | null>(null)
  const meta = ref<ContentEntry | null>(null)
  const headings = ref<Heading[]>([])
  const isLoading = ref(false)
  const error = ref<Error | null>(null)

  async function load(): Promise<void> {
    const path = unref(key)
    if (!path) return

    isLoading.value = true
    error.value = null

    try {
      // Check if entry exists in manifest
      const entry = manifest.entries[path]
      if (!entry) {
        throw new Error(`Content not found: ${path}`)
      }

      // Dynamically import the content component
      const module = await import(
        /* @vite-ignore */
        `virtual:inertia-content/entry/${path}`
      )

      component.value = module.default
      meta.value = module.meta
      headings.value = module.headings || []
    } catch (e) {
      error.value = e as Error
      component.value = null
      meta.value = null
      headings.value = []
    } finally {
      isLoading.value = false
    }
  }

  // Watch for key changes
  watch(key, load, { immediate: true })

  // HMR support
  if (import.meta.hot) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    import.meta.hot.on('inertia-content:update', (data: any) => {
      if (data.path === unref(key)) {
        console.log('[inertia-content] HMR update:', data.path)
        load()
      }
    })

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    import.meta.hot.on('inertia-content:remove', (data: any) => {
      if (data.path === unref(key)) {
        console.log('[inertia-content] Content removed:', data.path)
        error.value = new Error(`Content not found: ${data.path}`)
        component.value = null
        meta.value = null
        headings.value = []
      }
    })
  }

  return {
    component,
    meta,
    headings,
    isLoading,
    error,
    refresh: load,
  }
}
