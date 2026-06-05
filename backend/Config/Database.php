<?php
declare(strict_types=1);

namespace App\Config;

use PDO;

require_once dirname(__DIR__, 2) . '/database/connection.php';

class Database
{
    public static function getConnection(): PDO
    {
        return \database_connection();
    }
}
