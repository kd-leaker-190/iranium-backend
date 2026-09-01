<?php

namespace Modules\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ServiceGenerator
{
    public function __construct(
        private readonly Generator $generator
    )
    {
    }

    public function createService(string $name, string $module, $withRepo): void
    {
        $serviceName = str_replace('Service', '', $name) . 'Service';
        $path = $this->generator->getPath($module, $name, 'Services');

        $repoName = $name . 'Repository';
        $interfaceName = $repoName . 'Interface';
        $repoUser = $this->generator->getNamespace($module, $name, 'Repositories') . '\\' . $interfaceName;
        $data = [
            '{{namespace}}' => $this->generator->getNamespace($module, $name, 'Services'),
            '{{serviceName}}' => $serviceName,
            '{{repoUse}}' => $repoUser,
            '{{repoInterfaceName}}' => $interfaceName,
            '{{repoName}}' => lcfirst($repoName),
        ];

        if ($withRepo) {
            $this->generator->generate('service-with-repository', $path, "$serviceName.php", $data);
        } else {
            $this->generator->generate('service', $path, "$serviceName.php", $data);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function createRepository(string $name, string $module): void
    {
        $repositoryName = str_replace('Service', '', $name) . 'Repository';
        $interfaceName = str_replace('Service', '', $name) . 'RepositoryInterface';

        $path = $this->generator->getPath($module, $name, 'Repositories');
        $interfacePath = $this->generator->getPath($module, $name, 'Contracts');

        $repoData = [
            '{{namespace}}' => $this->generator->getNamespace($module, $name, 'Repositories'),
            '{{repositoryName}}' => $repositoryName,
            '{{interfaceName}}' => $interfaceName,
        ];

        $repoInterfaceData = [
            '{{namespace}}' => $this->generator->getNamespace($module, $name, "Contracts"),
            '{{interfaceName}}' => $interfaceName,
        ];

        $this->generator->generate('repository', $path, "$repositoryName.php", $repoData);
        $this->generator->generate('repository-interface', $interfacePath, "$interfaceName.php", $repoInterfaceData);
    }
}
