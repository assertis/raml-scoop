<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConfigurationResolver
{
    private const EXTENSIONS = ['json', 'php'];

    /**
     * @var string
     */
    private $configDir;
    /**
     * @var string
     */
    private $currentPath;

    /**
     * @param string $configDir
     * @param string $currentPath
     */
    public function __construct(string $configDir, string $currentPath)
    {
        $this->configDir = $configDir;
        $this->currentPath = $currentPath;
    }

    /**
     * @return FileLocatorInterface
     */
    private function getLocator(): FileLocatorInterface
    {
        return new ConfigurationLocator($this->configDir, self::EXTENSIONS);
    }

    /**
     * @return LoaderInterface
     */
    private function getLoader(): LoaderInterface
    {
        $locator = $this->getLocator();

        $loaderResolver = new LoaderResolver([
            new PhpLoader($locator),
            new JsonLoader($locator),
        ]);

        return new DelegatingLoader($loaderResolver);
    }
    
    /**
     * @param string $name
     * @return string
     */
    public function getPath(string $name): string
    {
        return $this->getLocator()->locate($name, $this->currentPath, true);
    }
    
    /**
     * @param string $input
     * @return array
     */
    public function resolve(string $input): array
    {
        $path = $this->getPath($input);
        $data = $this->getLoader()->load($path);

        $processor = new Processor();
        $structure = new GenerateConfiguration();

        return $processor->processConfiguration($structure, [$data]);
    }
}
