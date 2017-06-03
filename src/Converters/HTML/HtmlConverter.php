<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\HTML;

use Assertis\RamlScoop\Converters\Converter;
use Assertis\RamlScoop\Schema\Project;
use Assertis\RamlScoop\Schema\Source;
use League\Flysystem\Filesystem;
use Raml\Body;
use Raml\Method;
use Raml\Response;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class HtmlConverter implements Converter
{
    /**
     * @inheritdoc
     */
    public function convert(Project $project, Filesystem $filesystem, string $filesystemPrefix): void
    {
        $out = '';

        $out .= "<h1>{$project->getName()}</h1>\n";

        /** @var Source $source */
        foreach ($project->getSources() as $source) {

            $out .= "<h2 class='source-header'>{$source->getName()}</h2>\n";

            /** @var \Raml\Resource $resource */
            foreach ($source->getDefinition()->getResources() as $resource) {

                $out .= "
                <h3 class='resource-header'>{$resource->getDisplayName()}</h3>
                <p class='resource-description'>{$resource->getDescription()}</p>
                ";

                /** @var Method $method */
                foreach ($resource->getMethods() as $method) {
                    $out .= "
<h4 class='method-header'>
    <span class='method-type'>{$method->getType()}</span> 
    <span class='method-uri-prefix'>{$source->getPrefix()}</span><span class='method-uri'>{$resource->getUri()}</span>
</h4>\n
                    ";

                    /** @var Body $requestBody */
                    foreach ($method->getBodies() as $requestBody) {
                        $out .= $this->getJsonSchema('Request schema', (string)$requestBody->getSchema());

                        foreach ($requestBody->getExamples() as $example) {
                            $out .= "<h5 class='method-request-example-header'>Example request</h5>";
                            $out .= $this->getJsonCodeExample($example);
                        }
                    }

                    /** @var Response $response */
                    foreach ($method->getResponses() as $response) {
                        /** @var Body $responseBody */
                        foreach ($response->getBodies() as $responseBody) {
                            $out .= "
<h5 class='method-response-header'>
    <span class='method-response-code'>{$response->getStatusCode()}</span>
    <span class='method-response-type'>{$responseBody->getMediaType()}</span>
</h5>\n";

                            $out .= $this->getJsonSchema('Response schema', (string)$responseBody->getSchema());

                            if ($responseBody->getExamples()) {
                                foreach ($responseBody->getExamples() as $example) {
                                    $out .= "<h5 class='method-request-example-header'>Example response</h5>";
                                    $out .= $this->getJsonCodeExample($example);
                                }
                            }
                        }
                    }
                }
            }
        }

        $out = "
<html>
<head>
<link href='style.css' media='all' rel='stylesheet' />
<link href='highlight.css' media='all' rel='stylesheet' />
</head>
<body>\n\n
" . $out . "\n
<script src='highlight.js'></script>
<script>hljs.initHighlightingOnLoad();</script>
<script>
var toggles = document.getElementsByClassName('schema-toggle');
var ii;
for (ii = 0; ii < toggles.length; ii++) {
    toggles[ii].onclick = function(){
        if (this.innerHTML == 'hide') {
            this.innerHTML = 'show';
            this.parentNode.nextSibling.style.display = 'none';
        } else {
            this.innerHTML = 'hide';
            this.parentNode.nextSibling.style.display = 'block';
        }
    };
}
</script>
</body>
</html>
";

        $filesystem->put($filesystemPrefix . '/index.html', $out);
    }

    /**
     * @param string $json
     * @return string
     */
    private function getJsonCodeExample(string $json): string
    {
        $json = json_encode(json_decode($json, true), JSON_PRETTY_PRINT);

        return "<pre><code class='language-json hljs json'>{$json}</code></pre>\n";
    }

    /**
     * @param string $header
     * @param string $schema
     * @return string
     */
    private function getJsonSchema(string $header, string $schema): string
    {
        $data = json_decode($schema, true);
        $stripped = $this->removeSchemaIds($data);

        return
            '<h5 class="method-request-schema-header">' . $header . ' (<span class="schema-toggle">show</span>)</h5>' .
            '<div class="hide">' . $this->getJsonCodeExample(json_encode($stripped)) . "</div>";
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
