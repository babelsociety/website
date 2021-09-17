<?php
namespace BabelSociety;

trait Singleton {
    private static $instance;

    public static function get(): self {
        if (self::$instance === null)
            self::$instance = self::create();

        return self::$instance;
    }
}
