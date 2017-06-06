<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Command;

use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Schema\ProjectReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Preview extends Command
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
     * @var string
     */
    private $rootDir;

    /**
     * @param ConfigurationResolver $configurationResolver
     * @param ProjectReader $projectReader
     * @param string $rootDir
     * @internal param SchemaReader $schemaReader
     */
    public function __construct(
        ConfigurationResolver $configurationResolver,
        ProjectReader $projectReader,
        string $rootDir
    ) {
        parent::__construct();

        $this->configurationResolver = $configurationResolver;
        $this->projectReader = $projectReader;
        $this->rootDir = $rootDir;
    }

    protected function configure(): void
    {
        $this
            ->setName('preview')
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
            'host',
            'o',
            InputOption::VALUE_REQUIRED,
            'Hostname on which to serve the generated preview',
            'localhost'
        );

        $this->addOption(
            'port',
            'p',
            InputOption::VALUE_REQUIRED,
            'Port on which to serve the generated preview',
            '9999'
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

        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $configName = $input->getOption('config');
        $configPath = $this->configurationResolver->getPath($configName);

        $io->note("Using config: {$configPath}");
        $io->note("Listening on: http://{$host}:{$port}");

        exec("FOO=BAR foo=bar CONFIG={$configPath} php -d variables_order=EGPCS -S {$host}:{$port} {$this->rootDir}/src/Preview/Router.php");

        return 0;
    }
}
