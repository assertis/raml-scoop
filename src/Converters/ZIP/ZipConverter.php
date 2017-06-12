<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\ZIP;

use Assertis\RamlScoop\Converters\Converter;
use Assertis\RamlScoop\Schema\Project;
use Assertis\RamlScoop\Schema\Source;
use Assertis\RamlScoop\Tools\ImprovedMountManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ZipConverter implements Converter
{
    /**
     * @var Filesystem
     */
    private $temp;

    /**
     * @param Filesystem $temp
     */
    public function __construct(Filesystem $temp)
    {
        $this->temp = $temp;
    }

    /**
     * @inheritdoc
     */
    public function convert(Project $project): Filesystem
    {
        $out = new Filesystem(new ZipArchiveAdapter($this->getTemporaryZipPath()));

        /** @var Source $source */
        foreach ($project->getSources() as $source) {
            $zipName = $this->getZipName($source->getName());
            $source = new Filesystem(new Local($source->getDirectory()));
            $out->write($zipName, $this->getZipped($source));
        }

        return $out;
    }

    /**
     * @param Filesystem $source
     * @return string
     */
    private function getZipped(Filesystem $source): string
    {
        $path = $this->getTemporaryZipPath();
        $adapter = new ZipArchiveAdapter($path);
        $zip = new Filesystem($adapter);

        $zipManager = new ImprovedMountManager([
            'src' => $source,
            'zip' => $zip,
        ]);
        $zipManager->copyDirectory('src://', 'zip://');

        $adapter->getArchive()->close();
        
        return file_get_contents($path);
    }

    /**
     * @return string
     */
    private function getTemporaryZipPath(): string
    {
        /** @var Local $adapter */
        $adapter = $this->temp->getAdapter();

        return tempnam($adapter->getPathPrefix(), 'ZipConverter') . '.zip';
    }

    /**
     * @param string $name
     * @return string
     */
    private function getZipName(string $name): string
    {
        $clean = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
        $short = mb_ereg_replace("([\.]{2,})", '', $clean);

        return strtolower(str_replace(' ', '-', $short)) . '.zip';
    }
}
