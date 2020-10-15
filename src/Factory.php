<?php

declare(strict_types=1);

namespace Ravisdo\Bot;

use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;
use Discord\DiscordCommandClient;
use Psr\Log\LoggerInterface;

class Factory
{
    private Configuration $configuration;
    private ?DiscordCommandClient $discordCommandClient = null;
    private ?LoggerInterface $logger = null;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function fromConfiguration(Configuration $configuration): Factory
    {
        return new self($configuration);
    }

    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $logger = new Logger($this->configuration->getEnvironment());

            $mysqlHandler = new MySqlLoggingHandler();
            $logger->pushHandler($mysqlHandler);

            $this->logger = $logger;
        }
        return $this->logger;
    }

    public function getDiscordCommandClient(): DiscordCommandClient
    {
        if ($this->discordCommandClient === null) {
            $this->discordCommandClient = new DiscordCommandClient([
                'token' => $this->configuration->getDiscordBotToken(),
                'prefix' => $this->configuration->getDiscordCommandPrefix(),
                'discordOptions' => [
                    'logger' => $this->getLogger(),
                ],
            ]);
        }
        return $this->discordCommandClient;
    }

    public function initializeDatabase(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $this->configuration->getMySqlHostname(),
            'database' => $this->configuration->getMySqlDatabaseName(),
            'username' => $this->configuration->getMySqlUsername(),
            'password' => $this->configuration->getMySqlPassword(),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->bootEloquent();
        $capsule->setAsGlobal();
        $this->getLogger()->debug('Database initialized');
    }

    public function getDiscordEvents(DiscordCommandClient $client): DiscordEvents
    {
        return new DiscordEvents($client, $this->getLogger());
    }

    public function getDiscordCommands(DiscordCommandClient $client): DiscordCommands
    {
        return new DiscordCommands($client, $this->getLogger());
    }
}
