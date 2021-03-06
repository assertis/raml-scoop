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
     * @var string
     */
    private $directory;
    /**
     * @var ApiDefinition
     */
    private $definition;
    /**
     * @var array
     */
    private $excluded;
    /**
     * @var array
     */
    private $prefixes;

    /**
     * @param string $name
     * @param string $prefix
     * @param string $directory
     * @param ApiDefinition $definition
     * @param array $excluded List of excluded URI
     * @param array $prefixes List of prefixes for specific urls
     */
    public function __construct(
        string $name,
        string $prefix,
        string $directory,
        ApiDefinition $definition,
        array $excluded,
        array $prefixes
    ) {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->directory = $directory;
        $this->definition = $definition;
        $this->excluded = $excluded;
        $this->prefixes = $prefixes;
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
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
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

    /**
     * @return array
     */
    public function getPrefixes(): array
    {
        return $this->prefixes;
    }
}
