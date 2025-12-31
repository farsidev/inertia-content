import matter from 'gray-matter'
import MarkdownIt from 'markdown-it'
import { createHash } from '../utils/hash'
import { slugify } from '../utils/slugify'
import type {
  ContentEntry,
  Heading,
  MarkdownOptions,
  HeadingOptions,
  ExcerptOptions,
} from '../runtime/types'
import path from 'node:path'
import fs from 'node:fs'

interface CompileResult {
  meta: ContentEntry
  headings: Heading[]
  vueComponent: string
}

/**
 * Compile markdown file to Vue component with metadata
 */
export function compileMarkdown(
  source: string,
  filePath: string,
  contentDir: string,
  options: {
    markdown?: MarkdownOptions
    headings?: HeadingOptions
    excerpt?: ExcerptOptions
  } = {}
): CompileResult {
  // Parse frontmatter
  const { data: frontmatter, content } = matter(source)

  // Extract headings
  const headings = extractHeadings(content, options.headings)

  // Generate excerpt
  const excerpt = frontmatter.excerpt || extractExcerpt(content, options.excerpt)

  // Compile markdown to HTML
  const md = createMarkdownIt(options.markdown || {}, options.headings)
  const html = md.render(content)

  // Build metadata
  const meta = buildMeta(filePath, contentDir, frontmatter, headings, excerpt, source)

  // Generate Vue SFC
  const vueComponent = generateVueSFC(html, meta)

  return { meta, headings, vueComponent }
}

/**
 * Extract headings from markdown content
 */
function extractHeadings(
  content: string,
  options?: HeadingOptions
): Heading[] {
  const allowedDepths = options?.depth || [2, 3, 4]
  const headings: Heading[] = []

  // Regex to match markdown headings
  const headingRegex = /^(#{2,4})\s+(.+)$/gm
  let match

  while ((match = headingRegex.exec(content)) !== null) {
    const depth = match[1].length as 2 | 3 | 4
    const text = match[2].trim()

    if (allowedDepths.includes(depth)) {
      headings.push({
        depth,
        text,
        id: slugify(text),
      })
    }
  }

  // Build nested structure
  return buildHeadingTree(headings)
}

/**
 * Build nested heading tree
 */
function buildHeadingTree(headings: Heading[]): Heading[] {
  const tree: Heading[] = []
  const stack: Heading[] = []

  for (const heading of headings) {
    // Remove deeper headings from stack
    while (stack.length > 0 && stack[stack.length - 1].depth >= heading.depth) {
      stack.pop()
    }

    if (stack.length === 0) {
      tree.push(heading)
    } else {
      const parent = stack[stack.length - 1]
      parent.children = parent.children || []
      parent.children.push(heading)
    }

    stack.push(heading)
  }

  return tree
}

/**
 * Extract excerpt from content
 */
function extractExcerpt(
  content: string,
  options?: ExcerptOptions
): string {
  const maxLength = options?.maxLength || 200
  const separator = options?.separator || '\n\n'

  // Get first paragraph (before double newline)
  const firstParagraph = content.split(separator)[0]

  // Remove markdown syntax
  let excerpt = firstParagraph
    .replace(/^#+\s+/, '') // Remove headings
    .replace(/\[([^\]]+)\]\([^\)]+\)/g, '$1') // Remove links
    .replace(/[*_~`]/g, '') // Remove emphasis
    .trim()

  // Truncate if needed
  if (excerpt.length > maxLength) {
    excerpt = excerpt.substring(0, maxLength).trim() + '...'
  }

  return excerpt
}

/**
 * Create and configure markdown-it instance
 */
function createMarkdownIt(
  options: MarkdownOptions,
  headingOptions?: HeadingOptions
): MarkdownIt {
  const md = new MarkdownIt({
    html: options.html ?? false,
    linkify: options.linkify ?? true,
    typographer: options.typographer ?? true,
  })

  // Add heading anchors if enabled
  if (headingOptions?.anchorLinks !== false) {
    md.renderer.rules.heading_open = (tokens, idx) => {
      const token = tokens[idx]
      const textToken = tokens[idx + 1]
      const content = textToken?.content || ''
      const id = slugify(content)
      const tag = token.tag

      return `<${tag} id="${id}">`
    }
  }

  // Custom highlight if provided
  if (options.highlight) {
    md.options.highlight = options.highlight
  }

  return md
}

/**
 * Build content entry metadata
 */
function buildMeta(
  filePath: string,
  contentDir: string,
  frontmatter: Record<string, any>,
  headings: Heading[],
  excerpt: string,
  source: string
): ContentEntry {
  const relativePath = path.relative(contentDir, filePath)
  const pathWithoutExt = relativePath.replace(/\.md$/, '')
  const parts = pathWithoutExt.split(path.sep)
  const file = parts[parts.length - 1]
  const dir = parts.slice(0, -1).join('/')

  const stats = fs.statSync(filePath)

  return {
    _id: createHash(pathWithoutExt),
    _path: pathWithoutExt.replace(/\\/g, '/'),
    _slug: file,
    _dir: dir.replace(/\\/g, '/'),
    _file: file,
    _extension: 'md',
    _excerpt: excerpt,
    _headings: headings,
    _hash: createHash(source),
    _createdAt: Math.floor(stats.birthtimeMs),
    _updatedAt: Math.floor(stats.mtimeMs),
    title: frontmatter.title || file,
    ...frontmatter,
  }
}

/**
 * Generate Vue SFC from HTML
 */
function generateVueSFC(html: string, meta: ContentEntry): string {
  // Escape backticks in HTML
  const escapedHtml = html.replace(/`/g, '\\`')

  return `<script setup lang="ts">
defineProps<{ class?: string }>()
</script>

<template>
  <div :class="$props.class || 'prose'">
    ${html}
  </div>
</template>

<script lang="ts">
export const meta = ${JSON.stringify(meta, null, 2)}
export const headings = ${JSON.stringify(meta._headings, null, 2)}
</script>
`
}

