<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use JsonSchema\RefResolver;
use Raml\ApiDefinition;
use Raml\FileLoader\DefaultFileLoader;
use Raml\FileLoader\JsonSchemaFileLoader;
use Raml\Parser;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class SchemaReader
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @param FileLocatorInterface $locator
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $path
     * @return ApiDefinition
     */
    public function read(string $path): ApiDefinition
    {
        $path = $this->locator->locate($path);

        RefResolver::$maxDepth = 200;

        $parser = new Parser(null, null, [
            new JsonSchemaFileLoader(['jschema']),
            new DefaultFileLoader(),
        ]);

        $parser->configuration->enableDirectoryTraversal();

        return $parser->parse($path);
    }
}
