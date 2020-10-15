<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Ravisdo\Bot\Configuration;
use Ravisdo\Bot\Factory;

include __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$environmentConfiguration = Dotenv::createImmutable(__DIR__)->safeLoad();

try {
    $configuration = Configuration::fromArray($environmentConfiguration);
    $factory = Factory::fromConfiguration($configuration);
    $factory->initializeDatabase();

    $client = $factory->getDiscordCommandClient();
    $factory->getDiscordEvents($client)->registerEvents();
    $factory->getDiscordCommands($client)->registerCommands();

    $client->run();
} catch (\Exception $exception) {
    fwrite(STDERR, $exception->getMessage());
}
