<?php

namespace Modules\Support\Console;

use Illuminate\Console\Command;
use Modules\Support\Generators\ServiceGenerator;
use Nwidart\Modules\Facades\Module;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class Service extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name?} {module?} {--r|repository}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new service';

    public function __construct(
        private readonly ServiceGenerator $serviceGenerator,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name') ?? text(label: 'What is the name of the Service?', required: true);
        $module = $this->argument('module') ?? select(
            label: 'Select the module',
            options: array_merge($this->getModules(), ['None']),
            hint: 'Press Enter to select'
        );
        $createRepository = $this->option('repository');

        $this->serviceGenerator->createService($name, $module, $createRepository);

        if ($createRepository) {
            $this->serviceGenerator->createRepository($name, $module);
        }

        $this->info('Created successfully.');
        return 0;
    }

    protected function getModules(): array
    {
        return Module::allEnabled();
    }
}
