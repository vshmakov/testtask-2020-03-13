<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

$paths = [
    PROJECT_DIR.'/src/Entity',
];
$isDevMode = 'dev' === $_ENV['APP_ENV'];
$dbParams = [
    'driver' => 'pdo_mysql',
    'host' => 'db',
    'port' => 3306,
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname' => $_ENV['DB_NAME'],
];

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

return EntityManager::create($dbParams, $config);
