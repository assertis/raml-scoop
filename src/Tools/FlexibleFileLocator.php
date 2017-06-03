<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Tools;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class FlexibleFileLocator implements FileLocatorInterface
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @param array $paths
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        $full = $this->getFullPath($name, $currentPath);

        if (empty($full)) {
            $message = sprintf('The file "%s" does not exist.', $name);
            throw new FileLocatorFileNotFoundException($message, 0, null, [$name]);
        }

        return $full;
    }

    /**
     * @param string $name
     * @param null|string $currentPath
     * @return bool|null|string
     */
    protected function getFullPath(string $name, ?string $currentPath): ?string
    {
        if ($name[0] === '/') {
            return realpath($name) ?? null;
        } else {
            return $this->fromPartialPath($name, $currentPath) ?? null;
        }
    }

    /**
     * @param string $partial
     * @param string|null $currentPath
     * @return string|null
     */
    private function fromPartialPath(string $partial, ?string $currentPath): ?string
    {
        $paths = $this->paths;

        if ($currentPath) {
            $paths[] = $currentPath;
        }

        foreach ($paths as $path) {
            if ($path[-1] !== '/') {
                $path .= '/';
            }

            if (file_exists($path . $partial)) {
                return realpath($path . $partial);
            }
        }

        return null;
    }
}
