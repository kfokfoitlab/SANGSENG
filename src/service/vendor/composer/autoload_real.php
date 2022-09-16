<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd4e6ca45fd0b12dc1c9c2d632ed8c47d
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitd4e6ca45fd0b12dc1c9c2d632ed8c47d', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd4e6ca45fd0b12dc1c9c2d632ed8c47d', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        \Composer\Autoload\ComposerStaticInitd4e6ca45fd0b12dc1c9c2d632ed8c47d::getInitializer($loader)();

        $loader->register(true);

        $includeFiles = \Composer\Autoload\ComposerStaticInitd4e6ca45fd0b12dc1c9c2d632ed8c47d::$files;
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequired4e6ca45fd0b12dc1c9c2d632ed8c47d($fileIdentifier, $file);
        }

        return $loader;
    }
}

/**
 * @param string $fileIdentifier
 * @param string $file
 * @return void
 */
function composerRequired4e6ca45fd0b12dc1c9c2d632ed8c47d($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;

        require $file;
    }
}
