<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use Assertis\RamlScoop\Themes\Theme;
use League\Flysystem\FilesystemInterface;

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
     * Theme to render the project with.
     *
     * @var Theme
     */
    private $theme;
    /**
     * A list of output formats to generate.
     *
     * @var array
     */
    private $formats;
    /**
     * Output directory.
     *
     * @var FilesystemInterface
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
     * @param Theme $theme
     * @param array $formats
     * @param FilesystemInterface $output
     * @param Source[] $sources
     */
    public function __construct(
        string $name,
        Theme $theme,
        array $formats,
        FilesystemInterface $output,
        array $sources
    ) {
        $this->name = $name;
        $this->theme = $theme;
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
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @return array
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @return FilesystemInterface
     */
    public function getOutput(): FilesystemInterface
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
