<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Schema;

use Assertis\RamlScoop\Themes\ThemeLoader;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ProjectReader
{
    /**
     * @var SchemaReader
     */
    private $schemaReader;
    /**
     * @var ThemeLoader
     */
    private $themeLoader;
    /**
     * @var FileLocatorInterface
     */
    private $locator;
    /**
     * @var string
     */
    private $tempPath;

    /**
     * @param SchemaReader $schemaReader
     * @param ThemeLoader $themeLoader
     * @param FileLocatorInterface $locator
     * @param string $tempPath
     */
    public function __construct(
        SchemaReader $schemaReader,
        ThemeLoader $themeLoader,
        FileLocatorInterface $locator,
        string $tempPath
    ) {
        $this->schemaReader = $schemaReader;
        $this->themeLoader = $themeLoader;
        $this->locator = $locator;
        $this->tempPath = $tempPath;
    }

    /**
     * @param array $config
     * @return Project
     */
    public function read(array $config): Project
    {
        $output = $this->getOutput($config['output']);
        $theme = $this->themeLoader->getTheme($config['theme']);

        $sources = [];
        foreach ($config['sources'] as $source) {
            if (isset($source['path'])) {
                $path = $source['path'];
            } elseif (isset($source['git'])) {
                $path = $this->fromGithub(
                    $source['git']['uri'],
                    $source['git']['branch'],
                    $source['git']['path']
                );
            } else {
                throw new InvalidArgumentException(
                    'Source has to have either a path or git information.'
                );
            }

            $definition = $this->schemaReader->read($path);

            $sources[] = new Source(
                $source['name'],
                $source['prefix'],
                dirname($this->locator->locate($path)),
                $definition,
                $source['exclude'],
                $source['prefixes']
            );
        }

        return new Project($config['name'], $theme, $config['formats'], $output, $sources);
    }

    /**
     * @param string $repo
     * @param string $branch
     * @param string $path
     * @return string
     */
    private function fromGithub(string $repo, string $branch, string $path): string
    {
        if (!preg_match('/^[^:]+\:(.+)$/', $repo, $matches)) {
            throw new InvalidArgumentException(sprintf(
                'Not a valid repository address: %s',
                $repo
            ));
        }

        $dirName = str_replace('/', '-', $matches[1]);
        $tempPath = $this->tempPath . '/' . $dirName;

        if (is_dir($tempPath)) {
            exec(sprintf('cd %s; git fetch --all; git reset --hard origin/%s', $tempPath, $branch));
        } else {
            exec(sprintf('git clone %s %s -b %s', $repo, $tempPath, $branch));
        }

        $ramlPath = realpath($tempPath . '/' . $path);

        if (empty($ramlPath)) {
            throw new InvalidArgumentException(sprintf(
                'Path %s does not exist',
                $tempPath . '/' . $path
            ));
        }

        return $ramlPath;
    }

    /**
     * @param string $path
     * @return Filesystem
     */
    private function getOutput(string $path): Filesystem
    {
        $realPath = $path[0] === '/' ? $path : getcwd() . '/' . $path;

        return new Filesystem(new Local($realPath));
    }
}
