<?php

namespace Farsi\InertiaContent\Support;

use Illuminate\Support\Facades\File;

class PackageJson
{
    public static function read(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function write(string $path, array $data): void
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        File::put($path, $content.PHP_EOL);
    }

    public static function addDevDependency(array &$packages, string $name, string $version): void
    {
        if (! isset($packages['devDependencies'])) {
            $packages['devDependencies'] = [];
        }

        $packages['devDependencies'][$name] = $version;

        ksort($packages['devDependencies']);
    }
}
