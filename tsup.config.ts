import { defineConfig } from 'tsup'

export default defineConfig([
  // Main runtime exports
  {
    entry: ['resources/js/index.ts'],
    format: ['esm', 'cjs'],
    dts: true,
    sourcemap: true,
    clean: true,
    external: ['vue', 'vite'],
    treeshake: true,
  },
  // Vite plugin exports
  {
    entry: ['resources/js/vite/index.ts'],
    outDir: 'dist/vite',
    format: ['esm', 'cjs'],
    dts: true,
    sourcemap: true,
    external: ['vite', 'vue'],
    treeshake: true,
  },
])
