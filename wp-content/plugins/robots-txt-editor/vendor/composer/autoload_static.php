<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1506009a138202d514636d1177f4ae3f
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RobotsTxt\\' => 10,
        ),
        'P' => 
        array (
            'Premmerce\\SDK\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RobotsTxt\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Premmerce\\SDK\\' => 
        array (
            0 => __DIR__ . '/..' . '/premmerce/wordpress-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1506009a138202d514636d1177f4ae3f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1506009a138202d514636d1177f4ae3f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
