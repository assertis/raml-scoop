<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Configuration;

use Symfony\Component\Config\Loader\FileLoader;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class JsonLoader extends FileLoader
{
    /**
     * @param string $resource
     * @param string|null $type
     * @return array
     */
    public function load($resource, $type = null)
    {
        return json_decode(file_get_contents($resource), true);
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
