import type { ContentManifest, ManifestEntry, ContentEntry } from '../runtime/types'
import { createHash } from '../utils/hash'
import fs from 'node:fs'
import path from 'node:path'

interface CompiledEntry {
  meta: ContentEntry
  headings: any[]
  vueComponent: string
}

/**
 * Generate manifest from compiled entries
 */
export function generateManifest(
  entries: Map<string, CompiledEntry>
): ContentManifest {
  const manifestEntries: Record<string, ManifestEntry> = {}

  // Build entries
  for (const [contentPath, compiled] of entries) {
    manifestEntries[contentPath] = {
      path: contentPath,
      meta: compiled.meta,
      chunk: `content-${compiled.meta._hash}.js`,
    }
  }

  // Generate version hash from all entry hashes
  const allHashes = Array.from(entries.values())
    .map((e) => e.meta._hash)
    .sort()
    .join('')

  const version = createHash(allHashes)

  return {
    version,
    generatedAt: Date.now(),
    entries: manifestEntries,
  }
}

/**
 * Write manifest to file
 */
export function writeManifest(manifest: ContentManifest, outputPath: string): void {
  const dir = path.dirname(outputPath)

  // Ensure directory exists
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true })
  }

  // Write formatted JSON
  fs.writeFileSync(outputPath, JSON.stringify(manifest, null, 2))
}
