<?php

final class Bootstrap
{
    static $config = [
        'db' => [
            'driver' => 'mysql',
            'driverpdo' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'dbname' => 'employees',
        ],
        'cache' => TRUE,
        'limit' => 500,
    ];

    public static function init()
    {
        date_default_timezone_set('Europe/Prague');
    }

    public static function check($dir)
    {
        if (@!include $dir . '/vendor/autoload.php') {
            echo 'Install library using `installall` for all or manually `composer install` for every library';
            exit(1);
        }
    }

    public static function result($library, $libraryVersion, $startTime, $endTime)
    {
        echo str_pad($library, 12) . ' | ' .
            str_pad($libraryVersion, 15) . ' | ' .
            'Time: ', str_pad(sprintf('%.3f', $startTime + $endTime), 6, ' ', STR_PAD_LEFT), ' s | ',
        'Memory: ', str_pad(sprintf('%.1f', memory_get_peak_usage() / 1024 / 1024), 4, ' ', STR_PAD_LEFT), ' MB | ',
        'PHP: ', PHP_VERSION . PHP_EOL;
    }

}
