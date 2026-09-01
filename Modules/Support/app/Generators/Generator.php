<?php

namespace Modules\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Nwidart\Modules\Facades\Module;

class Generator
{
    private Filesystem $filesystem;

    public function getNamespace(string $module, string $name, string $directory): string
    {
        return ($module !== 'None' ? "Modules\\$module\\$directory" : "App\\$directory") . "\\$name";
    }

    /**
     * Generate from stub
     *
     * @param string $from From Stub file
     * @param string $to To directory
     * @param string $as AS file name
     * @param array $with With variables to replace
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function generate(string $from, string $to, string $as, array $with = []): void
    {
        $this->fs()->ensureDirectoryExists($to);

        $stub = $this->getStub($from);
        $content = str($stub)->replace(array_keys($with), array_values($with));

        $this->fs()->put($to . '/' . $as, $content);
    }

    /**
     * Get the file system
     *
     * @return Filesystem
     */
    protected function fs(): Filesystem
    {
        if (empty($this->filesystem))
            $this->filesystem = new Filesystem();

        return $this->filesystem;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getStub($type): bool|string
    {
        $stubsBasePath = Module::find('Support')->getPath() . '/stubs/';
        return $this->fs()->get($stubsBasePath . "$type.stub");
    }

    public function getPath(string $module, string $name, string $directory): string
    {
        return ($module !== 'None' ? module_path($module, "app/$directory") : app_path("$directory")) . "/$name";
    }
}
