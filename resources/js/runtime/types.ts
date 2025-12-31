/**
 * Content Entry structure matching Nuxt Content v2
 */
export interface ContentEntry {
  // Identity
  _id: string
  _path: string
  _slug: string
  _dir: string
  _file: string
  _extension: string

  // Required metadata
  title: string

  // Optional metadata
  description?: string
  draft?: boolean
  navigation?: boolean
  order?: number

  // Generated fields
  _excerpt?: string
  _headings: Heading[]
  _hash: string
  _createdAt: number
  _updatedAt: number

  // Custom frontmatter fields
  [key: string]: unknown
}

/**
 * Heading structure for table of contents
 */
export interface Heading {
  depth: 2 | 3 | 4
  text: string
  id: string
  children?: Heading[]
}

/**
 * Manifest entry in the compiled manifest
 */
export interface ManifestEntry {
  path: string
  meta: ContentEntry
  chunk: string
}

/**
 * Complete manifest structure
 */
export interface ContentManifest {
  version: string
  generatedAt: number
  entries: Record<string, ManifestEntry>
}

/**
 * Markdown-it configuration
 */
export interface MarkdownOptions {
  linkify?: boolean
  typographer?: boolean
  highlight?: (code: string, lang: string) => string
  html?: boolean
}

/**
 * Heading extraction configuration
 */
export interface HeadingOptions {
  depth?: (2 | 3 | 4)[]
  anchorLinks?: boolean
}

/**
 * Excerpt generation configuration
 */
export interface ExcerptOptions {
  maxLength?: number
  separator?: string
}

/**
 * Vite plugin configuration
 */
export interface InertiaContentOptions {
  contentDir?: string
  manifestPath?: string
  markdown?: MarkdownOptions
  headings?: HeadingOptions
  excerpt?: ExcerptOptions
  ignore?: string[]
}

