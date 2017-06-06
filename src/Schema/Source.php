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
     * @var array
     */
    private $excluded;

    /**
     * @param string $name
     * @param string $prefix
     * @param ApiDefinition $definition
     * @param array $excluded List of excluded URI
     */
    public function __construct(string $name, string $prefix, ApiDefinition $definition, array $excluded)
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->definition = $definition;
        $this->excluded = $excluded;
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

    /**
     * @return array
     */
    public function getExcluded(): array
    {
        return $this->excluded;
    }
}
