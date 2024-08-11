<?php

namespace Chattermax\Config;

use Dotenv\Dotenv;

class Config {
    private static $initialized = false;

    public static function init() {
        if (!self::$initialized) {
            $dotEnv = Dotenv::createUnsafeImmutable($_SERVER['DOCUMENT_ROOT']);
            $dotEnv->load();
            self::$initialized = true;
        }
    }

    public static function get($key) {
        self::init();
        return getenv($key);
    }
}
