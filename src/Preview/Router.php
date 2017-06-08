<?php
declare(strict_types=1);

use Assertis\RamlScoop\Configuration\ConfigurationResolver;
use Assertis\RamlScoop\Preview\PreviewGenerator;
use Assertis\RamlScoop\Preview\PreviewHandler;
use Assertis\RamlScoop\RamlScoop;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require getcwd() . '/vendor/autoload.php';

session_start();

$app = RamlScoop::getInstance(realpath(__DIR__ . '/../..'));

$configPath = getenv('CONFIG');

if (!empty($_GET['format'])) {
    $format = $_GET['format'];

    /** @var PreviewGenerator $generator */
    $generator = $app[PreviewGenerator::class];
    $filesystem = $generator->generate($configPath, $format);

    $_SESSION['preview'] = new PreviewHandler($filesystem);
} else {
    $format = '';
}

/** @var PreviewHandler $preview */
$preview = $_SESSION['preview'];

if (!($preview instanceof PreviewHandler) || ($_SERVER['SCRIPT_NAME'] === '/' && empty($format))) {
    /** @var array $config */
    $config = $app[ConfigurationResolver::class]->resolve($configPath);
    
    print '<h1>Select format:</h1>';
    
    foreach ($config['formats'] as $format) {
        print sprintf('<a href="?format=%s">%s</a><br/>', $format, strtoupper($format));
    }
    
    exit(0);
} else {
    exit($preview->handle($_SERVER['SCRIPT_NAME'], $format));
}
