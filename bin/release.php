<?php

// Initiate the file
require_once __DIR__ . '/../vendor/autoload.php';

use ThirstPlugin\Config;

class PluginCompiler
{
    private $root;
    private $output;

    const PLUGIN_FILES = [
        'accesstoken',
        'assets',
        'bootstrap.php',
        'classes',
        'config.json',
        'configure',
        'db',
        'lang',
        'lib.php',
        'vendor',
        'version.php'
    ];

    public function __construct()
    {
        $this->root = __DIR__ . '/../';
        $this->releaseFolder = 'thirst-lms-plugin-' . Config::get('version');
        $this->pluginName = 'thirst';
        $this->outputRoot = $this->root . '/releases/' . $this->releaseFolder;
        $this->output = $this->outputRoot . '/' . $this->pluginName;
    }

    public function __invoke()
    {
        // Confirm releases directory
        if (!file_exists($this->output)) {
            mkdir($this->output, 0775, true);
        }

        // Once done, get the list of all files required for the build
        $allFiles = glob('*');
        // Once all files are fetched, make sure that all files and folders are present
        $missingFiles = array_diff(self::PLUGIN_FILES, $allFiles);
        if ($missingFiles) {
            $this->msg('Following files are missing: ' . json_encode($missingFiles));
            exit();
        }

        // Once we've confirmed that there are no missing files, Start releasing process
        // Get the version of the plugin
        $this->msg('Starting the release of a plugin version ' . Config::get('version'));

        // Run through all files
        foreach (new \RecursiveDirectoryIterator($this->root, \FileSystemIterator::SKIP_DOTS) as $splFile) {
            $fileName = $splFile->getFilename();
            if (!$this->isFileRequired($fileName)) {
                continue;
            }
            // Copy file to the destination
            $this->msg('Copying ' . $fileName);
            exec(sprintf('cp -R %s %s', $fileName, $this->output . '/.'));
        }

        // Once all files are copied, create a zip package
        $this->msg('Creating zip package...');
        exec(sprintf('cd %s && zip -r -v ../%s.zip *', $this->outputRoot, $this->releaseFolder));

        // Once completed, remove the folder
        $this->msg('Cleaning up...');
        exec(sprintf('rm -rf %s', $this->outputRoot));

        $this->msg(sprintf('Completed! Package created %s.zip', $this->releaseFolder));
    }

    private function msg(string $msg)
    {
        echo $msg . PHP_EOL;
    }

    private function isFileRequired(string $fileName): bool
    {
        return in_array($fileName, self::PLUGIN_FILES);
    }
}

(new PluginCompiler)();