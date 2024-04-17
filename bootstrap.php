<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PenisBot\Bot;
use PenisBot\LoggerUpdate;
use PenisBot\Negotiator;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

const ROOT_PROJECT_DIR = __DIR__;

if (file_exists(ROOT_PROJECT_DIR . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PROJECT_DIR);
    $dotenv->load();
}

if ($_SERVER['DEBUG_MODE']) {
    ini_set('display_errors', 'on');
}

static $container;

if ($container !== null) {
    return $container;
}

$dependencies = [
    Connection::class => static function (): Connection {
        return DriverManager::getConnection([
            'url' => $_SERVER['DB_URL'],
            'dbname' => $_SERVER['DB_NAME'],
        ]);
    },

    Bot::class => static function (): Bot {
        return new Bot($_SERVER['BOT_TOKEN'], $_SERVER['BOT_NAME']);
    },

    Api::class => static function (Bot $bot): Negotiator {
        return new Negotiator($bot->getToken());
    },

    Update::class => static function (Api $telegram): Update {
        return $telegram->getWebhookUpdate();
    },

    LoggerUpdate::class => static function () {
        return new LoggerUpdate($_SERVER['PATH_TO_LOG']);
    },
];

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions($dependencies);

return $containerBuilder->build();
