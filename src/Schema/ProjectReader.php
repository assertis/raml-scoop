<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ProjectReader
{
    /**
     * @var SchemaReader
     */
    private $schemaReader;

    /**
     * @param SchemaReader $schemaReader
     */
    public function __construct(SchemaReader $schemaReader)
    {
        $this->schemaReader = $schemaReader;
    }

    /**
     * @param array $config
     * @return Project
     */
    public function read(array $config): Project
    {
        $output = $this->getOutput($config['output']);

        $sources = [];
        foreach ($config['sources'] as $source) {
            $definition = $this->schemaReader->read($source['path']);
            $sources[] = new Source($source['name'], $source['prefix'], $definition, $source['exclude']);
        }
        
        return new Project($config['name'], $config['formats'], $output, $sources);
    }

    /**
     * @param string $path
     * @return Filesystem
     */
    private function getOutput(string $path): Filesystem
    {
        $realPath = realpath($path[0] === '/' ? $path : getcwd() . '/' . $path);

        return new Filesystem(new Local($realPath));
    }
}
