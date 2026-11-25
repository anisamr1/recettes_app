<?php
declare(strict_types=1);

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $localConfigPath = __DIR__ . '/config.local.php';
    $exampleConfigPath = __DIR__ . '/config.local.exemple.php';

    if (file_exists($localConfigPath)) {
        $config = require $localConfigPath;
    } elseif (file_exists($exampleConfigPath)) {
        $config = require $exampleConfigPath;
    } else {
        throw new RuntimeException('Fichier de configuration introuvable.');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['db_host'],
        (int) $config['db_port'],
        $config['db_name'],
        $config['db_charset']
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
    return $pdo;
}
