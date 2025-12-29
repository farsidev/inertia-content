import type { Plugin, ViteDevServer, ResolvedConfig } from 'vite'
import { globSync } from 'glob'
import { compileMarkdown } from './compiler'
import { generateManifest, writeManifest } from './manifest'
import { setupHMR } from './hmr'
import type { InertiaContentOptions, ContentManifest, ContentEntry } from '../runtime/types'
import fs from 'node:fs'
import path from 'node:path'

const VIRTUAL_MANIFEST = 'virtual:inertia-content/manifest'
const VIRTUAL_ENTRY_PREFIX = 'virtual:inertia-content/entry/'

interface CompiledEntry {
  meta: ContentEntry
  headings: any[]
  vueComponent: string
}

/**
 * Vite plugin for Inertia Content
 */
export default function inertiaContent(options: InertiaContentOptions = {}): Plugin {
  const contentDir = options.contentDir || 'resources/content'
  const manifestPath = options.manifestPath || 'public/build/inertia-content-manifest.json'
  const ignore = options.ignore || ['**/.*', '**/_*', '**/node_modules/**']

  let config: ResolvedConfig
  let manifest: ContentManifest
  let compiledEntries: Map<string, CompiledEntry>
  let server: ViteDevServer | undefined
  let virtualModulesDir: string

  return {
    name: 'vite-plugin-inertia-content',

    configResolved(resolvedConfig) {
      config = resolvedConfig
      virtualModulesDir = path.join(config.root, 'node_modules/.vite-inertia-content')
    },

    buildStart() {
      // Scan and compile all .md files
      compiledEntries = scanAndCompile(contentDir, options, ignore)
      manifest = generateManifest(compiledEntries)

      if (config.command === 'build') {
        console.log(`[inertia-content] Compiled ${compiledEntries.size} content entries`)
      }
    },

    resolveId(id) {
      if (id === VIRTUAL_MANIFEST) {
        return '\0' + VIRTUAL_MANIFEST
      }
      if (id.startsWith(VIRTUAL_ENTRY_PREFIX)) {
        return '\0' + id
      }
    },

    load(id) {
      if (id === '\0' + VIRTUAL_MANIFEST) {
        return generateManifestModule(manifest)
      }
      if (id.startsWith('\0' + VIRTUAL_ENTRY_PREFIX)) {
        const contentPath = id.slice(('\0' + VIRTUAL_ENTRY_PREFIX).length)
        const entry = compiledEntries.get(contentPath)
        if (entry) {
          return entry.vueComponent
        }
      }
    },

    configureServer(devServer) {
      server = devServer
      setupHMR(devServer, contentDir, options, compiledEntries, manifest, ignore)
    },

    generateBundle(options, bundle) {
      // Emit content chunks as physical files
      if (config.command === 'build') {
        for (const [contentPath, entry] of compiledEntries) {
          const chunkId = `content-${entry.meta._hash}`
          
          // Emit each content entry as a separate chunk
          this.emitFile({
            type: 'chunk',
            id: `\0${VIRTUAL_ENTRY_PREFIX}${contentPath}`,
            fileName: `assets/${chunkId}.js`,
            name: chunkId
          })
        }

        // Write manifest to file
        const outputPath = path.resolve(config.root, manifestPath)
        writeManifest(manifest, outputPath)
        console.log(`[inertia-content] Emitted ${compiledEntries.size} content chunks`)
        console.log(`[inertia-content] Manifest written to ${outputPath}`)
      }
    },

  }
}

/**
 * Scan directory and compile all markdown files
 */
function scanAndCompile(
  contentDir: string,
  options: InertiaContentOptions,
  ignore: string[]
): Map<string, CompiledEntry> {
  const entries = new Map<string, CompiledEntry>()

  // Find all .md files
  const files = globSync('**/*.md', {
    cwd: contentDir,
    absolute: true,
    ignore,
  })

  for (const file of files) {
    const source = fs.readFileSync(file, 'utf-8')
    const compiled = compileMarkdown(source, file, contentDir, options)

    // Use normalized path as key
    const contentPath = compiled.meta._path
    entries.set(contentPath, compiled)
  }

  return entries
}

/**
 * Generate the virtual manifest module code
 */
function generateManifestModule(manifest: ContentManifest): string {
  return `
export const manifest = ${JSON.stringify(manifest, null, 2)}
export const version = "${manifest.version}"

export function getEntry(path) {
  return manifest.entries[path]
}

export function getAllPaths() {
  return Object.keys(manifest.entries)
}
`
}
