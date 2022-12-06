<?php

namespace ThirstPlugin;

class Config
{
    /** @var mixed[] $config Containing custom configuration */
    private static $config;

    /**
     * Get s the key inside configuration.
     * When accessing an array you can use . (full stop) as a separator, for example
     * api.url will access [ 'api' => ['url' => 'http://yoururl.com' ]]
     *
     * @param string $key
     * @return void
     */
    public static function get(string $key)
    {
        self::fetchConfig();
        return self::getSingleKey($key);
    }

    /**
     * Returns a single key of data inside configuration. This method runs in recursion.
     *
     * @param string $key
     * @param mixed[] $config
     * @return mixed                        Value configured under a certain key
     * @throws \InvalidArgumentException    If key does not exist
     */
    private static function getSingleKey(string $key, array $config = null)
    {
        $config = $config ?? self::$config;
        $keyParts = explode('.', $key, 2);
        $currentKey = array_shift($keyParts);
        $nextKey = array_shift($keyParts);

        // Make sure that the key exists
        if (!array_key_exists($currentKey, $config)) {
            throw new \InvalidArgumentException(sprintf('Requested key %s cannot be found', $currentKey));
        }

        // If it is an array, run recursively
        return $nextKey
            ? self::getSingleKey($nextKey, $config[$currentKey])
            : $config[$currentKey];
    }

    /**
     * Returns the configuration path
     *
     * @return string
     */
    private static function getConfigPath(): string
    {
        return __DIR__ . '/../config.json';
    }

    /**
     * Fetches configuration files from the plugin
     *
     * @return void
     * @throws \Exception if no configuration is found
     */
    private static function fetchConfig(): void
    {
        if (!file_exists(self::getConfigPath())) {
            throw new \Exception('Missing thirst plugin default configuration');
        }

        // Fetch the configuration
        self::$config = json_decode(file_get_contents(self::getConfigPath()), true);
    }
}