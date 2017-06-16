<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Themes;

use League\Flysystem\FilesystemInterface;
use Twig_Environment;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Theme
{
    /**
     * @var FilesystemInterface
     */
    private $resources;
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param FilesystemInterface $resources
     * @param Twig_Environment $twig
     */
    public function __construct(FilesystemInterface $resources, Twig_Environment $twig)
    {
        $this->resources = $resources;
        $this->twig = $twig;
    }

    /**
     * @return FilesystemInterface
     */
    public function getResources(): FilesystemInterface
    {
        return $this->resources;
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig(): Twig_Environment
    {
        return $this->twig;
    }
}
