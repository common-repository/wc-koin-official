<?php

namespace WKO\Helpers;

/**
 * Name: Config
 * Handle paths, URLs
 * @package Helpers
 * @since 1.0.0
 */
class Config
{
    /**
     * Get images URL
     * @since 1.0.0
     * @param string $relative
     * @return string
     */
    public static function __image($relative = "")
    {
        return plugins_url() . "/" . self::__folder() . "/resources/images/$relative";
    }

    /**
     * Get dist URL
     * @since 1.0.0
     * @param string @relative
     * @return string
     */
    public static function __dist($relative = "")
    {
        return plugins_url() . "/" . self::__folder() . "/dist/$relative";
    }

    /**
     * Get dir path
     * @since 1.0.0
     * @param string $dir
     * @param int $level
     * @return string
     */
    public static function __dir($dir = __DIR__, $level = 2)
    {
        return dirname($dir, $level);
    }


    /**
     * Get main file dir path
     * @since 1.0.0
     * @return string
     */
    public static function __main()
    {
        return self::__dir() . '/wc-koin-official.php';
    }


    /**
     * Get base file
     * @since 1.0.0
     * @return string
     */
    public static function __base()
    {
        return self::__folder() . "/wc-koin-official.php";
    }

    /**
     * Get plugin base folder
     * @since 1.0.0
     * @return string
     */
    public static function __folder()
    {
        $dir = explode("/", self::__dir());
        return $dir[count($dir) - 1];
    }

    /**
     * Get plugin base folder
     * @since 1.2.5
     * @return string
     */
    public static function __version()
    {
        return '1.3.4';
    }
}
