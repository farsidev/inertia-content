<?php

use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;

// We will use a temporary directory within the standard Laravel storage path,
// as Pest/Laravel's test environment has better control over this.
const TEST_DIR = 'app/temp-test-dir';

/**
 * Helper to get the full, absolute path to the temporary test directory.
 */
function getTestPath(string $path = ''): string
{
    // storage_path() is a reliable helper that points to the correct storage directory in any environment.
    return storage_path(TEST_DIR.($path ? DIRECTORY_SEPARATOR.$path : ''));
}

/**
 * Sets up a clean, temporary directory for each test.
 * This runs before each `it()` test case in this file.
 */
beforeEach(function () {
    // 1. Ensure the directory is clean before we start.
    File::deleteDirectory(getTestPath());
    File::makeDirectory(getTestPath(), 0755, true, true);

    // 2. This is the crucial part. We override the `base_path` binding in the service container.
    // The `artisan` command will now resolve `base_path()` to our temporary directory.
    $this->app->instance('path.base', getTestPath());

    // 3. The InstallCommand reads its OWN package.json to get dependency lists.
    // We need to create a dummy version of it for the command to read.
    // The command finds it at `__DIR__.'/../../package.json'`.
    $packageRoot = realpath(__DIR__.'/../../');
    File::makeDirectory($packageRoot, 0755, true, true);
    File::put($packageRoot.'/package.json', json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3',
            'glob' => '^10.3.10',
            'gray-matter' => '^4.0.3',
            'markdown-it' => '^14.0.0',
        ]
    ], JSON_PRETTY_PRINT));
});

/**
 * Cleans up the temporary directory after each test.
 */
afterEach(function () {
    File::deleteDirectory(getTestPath());
    $packageRoot = realpath(__DIR__.'/../../');
    File::delete($packageRoot.'/package.json');
});

it('updates package.json with required dependencies', function () {
    // Arrange: Create a dummy package.json in the temp directory
    File::put(getTestPath('package.json'), json_encode([
        'private' => true,
        'devDependencies' => [
            'existing-package' => '1.0.0',
        ],
    ], JSON_PRETTY_PRINT));

    // Act: Run the install command
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert: Check that package.json was updated
    $packageJson = json_decode(File::get(getTestPath('package.json')), true);

    expect($packageJson['devDependencies'])->toHaveKey('chokidar');
    expect($packageJson['devDependencies'])->toHaveKey('glob');
    expect($packageJson['devDependencies'])->toHaveKey('gray-matter');
    expect($packageJson['devDependencies'])->toHaveKey('markdown-it');
    expect($packageJson['devDependencies'])->toHaveKey('existing-package');
});

it('updates vite.config.js correctly', function () {
    // Arrange: Create a dummy vite.config.js
    File::put(getTestPath('vite.config.js'), <<<EOT
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        vue(),
    ],
});
EOT
    );

    // Act
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert
    $viteConfig = File::get(getTestPath('vite.config.js'));
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect($viteConfig)->toContain('inertiaContent(),');
    expect($viteConfig)->toContain('@inertia-content');
    expect(File::exists(getTestPath('vite.config.js.bak')))->toBeTrue();
});

it('updates vite.config.ts correctly', function () {
    // Arrange
    File::put(getTestPath('vite.config.ts'), <<<EOT
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        }
    }
});
EOT
    );

    // Act
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert
    $viteConfig = File::get(getTestPath('vite.config.ts'));
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect($viteConfig)->toContain('inertiaContent(),');
    expect($viteConfig)->toContain('@inertia-content');
    expect(File::exists(getTestPath('vite.config.ts.bak')))->toBeTrue();
});

it('does not modify package.json if dependencies are already present', function () {
    // Arrange
    File::put(getTestPath('package.json'), json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3',
            'glob' => '^10.3.10',
            'gray-matter' => '^4.0.3',
            'markdown-it' => '^14.0.0',
        ],
    ], JSON_PRETTY_PRINT));
    $originalContent = File::get(getTestPath('package.json'));

    // Act & Assert
    artisan('inertia-content:install')
        ->expectsOutput('✓ NPM dependencies are already up to date.')
        ->assertExitCode(0);

    expect(File::get(getTestPath('package.json')))->toBe($originalContent);
});


it('handles missing package.json gracefully', function () {
    // This test needs to run in a context where the file doesn't exist.
    // The beforeEach creates a directory, but not the file. So we just run.
    artisan('inertia-content:install')
        ->expectsOutput('! package.json not found. Skipping dependency updates.')
        ->assertExitCode(0);
});

it('handles missing vite.config gracefully', function () {
    // Arrange: We need a package.json for the command to proceed to the Vite step.
    File::put(getTestPath('package.json'), '{}');

    // Act & Assert
    artisan('inertia-content:install')
        ->expectsOutput('! vite.config.js or vite.config.ts not found.')
        ->assertExitCode(0);
});
