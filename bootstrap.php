<?php
require './vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Logger;

/**
 * Container Resources do Slim.
 * Aqui dentro dele vamos carregar todas as dependências
 * da nossa aplicação que vão ser consumidas durante a execução
 * da nossa API
 */

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'slim-app',
            'level' => Logger::DEBUG,
            'path' => __DIR__ . "/src/logs/app.log",
        ],
    ],
];

$container = new \Slim\Container($configuration);

$debug = true;
$paths = array(__DIR__."/src/Models/Entity");

/**
 * Diretório de Entidades e Metadata do Doctrine
 */
$config = Setup::createAnnotationMetadataConfiguration($paths, $debug);

/**
 * Array de configurações da nossa conexão com o banco
 */
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

/**
 * Instância do Entity Manager
 */
$entityManager = EntityManager::create($conn, $config);

/**
 * Coloca o Entity manager dentro do container com o nome de em (Entity Manager)
 */
$container['em'] = $entityManager;
$app = new \Slim\App($container);
