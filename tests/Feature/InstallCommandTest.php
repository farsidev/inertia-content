<?php

use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;

beforeEach(function () {
    // Ensure a clean state before each test
    File::delete(base_path('package.json'));
    File::delete(base_path('vite.config.js'));
    File::delete(base_path('vite.config.ts'));
    File::delete(base_path('vite.config.js.bak'));
    File::delete(base_path('vite.config.ts.bak'));
});

afterEach(function () {
    // Clean up created files
    File::delete(base_path('package.json'));
    File::delete(base_path('vite.config.js'));
    File::delete(base_path('vite.config.ts'));
    File::delete(base_path('vite.config.js.bak'));
    File::delete(base_path('vite.config.ts.bak'));
});

it('updates package.json with required dependencies', function () {
    // Arrange: Create a dummy package.json
    File::put(base_path('package.json'), json_encode([
        'private' => true,
        'devDependencies' => [
            'existing-package' => '1.0.0',
        ],
    ], JSON_PRETTY_PRINT));

    // Act: Run the install command
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert: Check that package.json was updated
    $packageJson = json_decode(File::get(base_path('package.json')), true);

    expect($packageJson['devDependencies'])->toHaveKey('chokidar');
    expect($packageJson['devDependencies'])->toHaveKey('glob');
    expect($packageJson['devDependencies'])->toHaveKey('gray-matter');
    expect($packageJson['devDependencies'])->toHaveKey('markdown-it');
    expect($packageJson['devDependencies'])->toHaveKey('existing-package'); // Ensure existing packages are kept
});

it('updates vite.config.js correctly', function () {
    // Arrange: Create a dummy vite.config.js
    File::put(base_path('vite.config.js'), <<<EOT
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

    // Act: Run the install command
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert: Check that vite.config.js was updated
    $viteConfig = File::get(base_path('vite.config.js'));

    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect($viteConfig)->toContain('inertiaContent(),');
    expect($viteConfig)->toContain('@inertia-content');
    expect(File::exists(base_path('vite.config.js.bak')))->toBeTrue();
});

it('updates vite.config.ts correctly', function () {
    // Arrange: Create a dummy vite.config.ts
    File::put(base_path('vite.config.ts'), <<<EOT
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

    // Act: Run the install command
    artisan('inertia-content:install')->assertExitCode(0);

    // Assert: Check that vite.config.ts was updated
    $viteConfig = File::get(base_path('vite.config.ts'));

    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect($viteConfig)->toContain('inertiaContent(),');
    expect($viteConfig)->toContain('@inertia-content');
    expect(File::exists(base_path('vite.config.ts.bak')))->toBeTrue();
});

it('does not modify package.json if dependencies are already present', function () {
    // Arrange: Create a dummy package.json with one of the deps
    File::put(base_path('package.json'), json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3',
            'glob' => '^10.3.10',
            'gray-matter' => '^4.0.3',
            'markdown-it' => '^14.0.0',
        ],
    ], JSON_PRETTY_PRINT));

    $originalContent = File::get(base_path('package.json'));

    // Act
    $command = artisan('inertia-content:install');

    // Assert
    $command->expectsOutput('✓ NPM dependencies are already up to date.');
    expect(File::get(base_path('package.json')))->toBe($originalContent);
});

it('handles missing package.json gracefully', function () {
    artisan('inertia-content:install')
        ->expectsOutput('! <fg=yellow>package.json not found.</> Skipping dependency updates.')
        ->assertExitCode(0);
});

it('handles missing vite.config gracefully', function () {
    // Arrange: Only create package.json
    File::put(base_path('package.json'), '{}');

    artisan('inertia-content:install')
        ->expectsOutput('! <fg=yellow>vite.config.js</> or <fg=yellow>vite.config.ts</> not found.')
        ->assertExitCode(0);
});
