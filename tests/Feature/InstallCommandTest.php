<?php

use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;

// Define a constant for the temporary test directory. Using storage_path() ensures
// that we have a reliable, writable location in any environment.
const TEST_DIR = 'app/temp-test-dir';

/**
 * Helper function to get the full, absolute path to the temporary test directory.
 */
function getTestPath(string $path = ''): string
{
    return storage_path(TEST_DIR.($path ? DIRECTORY_SEPARATOR.$path : ''));
}

/**
 * Helper function to run the artisan install command with the necessary test-specific options.
 * This keeps our tests clean and avoids repetition.
 */
function runInstallCommand(): \Illuminate\Foundation\Testing\PendingCommand
{
    return artisan('inertia-content:install', [
        '--source-package-path' => getTestPath('source-package.json'),
    ]);
}

/**
 * Sets up a clean, temporary directory for each test case.
 * This runs before each `it()` test.
 */
beforeEach(function () {
    // 1. Create a clean, temporary directory for the test to run in.
    File::deleteDirectory(getTestPath());
    File::makeDirectory(getTestPath(), 0755, true, true);

    // 2. Override the `base_path` binding in Laravel's service container.
    // This is the key to ensuring the `InstallCommand` writes to our temp directory.
    $this->app->instance('path.base', getTestPath());

    // --- DIAGNOSTICS ---
    echo "\n--- beforeEach ---\n";
    echo "getTestPath(): " . getTestPath() . "\n";
    echo "app->basePath(): " . $this->app->basePath() . "\n";
    // --- END DIAGNOSTICS ---

    // 3. The InstallCommand needs to read a source package.json to know which dependencies to add.
    // We create a dummy version of it here, inside our temp directory.
    File::put(getTestPath('source-package.json'), json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3',
            'glob' => '^10.3.10',
            'gray-matter' => '^4.0.3',
            'markdown-it' => '^14.0.0',
        ],
    ], JSON_PRETTY_PRINT));

    // --- DIAGNOSTICS ---
    echo "Files in test directory:\n";
    print_r(scandir(getTestPath()));
    echo "------------------\n";
    // --- END DIAGNOSTICS ---
});

/**
 * Cleans up the temporary directory after each test case.
 */
afterEach(function () {
    File::deleteDirectory(getTestPath());
});

it('updates package.json with required dependencies', function () {
    // Arrange: Create a dummy package.json in the temp directory for the command to modify.
    File::put(getTestPath('package.json'), json_encode([
        'private' => true,
        'devDependencies' => [
            'existing-package' => '1.0.0',
        ],
    ], JSON_PRETTY_PRINT));

    // --- DIAGNOSTICS ---
    echo "\n--- it('updates package.json...') ---\n";
    echo "Files in test directory before run:\n";
    print_r(scandir(getTestPath()));
    // --- END DIAGNOSTICS ---

    // Act: Run the install command.
    runInstallCommand()->assertExitCode(0);

    // --- DIAGNOSTICS ---
    echo "Command has been run. Checking final package.json contents:\n";
    $finalContent = File::exists(getTestPath('package.json')) ? File::get(getTestPath('package.json')) : '!!! package.json NOT FOUND !!!';
    echo $finalContent . "\n";
    echo "-------------------------------------\n";
    // --- END DIAGNOSTICS ---

    // Assert: Check that the dummy package.json was updated correctly.
    $packageJson = json_decode(File::get(getTestPath('package.json')), true);
    expect($packageJson['devDependencies'])->toHaveKey('chokidar');
    expect($packageJson['devDependencies'])->toHaveKey('glob');
    expect($packageJson['devDependencies'])->toHaveKey('gray-matter');
    expect($packageJson['devDependencies'])->toHaveKey('markdown-it');
    expect($packageJson['devDependencies'])->toHaveKey('existing-package');
});

it('updates vite.config.js correctly', function () {
    // Arrange: Create a dummy vite.config.js in the temp directory.
    File::put(getTestPath('vite.config.js'), "export default { plugins: [] };");
    File::put(getTestPath('package.json'), "{}"); // Command needs this to exist to proceed

    // Act
    runInstallCommand()->assertExitCode(0);

    // Assert
    $viteConfig = File::get(getTestPath('vite.config.js'));
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect(File::exists(getTestPath('vite.config.js.bak')))->toBeTrue();
});

it('updates vite.config.ts correctly', function () {
    // Arrange
    File::put(getTestPath('vite.config.ts'), "export default { plugins: [] };");
    File::put(getTestPath('package.json'), "{}");

    // Act
    runInstallCommand()->assertExitCode(0);

    // Assert
    $viteConfig = File::get(getTestPath('vite.config.ts'));
    expect($viteConfig)->toContain("import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';");
    expect(File::exists(getTestPath('vite.config.ts.bak')))->toBeTrue();
});

it('does not modify package.json if dependencies are already present', function () {
    // Arrange: Create a package.json that already has the dependencies.
    File::put(getTestPath('package.json'), json_encode([
        'devDependencies' => [
            'chokidar' => '^3.5.3',
            'glob' => '^10.3.10',
        ],
    ], JSON_PRETTY_PRINT));
    $originalContent = File::get(getTestPath('package.json'));

    // Act & Assert: Check for the "already up to date" message.
    runInstallCommand()
        ->expectsOutput('✓ NPM dependencies are already up to date.')
        ->assertExitCode(0);
    expect(File::get(getTestPath('package.json')))->toBe($originalContent);
});

it('handles missing package.json gracefully', function () {
    // No arrangement needed; the file is missing by default.
    runInstallCommand()
        ->expectsOutput('! package.json not found. Skipping dependency updates.')
        ->assertExitCode(0);
});

it('handles missing vite.config gracefully', function () {
    // Arrange: A package.json must exist for the command to proceed to the Vite step.
    File::put(getTestPath('package.json'), '{}');

    // Act & Assert
    runInstallCommand()
        ->expectsOutput('! vite.config.js or vite.config.ts not found.')
        ->assertExitCode(0);
});
