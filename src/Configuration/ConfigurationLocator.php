<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Configuration;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConfigurationLocator implements FileLocatorInterface
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    private $extensions;

    /**
     * @param string $path
     * @param array $extensions
     */
    public function __construct(string $path, array $extensions)
    {
        $this->path = $path;
        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        $full = null;

        if (strpos($name, '.') === false) {
            $full = $this->fromShortName($name);
        } elseif ($name[0] === '/') {
            // Full path
            $full = realpath($name);
        } else {
            $full = $this->fromPartialPath($name, $currentPath);
        }

        if (empty($full) || !file_exists($full)) {
            $message = sprintf('The file "%s" does not exist.', $name);
            throw new FileLocatorFileNotFoundException($message, 0, null, [$name]);
        }

        return $full;
    }

    /**
     * @param string $name
     * @return null|string
     */
    private function fromShortName(string $name): ?string
    {
        foreach ($this->extensions as $ext) {
            $path = $this->path;
            if ($path[-1] !== '/') {
                $path .= '/';
            }
            $test = $path . $name . '.' . $ext;

            if (file_exists($test)) {
                return realpath($test);
            }
        }

        return null;
    }

    /**
     * @param string $partial
     * @param $currentPath
     * @return null|string
     */
    private function fromPartialPath(string $partial, $currentPath): ?string
    {
        $paths = array_filter([$currentPath, $this->path]);

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
