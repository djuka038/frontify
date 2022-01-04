<?php

namespace App\Database\Migrations;

use App\Controller\AppConfig;

class Migration
{

    public static function runMigration()
    {
        $dsn = 'mysql:dbname=' . AppConfig::get('dbName') . ';host=' . AppConfig::get('dbHost');
        $db = new \PDO($dsn, AppConfig::get('dbUsername'), AppConfig::get('dbPassword'));

        $tableUser = "CREATE TABLE IF NOT EXISTS colors (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(63) NOT NULL,
            hexValue VARCHAR(63) NOT NULL
        );";

        $db->exec($tableUser);

        echo 'Migration Success!';
    }
}
