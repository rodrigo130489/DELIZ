<?php
declare(strict_types=1);

final class Database
{
    public function conectar(): PDO
    {
        $host = getenv('DELIZ_DB_HOST') ?: '127.0.0.1';
        $port = getenv('DELIZ_DB_PORT') ?: '3306';
        $name = getenv('DELIZ_DB_NAME') ?: 'deliz';
        $user = getenv('DELIZ_DB_USER');
        $password = getenv('DELIZ_DB_PASSWORD');

        if ($user === false || $user === '') {
            throw new RuntimeException('Falta DELIZ_DB_USER.');
        }

        return new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user,
            $password === false ? '' : $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
    }
}

