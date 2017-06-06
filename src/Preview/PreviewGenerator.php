<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Preview;

use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Converters\AggregateConverter;
use Assertis\RamlScoop\Schema\ProjectReader;
use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class PreviewGenerator
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;
    /**
     * @var ProjectReader
     */
    private $projectReader;
    /**
     * @var AggregateConverter
     */
    private $converter;

    /**
     * @param ConfigurationResolver $configurationResolver
     * @param ProjectReader $projectReader
     * @param AggregateConverter $converter
     */
    public function __construct(
        ConfigurationResolver $configurationResolver,
        ProjectReader $projectReader,
        AggregateConverter $converter
    ) {
        $this->configurationResolver = $configurationResolver;
        $this->projectReader = $projectReader;
        $this->converter = $converter;
    }

    /**
     * @param string $configPath
     * @param string $format
     * @return Filesystem
     */
    public function generate(string $configPath, string $format): Filesystem
    {
        $config = $this->configurationResolver->resolve($configPath);
        $project = $this->projectReader->read($config);
        
        return $this->converter->convert($format, $project);
    }
}
