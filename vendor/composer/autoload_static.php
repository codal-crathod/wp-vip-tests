<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdec288168af0e825524e53a759300e18
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dealerdirect\\Composer\\Plugin\\Installers\\PHPCodeSniffer\\' => 55,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dealerdirect\\Composer\\Plugin\\Installers\\PHPCodeSniffer\\' => 
        array (
            0 => __DIR__ . '/..' . '/dealerdirect/phpcodesniffer-composer-installer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdec288168af0e825524e53a759300e18::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdec288168af0e825524e53a759300e18::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdec288168af0e825524e53a759300e18::$classMap;

        }, null, ClassLoader::class);
    }
}