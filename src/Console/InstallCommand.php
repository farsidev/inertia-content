<?php

namespace Farsi\InertiaContent\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'inertia-content:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install Inertia Content package and scaffold its dependencies';

    public function handle(): int
    {
        $this->info('Installing Inertia Content...');
        $this->newLine();

        // 1. Publish config & stubs
        $this->publishAssets();

        // 2. Update package.json
        $this->updateNodeDependencies();

        // 3. Update Vite configuration
        $this->updateViteConfiguration();

        // 4. Display final summary
        $this->displayFinalSummary();

        return self::SUCCESS;
    }

    private function publishAssets(): void
    {
        // Publish config
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-config',
            '--force' => $this->option('force'),
        ]);
        $this->line('✓ Config published to <fg=gray>config/inertia-content.php</>');

        // Publish sample content & pages
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-stubs',
            '--force' => $this->option('force'),
        ]);
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-pages',
            '--force' => $this->option('force'),
        ]);
        $this->line('✓ Sample content and pages created in <fg=gray>resources/</>');
    }

    private function updateNodeDependencies(): void
    {
        $this->info('Updating NPM dependencies...');

        if (! file_exists(base_path('package.json'))) {
            $this->warn('! <fg=yellow>package.json not found.</> Skipping dependency updates.');

            return;
        }

        $hostPackageJson = json_decode(file_get_contents(base_path('package.json')), true);
        $packageDependencies = $this->getPackageDependencies();

        $hostDevDependencies = $hostPackageJson['devDependencies'] ?? [];

        $newDependencies = array_diff_key($packageDependencies, $hostDevDependencies);

        if (empty($newDependencies)) {
            $this->line('✓ NPM dependencies are already up to date.');

            return;
        }

        $hostPackageJson['devDependencies'] = array_merge($hostDevDependencies, $newDependencies);

        // Sort dependencies alphabetically
        ksort($hostPackageJson['devDependencies']);

        // Write back to package.json
        file_put_contents(
            base_path('package.json'),
            json_encode($hostPackageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->line('✓ <fg=yellow>package.json</> updated with the following dependencies:');
        foreach (array_keys($newDependencies) as $package) {
            $this->line("  - <fg=gray>$package</>");
        }
    }

    private function getPackageDependencies(): array
    {
        $packageJsonPath = __DIR__.'/../../package.json';

        if (! file_exists($packageJsonPath)) {
            return [];
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        return array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? []
        );
    }

    private function updateViteConfiguration(): void
    {
        $this->info('Updating Vite configuration...');

        $viteConfigPath = $this->findViteConfigPath();

        if ($viteConfigPath === null) {
            $this->warn('! <fg=yellow>vite.config.js</> or <fg=yellow>vite.config.ts</> not found.');
            $this->line('Please add the Inertia Content plugin to your Vite configuration manually.');

            return;
        }

        $viteConfigContent = file_get_contents($viteConfigPath);

        // Backup the original file
        copy($viteConfigPath, $viteConfigPath.'.bak');

        // Add import statement if it doesn't exist
        if (! Str::contains($viteConfigContent, 'inertiaContent')) {
            $viteConfigContent = $this->addViteImport($viteConfigContent);
        }

        // Add the plugin to the plugins array
        if (! Str::contains($viteConfigContent, 'inertiaContent()')) {
            $viteConfigContent = $this->addVitePlugin($viteConfigContent);
        }

        // Add the alias if it doesn't exist
        if (! Str::contains($viteConfigContent, '@inertia-content')) {
            $viteConfigContent = $this->addViteAlias($viteConfigContent);
        }

        // Write the updated content back to the file
        file_put_contents($viteConfigPath, $viteConfigContent);

        $this->line("✓ Vite configuration updated at <fg=yellow>".basename($viteConfigPath)."</>");
        $this->line("  - A backup has been created at <fg=gray>".basename($viteConfigPath).".bak</>");
    }

    private function findViteConfigPath(): ?string
    {
        $paths = [
            base_path('vite.config.js'),
            base_path('vite.config.ts'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function addViteImport(string $content): string
    {
        $import = "import inertiaContent from './vendor/farsi/inertia-content/resources/js/vite';";

        // Find the last import statement to add our import after it
        $lines = explode("\n", $content);
        $lastImportLine = -1;
        foreach ($lines as $i => $line) {
            if (Str::startsWith(trim($line), 'import')) {
                $lastImportLine = $i;
            }
        }

        if ($lastImportLine !== -1) {
            array_splice($lines, $lastImportLine + 1, 0, $import);
            return implode("\n", $lines);
        }

        // Fallback: add to the top
        return $import."\n".$content;
    }

    private function addVitePlugin(string $content): string
    {
        if (Str::contains($content, 'plugins: [')) {
            return Str::replaceFirst(
                'plugins: [',
                "plugins: [\n        inertiaContent(),",
                $content
            );
        }

        return $content;
    }

    private function addViteAlias(string $content): string
    {
        $alias = "'@inertia-content': '/vendor/farsi/inertia-content/resources/js',";

        if (Str::contains($content, 'resolve: {')) {
             if (Str::contains($content, 'alias: {')) {
                 return Str::replaceFirst(
                     'alias: {',
                     "alias: {\n            $alias",
                     $content
                 );
             }
             return Str::replaceFirst(
                 'resolve: {',
                 "resolve: {\n        alias: {\n            $alias\n        },",
                 $content
             );
        }

        return Str::replaceLast(
            '}),',
            "}),\n    resolve: {\n        alias: {\n            $alias\n        }\n    },",
            $content
        );
    }

    private function displayFinalSummary(): void
    {
        $this->newLine();
        $this->info('🎉 Inertia Content installed successfully!');
        $this->newLine();

        $this->line('Next step:');
        $this->line('Please run the following command to install the new NPM dependencies:');
        $this->newLine();
        $this->line('   <fg=cyan>npm install</>');
        $this->newLine();
        $this->line('Then, run your dev server or build for production:');
        $this->line('   <fg=cyan>npm run dev</>');
    }
}
