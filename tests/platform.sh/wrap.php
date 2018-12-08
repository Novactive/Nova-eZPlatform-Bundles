<?php
/**
 * Wrap a bundle in a Symfony Application
 * This script helps you to automate a Symfony application installation around a
 * bundle.
 *
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2018 Sébastien Morel (Plopix)
 * @license   https://github.com/Plopix/symfony-bundle-app-wrapper/blob/master/LICENSE
 */

$appRootDir = $_SERVER['WRAP_APP_DIR'] ?? null;
$bundleDir  = $_SERVER['WRAP_BUNDLE_DIR'] ?? getenv('PLATFORM_DIR') ?? '~';

if (null === $appRootDir) {
    echo "WRAP_APP_DIR must be defined.";
}

// Inject the needed bundles in the Kernel
// With 4 and Flex we should be able to do better
$kernelFilePath = "{$appRootDir}/app/AppKernel.php";
$kernel         = file_get_contents("{$appRootDir}/app/AppKernel.php");
$bundles        = array_map(
    function ($line) {
        list($useless, $fqdn) = explode(" ", $line);
        return '$bundles[] = new '.trim($fqdn).';';
    },
    file(__DIR__."/bundles.yaml")
);
$bundles[]      = 'return $bundles;';
$kernel         = \str_replace($bundles[count($bundles) - 1], implode(PHP_EOL, $bundles), $kernel);
file_put_contents($kernelFilePath, $kernel);

// Inject the configurations
// With 4 and Flex we should be able to do better
file_put_contents("{$appRootDir}/app/config/config.yml", file_get_contents(__DIR__."/configs.yaml"), FILE_APPEND);

// Inject the routes
// With 4 and Flex we should be able to do better
file_put_contents("{$appRootDir}/app/config/routing.yml", file_get_contents(__DIR__."/routes.yaml"), FILE_APPEND);

// Trick the Composer.json
$composerJsonPath   = "{$appRootDir}/composer.json";
$data               = json_decode(file_get_contents($composerJsonPath), true);
$bundleComposerJson = json_decode(file_get_contents("{$bundleDir}/composer.json"), true);

// re map paths, one directory before because composer create-project install in a dir
$psr4 = array_map(
    function ($item) {
        return "../{$item}";
    },
    $bundleComposerJson["autoload"]['psr-4'] ?? []
);

$data["autoload"]['psr-4'] += $psr4;
$data['require']           += $bundleComposerJson['require'] ?? [];
$data['require-dev']       += $bundleComposerJson['require-dev'] ?? [];

// write the composer.json
file_put_contents(
    $composerJsonPath,
    json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
);
