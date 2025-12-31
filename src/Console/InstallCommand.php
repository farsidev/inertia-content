<?php

namespace Farsi\InertiaContent\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'inertia-content:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install Inertia Content package';

    public function handle(): int
    {
        $this->info('Installing Inertia Content...');
        $this->newLine();

        // 1. Publish config
        $this->publishConfig();

        // 2. Create content directory
        $this->createContentDirectory();

        // 3. Publish stubs
        $this->publishStubs();

        // 4. Display next steps
        $this->displayNextSteps();

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
        // Publish sample content
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-stubs',
            '--force' => $this->option('force'),
        ]);

        // Publish sample pages
        $this->callSilent('vendor:publish', [
            '--tag' => 'inertia-content-pages',
            '--force' => $this->option('force'),
        ]);

        $this->line('✓ Sample content created at resources/content/index.md');
        $this->line('✓ Sample pages created at resources/js/Pages/Content');
    }

    private function displayNextSteps(): void
    {
        $this->newLine();
        $this->info('Next steps:');
        $this->newLine();

        $this->line('1. Install the NPM package:');
        $this->line('   <fg=gray>npm install farsi-inertia-content</>');
        $this->newLine();

        $this->line('2. Add the Vite plugin to your vite.config.ts:');
        $this->newLine();
        $this->line('   <fg=gray>import inertiaContent from \'farsi-inertia-content/vite\'</>');
        $this->newLine();
        $this->line('   <fg=gray>export default defineConfig({</>');
        $this->line('   <fg=gray>  plugins: [</>');
        $this->line('   <fg=gray>    laravel({ ... }),</>');
        $this->line('   <fg=gray>    vue(),</>');
        $this->line('   <fg=gray>    inertiaContent(),  // Add this</>');
        $this->line('   <fg=gray>  ],</>');
        $this->line('   <fg=gray>})</>');
        $this->newLine();

        $this->line('3. Add a content route to routes/web.php:');
        $this->newLine();
        $this->line('   <fg=gray>use Farsi\InertiaContent\Facades\Content;</>');
        $this->newLine();
        $this->line('   <fg=gray>Route::get(\'/docs/{path?}\', function ($path = \'index\') {</>');
        $this->line('   <fg=gray>    return Content::pageOrFail("docs/$path");</>');
        $this->line('   <fg=gray>})->where(\'path\', \'.*\');</>');
        $this->newLine();

        $this->line('4. Run npm install && npm run build');
        $this->newLine();

        $this->info('Installation complete! 🎉');
    }
}

