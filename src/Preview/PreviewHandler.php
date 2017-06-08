<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Preview;

use League\Flysystem\Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class PreviewHandler
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $uri
     * @param string $format
     * @return int
     */
    public function handle(string $uri, string $format): int
    {
        if ($uri === '/') {
            $this->outputIndex($format);
            
            return 0;
        }

        if (!$this->filesystem->has($uri)) {
            http_response_code(404);
            
            return 1;
        }

        $this->outputFile($uri);

        return 0;
    }

    private function outputIndex(string $format)
    {
        http_response_code(200);

        print '<h1>Select file:</h1>';
        
        foreach ($this->filesystem->listContents() as $item) {
            print sprintf('<a href="%s?format=%s">%s</a><br/>', $item['path'], $format, $item['basename']);
        }
    }

    /**
     * @param string $uri
     */
    private function outputFile(string $uri)
    {
        http_response_code(200);

        $type = $this->filesystem->getMimetype($uri);

        header(sprintf('Content-Type: %s', $type));
        print $this->filesystem->read($uri);
    }
}
