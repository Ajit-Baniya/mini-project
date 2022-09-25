<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitcc06bbe3ba262e1a9be56eb863f1d36c
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

        spl_autoload_register(array('ComposerAutoloaderInitcc06bbe3ba262e1a9be56eb863f1d36c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitcc06bbe3ba262e1a9be56eb863f1d36c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitcc06bbe3ba262e1a9be56eb863f1d36c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}