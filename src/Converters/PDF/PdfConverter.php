<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\PDF;

use Assertis\RamlScoop\Converters\Converter;
use Assertis\RamlScoop\Converters\HTML\HtmlConverter;
use Assertis\RamlScoop\Schema\Project;
use Assertis\RamlScoop\Tools\ImprovedMountManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use mikehaertl\wkhtmlto\Pdf;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class PdfConverter implements Converter
{
    /**
     * @var HtmlConverter
     */
    private $htmlConverter;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var Filesystem
     */
    private $temp;

    /**
     * @param HtmlConverter $htmlConverter
     * @param Pdf $pdf
     * @param Filesystem $temp
     */
    public function __construct(HtmlConverter $htmlConverter, Pdf $pdf, Filesystem $temp)
    {
        $this->htmlConverter = $htmlConverter;
        $this->pdf = $pdf;
        $this->temp = $temp;
    }

    /**
     * @inheritdoc
     */
    public function convert(Project $project): Filesystem
    {
        $html = $this->htmlConverter->convert($project);

        $manager = new ImprovedMountManager(['html' => $html, 'temp' => $this->temp]);
        $manager->deleteAll('temp://');
        $manager->copyDirectory('html://', 'temp://');
        
        /** @var Local $adapter */
        $adapter = $this->temp->getAdapter();
        
        $this->pdf->addPage($adapter->getPathPrefix().'/index.html');
            
        $out = new Filesystem(new MemoryAdapter());
        $out->write($this->getPdfFilename($project->getName()), $this->pdf->toString());
        
        return $out;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPdfFilename(string $name): string
    {
        $clean = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
        $short = mb_ereg_replace("([\.]{2,})", '', $clean);
        
        return strtolower(str_replace(' ', '-', $short)).'.pdf';
    }
}
