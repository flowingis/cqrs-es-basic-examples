<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc497fd016541ce02c0a254c64f890211
{
    public static $files = array (
        'a4ecaeafb8cfb009ad0e052c90355e98' => __DIR__ . '/..' . '/beberlei/assert/lib/Assert/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Broadway\\UuidGenerator\\' => 23,
        ),
        'A' => 
        array (
            'Assert\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Broadway\\UuidGenerator\\' => 
        array (
            0 => __DIR__ . '/..' . '/broadway/uuid-generator/src/Broadway/UuidGenerator',
        ),
        'Assert\\' => 
        array (
            0 => __DIR__ . '/..' . '/beberlei/assert/lib/Assert',
        ),
    );

    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Broadway\\' => 
            array (
                0 => __DIR__ . '/..' . '/broadway/broadway/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc497fd016541ce02c0a254c64f890211::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc497fd016541ce02c0a254c64f890211::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc497fd016541ce02c0a254c64f890211::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
