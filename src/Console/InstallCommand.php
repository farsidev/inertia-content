<?php

namespace Farsi\InertiaContent\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Farsi\InertiaContent\Support\PackageJson;

class InstallCommand extends Command
{
    protected $signature = 'inertia-content:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install Inertia Content package';

    public function handle(): int
    {
        $this->info('Installing Inertia Content...');
        $this->newLine();

        $this->publishConfig();
        $this->createContentDirectory();
        $this->publishStubs();
        $this->updatePackageJson();
        $this->publishViteConfig();

        $this->newLine();
        $this->info('Inertia Content installed successfully! 🎉');
        $this->newLine();

        $this->line('Next steps:');
        $this->line('1. Run <fg=yellow>npm install</> to install the new JavaScript dependencies.');
        $this->line('2. Import and merge <fg=yellow>vite.inertia-content.js</> into your main <fg=yellow>vite.config.js</>.');
        $this->line('   <fg=gray>See https://vitejs.dev/config/#mergeconfig for details.</>');
        $this->line('3. Add a content route to your <fg=yellow>routes/web.php</> file.');
        $this->line('4. Run <fg=yellow>npm run dev</> to start the development server.');

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-config',
            '--force' => $this->option('force'),
        ]);
        $this->line('✓ Config published to config/inertia-content.php');
    }

    private function createContentDirectory(): void
    {
        $contentDir = resource_path('content');
        if (! File::isDirectory($contentDir)) {
            File::makeDirectory($contentDir, 0755, true);
            $this->line('✓ Content directory created at resources/content');
        } else {
            $this->line('✓ Content directory already exists');
        }
    }

    private function publishStubs(): void
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-stubs',
            '--force' => $this->option('force'),
        ]);
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-pages',
            '--force' => $this->option('force'),
        ]);
        $this->line('✓ Sample content and pages published.');
    }

    private function updatePackageJson(): void
    {
        $packageJsonPath = base_path('package.json');

        if (! File::exists($packageJsonPath)) {
            $this->warn('Could not find package.json. Please install the JS dependency manually.');
            return;
        }

        $packages = PackageJson::read($packageJsonPath);

        PackageJson::addDevDependency($packages, 'inertia-content', 'file:vendor/farsi/inertia-content');

        PackageJson::write($packageJsonPath, $packages);

        $this->line('✓ <fg=yellow>inertia-content</> dependency added to package.json');
    }

    private function publishViteConfig(): void
    {
        $stubPath = __DIR__.'/../../stubs/vite.inertia-content.js.stub';
        $destinationPath = base_path('vite.inertia-content.js');

        if (File::exists($destinationPath) && ! $this->option('force')) {
            $this->line('✓ Vite config stub already exists.');
            return;
        }

        File::copy($stubPath, $destinationPath);

        $this->line('✓ Vite config stub published to <fg=yellow>vite.inertia-content.js</>');
    }
}
