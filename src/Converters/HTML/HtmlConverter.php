<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\HTML;

use Assertis\RamlScoop\Converters\Converter;
use Assertis\RamlScoop\Schema\Project;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Flysystem\MountManager;
use Twig_Environment;
use Twig_Function;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class HtmlConverter implements Converter
{
    private $initialized = false;

    /**
     * @param Project $project
     * @return Twig_Environment
     */
    private function getTwig(Project $project): Twig_Environment
    {
        $twig = $project->getTheme()->getTwig();
        
        if (!$this->initialized) {
            $twig->addFunction(
                new Twig_Function('schema', [$this, 'getSchemaHtml'], ['is_safe' => ['html']])
            );

            $twig->addFunction(
                new Twig_Function('example', [$this, 'getExampleHtml'], ['is_safe' => ['html']])
            );

            $twig->addFunction(
                new Twig_Function('dump', 'dump', ['is_safe' => ['html']])
            );

            $this->initialized = true;
        }

        return $twig;
    }

    /**
     * @inheritdoc
     */
    public function convert(Project $project): Filesystem
    {
        $html = $this->getTwig($project)->render('Project.twig', ['project' => $project]);

        $filesystem = new Filesystem(new MemoryAdapter());
        $filesystem->put('/index.html', $html);

        $manager = new MountManager([
            'res' => $project->getTheme()->getResources(),
            'out' => $filesystem
        ]);

        $contents = $manager->listContents('res://', true);
        foreach ($contents as $fileNode) {
            if ($fileNode['type'] == 'dir') {
                $manager->createDir('out://' . $fileNode['path']);
                continue;
            }

            $manager->put(
                'out://' . $fileNode['path'],
                $manager->read('res://' . $fileNode['path'])
            );
        }

        return $filesystem;
    }

    /**
     * @param string $json
     * @return string
     */
    private function getJsonCodeSample(string $json): string
    {
        $json = json_encode(json_decode($json, true), JSON_PRETTY_PRINT);

        return "<pre><code class='language-json hljs json'>{$json}</code></pre>\n";
    }

    /**
     * @param string $header
     * @param string $json
     * @return string
     */
    public function getExampleHtml(string $header, string $json): string
    {
        return
            '<h5 class="example-header">' . $header . ' <span class="snippet-toggle hide-print">(show)</span></h5>' .
            '<div class="hide-screen">' . $this->getJsonCodeSample($json) . "</div>";
    }

    /**
     * @param string $header
     * @param string $schema
     * @return string
     */
    public function getSchemaHtml(string $header, string $schema): string
    {
        $data = json_decode($schema, true);
        $stripped = $this->removeSchemaIds($data);
        $json = json_encode($stripped);

        return
            '<h5 class="schema-header">' . $header . ' <span class="snippet-toggle hide-print">(show)</span></h5>' .
            '<div class="hide-screen">' . $this->getJsonCodeSample($json) . "</div>";
    }

    /**
     * @param array $data
     * @return array
     */
    private function removeSchemaIds(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->removeSchemaIds($value);
            } elseif ($key === 'id' && strpos($value, 'file:') === 0) {
                unset($data[$key]);
            } elseif ($key === '$schema') {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
