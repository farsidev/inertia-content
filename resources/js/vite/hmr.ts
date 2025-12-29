import type { ViteDevServer } from 'vite'
import type { InertiaContentOptions, ContentManifest, ContentEntry } from '../runtime/types'
import chokidar from 'chokidar'
import { compileMarkdown } from './compiler'
import fs from 'node:fs'
import path from 'node:path'

interface CompiledEntry {
  meta: ContentEntry
  headings: any[]
  vueComponent: string
}

/**
 * Setup HMR for content files
 */
export function setupHMR(
  server: ViteDevServer,
  contentDir: string,
  options: InertiaContentOptions,
  compiledEntries: Map<string, CompiledEntry>,
  manifest: ContentManifest,
  ignore: string[]
): void {
  const absoluteContentDir = path.resolve(contentDir)

  // Watch for content file changes
  const watcher = chokidar.watch('**/*.md', {
    cwd: absoluteContentDir,
    ignoreInitial: true,
    ignored: ignore,
  })

  watcher.on('add', async (relativePath) => {
    const filePath = path.join(absoluteContentDir, relativePath)
    await handleFileChange(filePath, 'add', server, options, compiledEntries, manifest, contentDir)
  })

  watcher.on('change', async (relativePath) => {
    const filePath = path.join(absoluteContentDir, relativePath)
    await handleFileChange(filePath, 'change', server, options, compiledEntries, manifest, contentDir)
  })

  watcher.on('unlink', async (relativePath) => {
    const filePath = path.join(absoluteContentDir, relativePath)
    await handleFileRemove(filePath, server, compiledEntries, manifest, contentDir)
  })

  // Clean up on server close
  server.httpServer?.on('close', () => {
    watcher.close()
  })
}

/**
 * Handle file add or change
 */
async function handleFileChange(
  filePath: string,
  type: 'add' | 'change',
  server: ViteDevServer,
  options: InertiaContentOptions,
  compiledEntries: Map<string, CompiledEntry>,
  manifest: ContentManifest,
  contentDir: string
): Promise<void> {
  try {
    // Recompile the file
    const source = fs.readFileSync(filePath, 'utf-8')
    const compiled = compileMarkdown(source, filePath, contentDir, options)
    const contentPath = compiled.meta._path

    // Update compiled entries
    compiledEntries.set(contentPath, compiled)

    // Update manifest
    manifest.entries[contentPath] = {
      path: contentPath,
      meta: compiled.meta,
      chunk: `content-${compiled.meta._hash}.js`,
    }

    // Invalidate virtual modules
    const manifestModuleId = '\0virtual:inertia-content/manifest'
    const entryModuleId = `\0virtual:inertia-content/entry/${contentPath}`

    const manifestModule = server.moduleGraph.getModuleById(manifestModuleId)
    if (manifestModule) {
      server.moduleGraph.invalidateModule(manifestModule)
    }

    const entryModule = server.moduleGraph.getModuleById(entryModuleId)
    if (entryModule) {
      server.moduleGraph.invalidateModule(entryModule)
    }

    // Send HMR update to clients
    server.ws.send({
      type: 'custom',
      event: 'inertia-content:update',
      data: {
        type,
        path: contentPath,
        entry: manifest.entries[contentPath],
        timestamp: Date.now(),
      },
    })

    console.log(`[inertia-content] ${type === 'add' ? 'Added' : 'Updated'}: ${contentPath}`)
  } catch (error) {
    console.error(`[inertia-content] Error processing ${filePath}:`, error)
  }
}

/**
 * Handle file removal
 */
async function handleFileRemove(
  filePath: string,
  server: ViteDevServer,
  compiledEntries: Map<string, CompiledEntry>,
  manifest: ContentManifest,
  contentDir: string
): Promise<void> {
  // Determine the content path from file path
  const relativePath = path.relative(contentDir, filePath)
  const contentPath = relativePath.replace(/\.md$/, '').replace(/\\/g, '/')

  // Remove from compiled entries
  compiledEntries.delete(contentPath)

  // Remove from manifest
  delete manifest.entries[contentPath]

  // Invalidate manifest module
  const manifestModuleId = '\0virtual:inertia-content/manifest'
  const manifestModule = server.moduleGraph.getModuleById(manifestModuleId)
  if (manifestModule) {
    server.moduleGraph.invalidateModule(manifestModule)
  }

  // Send HMR update
  server.ws.send({
    type: 'custom',
    event: 'inertia-content:remove',
    data: {
      path: contentPath,
      timestamp: Date.now(),
    },
  })

  console.log(`[inertia-content] Removed: ${contentPath}`)
}
