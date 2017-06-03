<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Project
{
    /**
     * Project name.
     *
     * @var string
     */
    private $name;

    /**
     * A list of output formats to generate.
     *
     * @var array
     */
    private $formats;

    /**
     * Output directory.
     *
     * @var Filesystem
     */
    private $output;

    /**
     * A list of API definition sources.
     *
     * @var Source[]
     */
    private $sources;

    /**
     * @param string $name
     * @param array $formats
     * @param Filesystem $output
     * @param Source[] $sources
     */
    public function __construct(string $name, array $formats, Filesystem $output, array $sources)
    {
        $this->name = $name;
        $this->formats = $formats;
        $this->output = $output;
        $this->sources = $sources;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @return Filesystem
     */
    public function getOutput(): Filesystem
    {
        return $this->output;
    }

    /**
     * @return Source[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }
}
