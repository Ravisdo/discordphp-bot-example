<?php

declare(strict_types=1);

namespace Ravisdo\Bot;

class Configuration
{
    private array $configuration;

    private function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function fromArray(array $configuration): Configuration
    {
        return new self($configuration);
    }

    public function getMySqlDatabaseName(): string
    {
        return $this->configuration['DB_DATABASE'];
    }

    public function getMySqlHostname(): string
    {
        return $this->configuration['DB_HOST'];
    }

    public function getMySqlUsername(): string
    {
        return $this->configuration['DB_USERNAME'];
    }

    public function getMySqlPassword(): string
    {
        return $this->configuration['DB_PASSWORD'];
    }

    public function getEnvironment(): string
    {
        return $this->configuration['APP_ENV'];
    }

    public function getDiscordBotToken(): string
    {
        return $this->configuration['DISCORD_BOT_TOKEN'];
    }

    public function getDiscordCommandPrefix(): string
    {
        return $this->configuration['DISCORD_BOT_PREFIX'];
    }
}
