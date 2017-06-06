<?php
declare(strict_types=1);

namespace Assertis\RamlScoop;

use Assertis\RamlScoop\Command\Generate;
use Assertis\RamlScoop\Command\Preview;
use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Converters\AggregateConverter;
use Assertis\RamlScoop\Converters\HTML\HtmlConverter;
use Assertis\RamlScoop\Converters\HTML\MichelfMarkdown;
use Assertis\RamlScoop\Preview\PreviewGenerator;
use Assertis\RamlScoop\Schema\ProjectReader;
use Assertis\RamlScoop\Schema\SchemaReader;
use Assertis\RamlScoop\Tools\FlexibleFileLocator;
use Jralph\Twig\Markdown\Extension;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Pimple\Container;
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

        $this[SchemaReader::class] = function (Container $di) {
            $locator = new FlexibleFileLocator([$di['dir.current']]);

            return new SchemaReader($locator);
        };

        $this[ProjectReader::class] = function (Container $di) {
            return new ProjectReader($di[SchemaReader::class]);
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

        $this[AggregateConverter::class] = function (Container $di) {
            return new AggregateConverter([
                'html' => $di[HtmlConverter::class]
            ]);
        };
    }
}
