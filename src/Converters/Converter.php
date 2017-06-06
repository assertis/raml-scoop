<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters;

use Assertis\RamlScoop\Schema\Project;
use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
interface Converter
{
    /**
     * @param Project $project
     * @return Filesystem
     */
    public function convert(Project $project): Filesystem;
}
