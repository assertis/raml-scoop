<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Configuration;

use Assertis\RamlScoop\Tools\FlexibleFileLocator;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConfigurationLocator extends FlexibleFileLocator
{
    /**
     * @var string
     */
    private $defaultPath;
    /**
     * @var array
     */
    private $extensions;

    /**
     * @param string $defaultPath
     * @param array $extensions
     */
    public function __construct(string $defaultPath, array $extensions)
    {
        parent::__construct();
        
        if ($defaultPath[-1] !== '/') {
            $defaultPath .= '/';
        }

        $this->defaultPath = $defaultPath;
        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    protected function getFullPath(string $name, ?string $currentPath): ?string
    {
        if (strpos($name, '.') === false) {
            return $this->fromShortName($name);
        } else {
            return parent::getFullPath($name, $currentPath);
        }
    }

    /**
     * @param string $name
     * @return null|string
     */
    private function fromShortName(string $name): ?string
    {
        foreach ($this->extensions as $ext) {
            $test = $this->defaultPath . $name . '.' . $ext;

            if (file_exists($test)) {
                return realpath($test);
            }
        }

        return null;
    }
}
