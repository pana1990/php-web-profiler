<?php

declare(strict_types=1);

namespace App\db;

use PDO;

final class Db
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("sqlite:" . __DIR__ . "/bbdd.db");
    }

    public static function setUp(): PDO
    {
        $self = new self();

        $table = <<<SQL
CREATE TABLE IF NOT EXISTS test(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	title TEXT NOT NULL
);
SQL;

        $self->pdo->exec($table);

        return $self->pdo;
    }
}
