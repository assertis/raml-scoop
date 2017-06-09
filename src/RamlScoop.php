<?php
declare(strict_types=1);

namespace Assertis\RamlScoop;

use Assertis\RamlScoop\Command\Generate;
use Assertis\RamlScoop\Command\Preview;
use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Converters\AggregateConverter;
use Assertis\RamlScoop\Converters\HTML\HtmlConverter;
use Assertis\RamlScoop\Converters\HTML\MichelfMarkdown;
use Assertis\RamlScoop\Converters\PDF\PdfConverter;
use Assertis\RamlScoop\Preview\PreviewGenerator;
use Assertis\RamlScoop\Schema\ProjectReader;
use Assertis\RamlScoop\Schema\SchemaReader;
use Assertis\RamlScoop\Tools\FlexibleFileLocator;
use Jralph\Twig\Markdown\Extension;
use JsonSchema\RefResolver;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use mikehaertl\wkhtmlto\Pdf;
use Pimple\Container;
use Raml\FileLoader\DefaultFileLoader;
use Raml\FileLoader\JsonSchemaFileLoader;
use Raml\Parser;
use Symfony\Component\Console\Application;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class RamlScoop extends Container
{
    /**
     * @param string $rootDir
     * @return RamlScoop
     */
    public static function getInstance(string $rootDir): self
    {
        static $instance;

        if (empty($instance)) {
            $instance = new self;
            $instance->initialize($rootDir);
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function runCLI(): int
    {
        return $this[Application::class]->run();
    }

    /**
     * @param $rootDir
     */
    public function initialize($rootDir)
    {
        $this['dir.current'] = getcwd();
        $this['dir.root'] = $rootDir;
        $this['dir.config'] = $this['dir.root'] . '/config';
        $this['dir.resources'] = $this['dir.root'] . '/resources';
        $this['dir.tmp'] = $this['dir.root'] . '/tmp';

        $this['app.commands'] = [
            Generate::class,
            Preview::class
        ];

        $this[Application::class] = function (Container $di) {
            $app = new Application();

            foreach ($this['app.commands'] as $class) {
                $app->add($di[$class]);
            }

            return $app;
        };

        $this[ConfigurationResolver::class] = function (Container $di) {
            return new ConfigurationResolver($di['dir.config'], $di['dir.current']);
        };

        $this[Parser::class] = function () {
            RefResolver::$maxDepth = 200;

            $parser = new Parser(null, null, [
                new JsonSchemaFileLoader(['jschema']),
                new DefaultFileLoader(),
            ]);

            $parser->configuration->enableDirectoryTraversal();

            return $parser;
        };

        $this[SchemaReader::class] = function (Container $di) {
            $locator = new FlexibleFileLocator([$di['dir.current']]);

            return new SchemaReader($locator, $di[Parser::class]);
        };

        $this[ProjectReader::class] = function (Container $di) {
            $tempPath = $di['dir.tmp'] . '/project-reader';

            return new ProjectReader($di[SchemaReader::class], $tempPath);
        };

        $this[PreviewGenerator::class] = function (Container $di) {
            return new PreviewGenerator(
                $di[ConfigurationResolver::class],
                $di[ProjectReader::class],
                $di[AggregateConverter::class]
            );
        };

        $this[Generate::class] = function (Container $di) {
            return new Generate(
                $di[ConfigurationResolver::class],
                $di[ProjectReader::class],
                $di[AggregateConverter::class]
            );
        };

        $this[Preview::class] = function (Container $di) {
            return new Preview(
                $di[ConfigurationResolver::class],
                $di[ProjectReader::class],
                $di['dir.root']
            );
        };

        $this[HtmlConverter::class] = function (Container $di) {
            $resourcesDir = $di['dir.resources'] . '/HtmlConverter';

            $resources = new Filesystem(
                new Local($resourcesDir . '/Assets')
            );

            $twig = new Twig_Environment(
                new Twig_Loader_Filesystem($resourcesDir . '/Views')
            );
            $twig->addExtension(new Extension(new MichelfMarkdown()));

            return new HtmlConverter($resources, $twig);
        };

        $this[PdfConverter::class] = function (Container $di) {
            return new PdfConverter(
                $di[HtmlConverter::class],
                new Pdf(),
                new Filesystem(new Local($di['dir.tmp'] . '/pdf-converter'))
            );
        };

        $this[AggregateConverter::class] = function (Container $di) {
            return new AggregateConverter([
                'html' => $di[HtmlConverter::class],
                'pdf'  => $di[PdfConverter::class],
            ]);
        };
    }
}
