<?php
declare(strict_types=1);

use Assertis\RamlScoop\Preview\PreviewGenerator;
use Assertis\RamlScoop\Preview\PreviewHandler;
use Assertis\RamlScoop\RamlScoop;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require getcwd() . '/vendor/autoload.php';

session_start();

$app = RamlScoop::getInstance(realpath(__DIR__ . '/../..'));

if (!empty($_GET['format'])) {
    $configPath = getenv('CONFIG');
    $format = $_GET['format'];

    /** @var PreviewGenerator $generator */
    $generator = $app[PreviewGenerator::class];
    $filesystem = $generator->generate($configPath, $format);

    $_SESSION['preview'] = new PreviewHandler($filesystem);
}

/** @var PreviewHandler $preview */
$preview = $_SESSION['preview'];

if (!($preview instanceof PreviewHandler)) {
    die('select format');
}

exit($preview->handle($_SERVER['SCRIPT_NAME']));
