<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Themes;

use Assertis\RamlScoop\Converters\HTML\MichelfMarkdown;
use Jralph\Twig\Markdown\Extension;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Config\FileLocatorInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ThemeLoader
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
     * @return Theme
     */
    public function getTheme(string $path): Theme
    {
        $fullPath = $this->locator->locate($path);

        $resources = new Filesystem(
            new Local($fullPath . '/Assets')
        );

        $twig = new Twig_Environment(
            new Twig_Loader_Filesystem($fullPath . '/Views')
        );

        $twig->addExtension(new Extension(new MichelfMarkdown()));

        return new Theme($resources, $twig);
    }
}
