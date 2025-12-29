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
        
        // Write virtual modules to physical files for production build
        writeVirtualModulesToDisk(compiledEntries, virtualModulesDir)
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

    generateBundle() {
      // Write manifest to file in build mode
      if (config.command === 'build') {
        const outputPath = path.resolve(config.root, manifestPath)
        writeManifest(manifest, outputPath)
        console.log(`[inertia-content] Manifest written to ${outputPath}`)
      }
    },

    closeBundle() {
      // Clean up virtual modules directory after build
      if (config.command === 'build' && fs.existsSync(virtualModulesDir)) {
        fs.rmSync(virtualModulesDir, { recursive: true, force: true })
        console.log(`[inertia-content] Cleaned up temporary files`)
      }
    },
  }
}

/**
 * Write virtual modules to disk for production build
 */
function writeVirtualModulesToDisk(
  entries: Map<string, CompiledEntry>,
  outputDir: string
): void {
  // Create output directory
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true })
  }

  // Write each compiled entry as a physical .vue file
  for (const [contentPath, entry] of entries) {
    const outputPath = path.join(outputDir, `${contentPath}.vue`)
    const outputDirPath = path.dirname(outputPath)

    if (!fs.existsSync(outputDirPath)) {
      fs.mkdirSync(outputDirPath, { recursive: true })
    }

    fs.writeFileSync(outputPath, entry.vueComponent)
  }

  console.log(`[inertia-content] Written ${entries.size} virtual modules to ${outputDir}`)
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
