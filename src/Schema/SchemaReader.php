<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use Raml\ApiDefinition;
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
     * @var Parser
     */
    private $parser;

    /**
     * @param FileLocatorInterface $locator
     * @param Parser $parser
     */
    public function __construct(FileLocatorInterface $locator, Parser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * @param string $path
     * @return ApiDefinition
     */
    public function read(string $path): ApiDefinition
    {
        $path = $this->locator->locate($path);

        return $this->parser->parse($path);
    }
}
