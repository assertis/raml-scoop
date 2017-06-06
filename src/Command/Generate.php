<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Command;

use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Converters\AggregateConverter;
use Assertis\RamlScoop\Schema\ProjectReader;
use League\Flysystem\Adapter\Local;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Generate extends Command
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;
    /**
     * @var ProjectReader
     */
    private $projectReader;
    /**
     * @var AggregateConverter
     */
    private $converter;

    /**
     * @param ConfigurationResolver $configurationResolver
     * @param ProjectReader $projectReader
     * @param AggregateConverter $converter
     */
    public function __construct(
        ConfigurationResolver $configurationResolver,
        ProjectReader $projectReader,
        AggregateConverter $converter
    ) {
        parent::__construct();

        $this->configurationResolver = $configurationResolver;
        $this->projectReader = $projectReader;
        $this->converter = $converter;
    }

    protected function configure(): void
    {
        $this
            ->setName('generate')
            ->setDescription('Generates the documentation according to a config file')
            ->setHelp('This command allows you to create a user...');

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Name of or path to the config file (default: config/default.[php|json])',
            'default'
        );

        $this->addOption(
            'dry-run',
            'd',
            InputOption::VALUE_NONE,
            'Generate the documentation in-memory, without saving to disk'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $configName = $input->getOption('config');
        $configPath = $this->configurationResolver->getPath($configName);

        $io->note('Using config: ' . $configPath);

        $config = $this->configurationResolver->resolve($configName);
        $project = $this->projectReader->read($config);

        $io->text(sprintf('Generating documentation for project "%s"...', $project->getName()));

        foreach ($project->getFormats() as $format) {
            $filesystem = $this->converter->convert($format, $project);

            $manager = new MountManager([
                'source'      => $filesystem,
                'destination' => $project->getOutput()
            ]);

            $contents = $manager->listContents('source://', true);
            foreach ($contents as $fileNode) {
                if ($fileNode['type'] == 'dir') {
                    $manager->createDir('destination://html/' . $fileNode['path']);
                    continue;
                }

                $manager->put(
                    'destination://html/' . $fileNode['path'],
                    $manager->read('source://' . $fileNode['path'])
                );
            }

        }

        $io->text('Flushing documentation to disk...');

        $adapter = $project->getOutput()->getAdapter();

        if ($adapter instanceof Local) {
            $io->text('Flushed documentation to: ' . $adapter->getPathPrefix());
        }

        $io->success('Done');

        return 0;
    }
}
