<?php

use Illuminate\Support\Facades\File;
use Illuminate\Testing\PendingCommand;
use function Pest\Laravel\artisan;

// Define a constant for the temporary test directory's relative path.
const TEST_DIR_NAME = 'app/temp-test-dir';

/**
 * Helper function to run the artisan install command. This is now stateless,
 * accepting the temporary path as an argument.
 *
 * @param string $tempDir The absolute path to the temporary test directory.
 * @return \Illuminate\Testing\PendingCommand
 */
function runInstallCommand(string $tempDir): PendingCommand
{
    return artisan('inertia-content:install', [
        '--source-package-path' => $tempDir.'/source-package.json',
    ]);
}

/**
 * Sets up a clean, temporary directory for each test case.
 */
beforeEach(function () {
    // 1. Calculate the absolute path to the temporary directory ONCE, before any
    // application state is changed. This is the key to the fix.
    $this->tempDir = storage_path(TEST_DIR_NAME);

    // 2. Create a clean directory at this stable, absolute path.
    File::deleteDirectory($this->tempDir);
    File::makeDirectory($this->tempDir, 0755, true, true);

    // 3. Set the application's base path to our temporary directory.
    $this->app->setBasePath($this->tempDir);

    // 4. Create the dummy source package.json inside the temp directory.
    File::put($this->tempDir.'/source-package.json', json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3', 'glob' => '^10.3.10', 'gray-matter' => '^4.0.3', 'markdown-it' => '^14.0.0',
        ],
    ], JSON_PRETTY_PRINT));
});

/**
 * Cleans up the temporary directory after each test case.
 */
afterEach(function () {
    File::deleteDirectory($this->tempDir);
});

it('updates package.json with required dependencies', function () {
    // Arrange
    File::put($this->tempDir.'/package.json', json_encode([
        'private' => true,
        'devDependencies' => ['existing-package' => '1.0.0'],
    ], JSON_PRETTY_PRINT));

    // Act
    runInstallCommand($this->tempDir)->assertExitCode(0);

    // Assert
    $packageJson = json_decode(File::get($this->tempDir.'/package.json'), true);
    expect($packageJson['devDependencies'])->toHaveKey('chokidar');
    expect($packageJson['devDependencies'])->toHaveKey('existing-package');
});

it('updates vite.config.js correctly', function () {
    // Arrange
    File::put($this->tempDir.'/vite.config.js', "export default { plugins: [] };");
    File::put($this->tempDir.'/package.json', "{}");

    // Act
    runInstallCommand($this->tempDir)->assertExitCode(0);

    // Assert
    $viteConfig = File::get($this->tempDir.'/vite.config.js');
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect(File::exists($this->tempDir.'/vite.config.js.bak'))->toBeTrue();
});

it('updates vite.config.ts correctly', function () {
    // Arrange
    File::put($this->tempDir.'/vite.config.ts', "export default { plugins: [] };");
    File::put($this->tempDir.'/package.json', "{}");

    // Act
    runInstallCommand($this->tempDir)->assertExitCode(0);

    // Assert
    $viteConfig = File::get($this->tempDir.'/vite.config.ts');
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect(File::exists($this->tempDir.'/vite.config.ts.bak'))->toBeTrue();
});

it('does not modify package.json if dependencies are already present', function () {
    // Arrange
    File::put($this->tempDir.'/package.json', json_encode([
        'devDependencies' => ['chokidar' => '^3.5.3', 'glob' => '^10.3.10'],
    ], JSON_PRETTY_PRINT));
    $originalContent = File::get($this->tempDir.'/package.json');

    // Act & Assert
    runInstallCommand($this->tempDir)
        ->expectsOutput('✓ NPM dependencies are already up to date.')
        ->assertExitCode(0);
    expect(File::get($this->tempDir.'/package.json'))->toBe($originalContent);
});

it('handles missing package.json gracefully', function () {
    // Act & Assert
    runInstallCommand($this->tempDir)
        ->expectsOutput('! package.json not found. Skipping dependency updates.')
        ->assertExitCode(0);
});

it('handles missing vite.config gracefully', function () {
    // Arrange
    File::put($this->tempDir.'/package.json', '{}');

    // Act & Assert
    runInstallCommand($this->tempDir)
        ->expectsOutput('! vite.config.js or vite.config.ts not found.')
        ->assertExitCode(0);
});
