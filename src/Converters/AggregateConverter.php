<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters;

use Assertis\RamlScoop\Schema\Project;
use InvalidArgumentException;
use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class AggregateConverter
{
    private $converters = [];

    /**
     * @param array $converters
     */
    public function __construct(array $converters)
    {
        foreach ($converters as $format => $converter) {
            $this->register($format, $converter);
        }
    }

    /**
     * @param string $format
     * @param Converter $converter
     */
    public function register(string $format, Converter $converter)
    {
        if (array_key_exists($format, $this->converters)) {
            throw new InvalidArgumentException(sprintf(
                'Converter for format %s already exists: %s',
                $format,
                get_class($this->converters[$format])
            ));
        }

        $this->converters[$format] = $converter;
    }

    /**
     * @param string $format
     * @return Converter
     */
    private function getConverter(string $format): Converter
    {
        if (!array_key_exists($format, $this->converters)) {
            throw new InvalidArgumentException(sprintf(
                'Converter for format %s is not registered',
                $format
            ));
        }

        return $this->converters[$format];
    }

    /**
     * @param string $format
     * @param Project $project
     * @return Filesystem
     */
    public function convert(string $format, Project $project): Filesystem
    {
        $converter = $this->getConverter($format);
        
        return $converter->convert($project);
    }
}
