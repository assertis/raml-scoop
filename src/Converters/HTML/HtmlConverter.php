<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\HTML;

use Assertis\RamlScoop\Converters\Converter;
use Assertis\RamlScoop\Schema\Project;
use Assertis\RamlScoop\Schema\Source;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Flysystem\MountManager;
use Twig_Environment as Twig;
use Twig_Function;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class HtmlConverter implements Converter
{
    /**
     * @var Filesystem
     */
    private $resources;
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param Filesystem $resources
     * @param Twig $twig
     */
    public function __construct(Filesystem $resources, Twig $twig)
    {
        $this->resources = $resources;
        $this->twig = $twig;

        $this->twig->addFunction(new Twig_Function('schema', [$this, 'getSchemaHtml'], ['is_safe' => ['html']]));
        $this->twig->addFunction(new Twig_Function('example', [$this, 'getExampleHtml'], ['is_safe' => ['html']]));
        $this->twig->addFunction(new Twig_Function('dump', 'dump', ['is_safe' => ['html']]));
    }

    /**
     * @inheritdoc
     */
    public function convert(Project $project): Filesystem
    {
        $out = '';

        $out .= "<h1>{$project->getName()}</h1>\n";

        /** @var Source $source */
        foreach ($project->getSources() as $source) {
            $out .= "<h2 class='source-header'>{$source->getName()}</h2>\n";

            if ($source->getDefinition()->getDocumentationList()) {
                $out .= $this->twig->render(
                    'Documentation.twig',
                    ['items' => $source->getDefinition()->getDocumentationList()]
                );
            }

            /** @var \Raml\Resource $resource */
            foreach ($source->getDefinition()->getResources() as $resource) {
                if (in_array($resource->getUri(), $source->getExcluded())) {
                    continue;
                }

                $out .= $this->twig->render(
                    'Resource.twig',
                    [
                        'source'   => $source,
                        'resource' => $resource,
                    ]
                );
                
                //foreach ($resource->getResources())
            }
        }

        $out = "
<html>
<head>
<link href='style.css' media='all' rel='stylesheet' />
<link href='print.css' media='print' rel='stylesheet' />
<link href='highlight.css' media='all' rel='stylesheet' />
</head>
<body>\n\n
" . $out . "\n
<script src='highlight.js'></script>
<script>hljs.initHighlightingOnLoad();</script>
<script>
var toggles = document.getElementsByClassName('snippet-toggle');
var ii;
for (ii = 0; ii < toggles.length; ii++) {
    toggles[ii].onclick = function(){
        if (this.innerHTML == '(hide)') {
            this.innerHTML = '(show)';
            this.parentNode.nextSibling.style.display = 'none';
        } else {
            this.innerHTML = '(hide)';
            this.parentNode.nextSibling.style.display = 'block';
        }
    };
}
</script>
</body>
</html>
";

        $filesystem = new Filesystem(new MemoryAdapter());
        $filesystem->put('/index.html', $out);

        $manager = new MountManager([
            'res' => $this->resources,
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
