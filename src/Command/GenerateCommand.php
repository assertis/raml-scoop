<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Command;

use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class GenerateCommand extends Command
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @param ConfigurationResolver $configurationResolver
     */
    public function __construct(ConfigurationResolver $configurationResolver)
    {
        parent::__construct();

        $this->configurationResolver = $configurationResolver;
    }

    protected function configure()
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $configName = $input->getOption('config');
        $configPath = $this->configurationResolver->getPath($configName);

        $io->note('Using config: ' . $configPath);

        $config = $this->configurationResolver->resolve($configName);
        
        $io->text('Generating documentation...');
        $io->text('Generated documentation');

        $io->text('Flushing documentation to disk...');
        $io->text('Flushed documentation to: '.$config['output']);

        $io->success('Done');

        return 0;
    }
}
