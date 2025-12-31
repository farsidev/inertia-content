import crypto from 'node:crypto'

/**
 * Create SHA256 hash of content
 */
export function createHash(content: string): string {
  return crypto
    .createHash('sha256')
    .update(content)
    .digest('hex')
}

