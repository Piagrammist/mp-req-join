<?php declare(strict_types=1);

use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings;

require __DIR__ . '/vendor/autoload.php';

$settings = new Settings;
$settings->getLogger()
    ->setType(Logger::FILE_LOGGER);
$settings->getAppInfo()
    ->setApiId(API_ID)
    ->setApiHash(API_HASH);

is_dir(SESSION_DIR) || mkdir(SESSION_DIR);
Rz\BotEventHandler::startAndLoopBot(realpath(SESSION_DIR), BOT_TOKEN, $settings);
