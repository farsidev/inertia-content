// eslint.config.js
import tseslint from 'typescript-eslint';
import vuePlugin from 'eslint-plugin-vue';
import vueParser from 'vue-eslint-parser';

export default tseslint.config(
  {
    ignores: ["**/dist/", "**/node_modules/", "stubs/"],
  },
  // TypeScript files
  {
    files: ["resources/js/**/*.ts"],
    extends: [
      ...tseslint.configs.recommended,
    ],
    rules: {
      // Add custom TS rules here if needed
    },
  },
  // Vue files
  ...vuePlugin.configs['flat/essential'].map(config => ({
    ...config,
    files: ["resources/js/**/*.vue"],
    processor: vuePlugin.processors['.vue'],
    languageOptions: {
      ...config.languageOptions,
      parser: vueParser,
      parserOptions: {
        parser: tseslint.parser,
        sourceType: 'module',
      },
    },
    rules: {
        ...config.rules,
        // Add custom Vue rules here if needed
    }
  }))
);
