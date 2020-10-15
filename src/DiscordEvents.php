<?php

declare(strict_types=1);

namespace Ravisdo\Bot;

use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use Psr\Log\LoggerInterface;

class DiscordEvents
{
    private DiscordCommandClient $client;
    private LoggerInterface $logger;

    public function __construct(DiscordCommandClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function registerEvents(): void
    {
        $this->registerReadyEvent();
        $this->registerMessageReceivedEvent();
        $this->registerMemberJoinedEvent();
    }

    private function registerReadyEvent(): void
    {
        $this->client->on('ready', function () {
            echo 'Bot is ready.' . PHP_EOL;
        });
    }

    private function registerMessageReceivedEvent(): void
    {
        $this->client->on('message', function (Message $message) {
            if ($message->author->id === $this->client->id) {
                return;
            }
            $this->logger->debug(
                sprintf(
                    'Received a message from "%s" with content "%s"',
                    $message->author->username,
                    $message->content
                ),
                [
                    'guild' => $message->author->guild_id
                ]
            );
        });
    }

    private function registerMemberJoinedEvent(): void
    {
        $this->client->on(Event::GUILD_MEMBER_ADD, function (Member $member) {
            $this->logger->info(
                sprintf(
                    'Member "%s" joined',
                    $member->username
                ),
                [
                    'guild' => $member->guild_id
                ]
            );
        });
    }
}
