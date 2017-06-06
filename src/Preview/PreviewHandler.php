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
     * @return int
     */
    public function handle(string $uri): int
    {
        if ($uri === '/') {
            $this->outputIndex();
            
            return 0;
        }

        if (!$this->filesystem->has($uri)) {
            http_response_code(404);
            
            return 1;
        }

        $this->outputFile($uri);

        return 0;
    }

    private function outputIndex()
    {
        http_response_code(200);

        foreach ($this->filesystem->listContents() as $item) {
            print sprintf('<a href="%s">%s</a><br/>', $item['path'], $item['basename']);
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
