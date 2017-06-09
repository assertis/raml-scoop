<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Tools;

use League\Flysystem\MountManager;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ImprovedMountManager extends MountManager
{
    /**
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public function copyDirectory(string $source, string $destination): bool
    {
        foreach ($this->listContents($source, true) as $file) {
            if (!$this->copy($source . $file['path'], $destination . $file['path'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    public function deleteAll(string $path, bool $recursive = false): bool
    {
        list($prefix, $directory) = $this->getPrefixAndPath($path);
        $filesystem = $this->getFilesystem($prefix);

        foreach ($this->listContents($path, $recursive) as $file) {
            if (!$filesystem->has($file['path'])) {
                continue;
            }

            $result = $file['type'] === 'dir'
                ? $filesystem->deleteDir($file['path'])
                : $filesystem->delete($file['path']);

            if (!$result) {
                return false;
            }
        }

        return true;
    }
}
