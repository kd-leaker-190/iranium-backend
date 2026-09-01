<?php

namespace Modules\Support\Console;

use Illuminate\Console\Command;
use Modules\Support\Generators\Generator;
use Modules\Support\Generators\ServiceGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\table;

class MyG extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'myg:create {module?} {model?} {--M|model} {--r|request} {--f|factory} {--c|controller} {--m|migration} {--p|policy} {--resource} {--s|service} {--R|repository}';

    /**
     * The console command description.
     */
    protected $description = 'Create resources';

    private array $report = [];

    public function __construct(
        private readonly ServiceGenerator $serviceGenerator,
        private readonly Generator        $generator
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $module = $this->argument('module') ?? $this->ask('What is the name of the Module?');
        $model = $this->argument('model') ?? $this->ask('What is the name of the Model?');

        $options = [
            'model' => $this->option('model'),
            'request' => $this->option('request'),
            'controller' => $this->option('controller'),
            'migration' => $this->option('migration'),
            'policy' => $this->option('policy'),
            'resource' => $this->option('resource'),
            'service' => $this->option('service'),
            'repository' => $this->option('repository'),
            'factory' => $this->option('factory'),
        ];

        $selectedOptions = array_filter($options, fn($value) => $value);

        $selectedOptions = multiselect(
            label: 'Select the resources to create',
            options: array_keys($options),
            default: array_keys($selectedOptions),
            required: true
        );

        $selectedOptions = array_flip($selectedOptions);

        if ($this->option('request') || array_key_exists('request', $selectedOptions)) {
            $selectedOptions['request'] = multiselect(
                label: 'Select the request methods to create',
                options: [
                    $model . 'Request',
                    'Store' . $model . 'Request',
                    'Index' . $model . 'Request',
                    'Update' . $model . 'Request',
                    'Patch' . $model . 'Request'
                ],
                default: [$model . 'Request'],
                required: true

            );
        }

        $this->createResources($model, $module, $selectedOptions);
        $this->outputReport();
    }

    /**
     * Create resources based on user choices.
     */
    protected function createResources(string $model, string $module, array $selectedOptions): void
    {
        $total = count($selectedOptions);
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();
        foreach ($selectedOptions as $option => $methods) {
            $startTime = microtime(true);
            usleep(rand(8, 15) * 10000);
            if ($option === 'request') {
                foreach ($methods as $method) {
                    $this->createResource($option, $method, $module);
                    $endTime = microtime(true);
                    $this->logReport($method, $model, $module, $endTime - $startTime);
                }
            } else {
                $this->createResource($option, $model, $module);
                $endTime = microtime(true);
                $this->logReport($option, $model, $module, $endTime - $startTime);
            }
            $progressBar->advance();
        }
        $this->line('');
        $progressBar->finish();
    }

    /**
     * Create a specific resource.
     */
    protected function createResource(string $resource, string $model, string $module): void
    {
        switch ($resource) {
            case 'model':
                $withMigration = $this->option('migration');
                $params = ['model' => $model, 'module' => $module];
                if ($withMigration) {
                    $params['--migration'] = true;
                }
                $this->call('module:make-model', $params);
                break;
            case 'request':
                $this->call('module:make-request', ['name' => $model, 'module' => $module]);
                break;
            case 'controller':
                $this->call('module:make-controller', ['controller' => $model, 'module' => $module]);
                break;
            case 'policy':
                $this->call('module:make-policy', ['name' => $model, 'module' => $module]);
                break;
            case 'factory':
                $this->call('module:make-factory', ['name' => $model, 'module' => $module]);
                break;
            case 'resource':
                $this->makeResource($model, $module);
                break;
            case 'service':
                $this->serviceGenerator->createService($model, $module, $this->option('repository'));
                break;
            case 'repository':
                $this->serviceGenerator->createRepository($model, $module);
                break;
        }
    }

    private function makeResource(string $model, string $module): void
    {
        $this->generator->generate(
            from: 'resource',
            to: $this->generator->getPath($module, $model, 'Http/Resources'),
            as: $model . 'Resource.php',
            with: [
                '$MODEL$' => $this->generator->getNamespace($module, $model, 'Models'),
                '$NAMESPACE$' => $this->generator->getNamespace($module, $model, "Http\\Resources"),
                '$CLASS$' => $model
            ]
        );
    }

    /**
     * Log the report for each created resource.
     */
    protected function logReport(string $resource, string $model, string $module, float $timeTaken): void
    {
        $this->report[] = [
            'resource' => ucfirst($resource),
            'path' => $model . '/' . $module,
            'time' => number_format($timeTaken, 2)
        ];
    }

    /**
     * Output the report after the process is finished.
     */
    protected function outputReport(): void
    {
        $headers = ['Resource', 'Short Path', 'Time Taken (seconds)'];
        table($headers, $this->report);
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::OPTIONAL, 'The name of the model.'],
            ['module', InputArgument::OPTIONAL, 'The name of the module.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['request', 'r', InputOption::VALUE_NONE, 'Create a request.'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a controller.'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a migration.'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Create a policy.'],
            ['resource', '', InputOption::VALUE_NONE, 'Create a resource.'],
            ['service', 's', InputOption::VALUE_NONE, 'Create a service.'],
            ['repository', 'R', InputOption::VALUE_NONE, 'Create a repository.'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a factory.'],
        ];
    }
}
