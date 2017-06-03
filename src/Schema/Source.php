<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use Raml\ApiDefinition;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Source
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var ApiDefinition
     */
    private $definition;

    /**
     * @param string $name
     * @param string $prefix
     * @param ApiDefinition $definition
     */
    public function __construct(string $name, string $prefix, ApiDefinition $definition)
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return ApiDefinition
     */
    public function getDefinition(): ApiDefinition
    {
        return $this->definition;
    }
}
