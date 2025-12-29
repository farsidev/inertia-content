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
  const compiledDir = 'resources/js/.content-compiled'

  let config: ResolvedConfig
  let manifest: ContentManifest
  let compiledEntries: Map<string, CompiledEntry>
  let server: ViteDevServer | undefined
  let compiledDirPath: string

  return {
    name: 'vite-plugin-inertia-content',

    configResolved(resolvedConfig) {
      config = resolvedConfig
      compiledDirPath = path.join(config.root, compiledDir)
    },

    buildStart() {
      // Scan and compile all .md files
      compiledEntries = scanAndCompile(contentDir, options, ignore)
      manifest = generateManifest(compiledEntries)

      // Write compiled Vue files to disk
      writeCompiledFilesToDisk(compiledEntries, compiledDirPath, manifest)

      console.log(`[inertia-content] Compiled ${compiledEntries.size} content entries`)
    },

    resolveId(id) {
      if (id === VIRTUAL_MANIFEST) {
        return '\0' + VIRTUAL_MANIFEST
      }
    },

    load(id) {
      if (id === '\0' + VIRTUAL_MANIFEST) {
        return generateManifestModule(manifest, compiledDir)
      }
    },

    configureServer(devServer) {
      server = devServer
      setupHMR(devServer, contentDir, compiledDir, options, compiledEntries, manifest, ignore)
    },

    generateBundle() {
      // Write manifest to file
      if (config.command === 'build') {
        const outputPath = path.resolve(config.root, manifestPath)
        writeManifest(manifest, outputPath)
        console.log(`[inertia-content] Manifest written to ${outputPath}`)
      }
    },

    closeBundle() {
      // Clean up compiled Vue files after build
      if (config.command === 'build' && fs.existsSync(compiledDirPath)) {
        fs.rmSync(compiledDirPath, { recursive: true, force: true })
        console.log(`[inertia-content] Cleaned up compiled files`)
      }
    },
  }
}

/**
 * Write compiled markdown to actual Vue files on disk
 */
function writeCompiledFilesToDisk(
  entries: Map<string, CompiledEntry>,
  outputDir: string,
  manifest: ContentManifest
): void {
  // Clean existing directory
  if (fs.existsSync(outputDir)) {
    fs.rmSync(outputDir, { recursive: true })
  }

  fs.mkdirSync(outputDir, { recursive: true })

  // Write each entry as a .vue file
  for (const [contentPath, entry] of entries) {
    const vueFilePath = path.join(outputDir, `${contentPath}.vue`)
    const dirPath = path.dirname(vueFilePath)

    if (!fs.existsSync(dirPath)) {
      fs.mkdirSync(dirPath, { recursive: true })
    }

    fs.writeFileSync(vueFilePath, entry.vueComponent)
  }

  console.log(`[inertia-content] Written ${entries.size} Vue files to ${outputDir}`)
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
function generateManifestModule(manifest: ContentManifest, compiledDir: string): string {
  return `
export const manifest = ${JSON.stringify(manifest, null, 2)}
export const version = "${manifest.version}"
export const compiledDir = "${compiledDir}"

export function getEntry(path) {
  return manifest.entries[path]
}

export function getAllPaths() {
  return Object.keys(manifest.entries)
}
`
}
