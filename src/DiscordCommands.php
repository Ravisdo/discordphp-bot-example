<?php

declare(strict_types=1);

namespace Ravisdo\Bot;

use Discord\Parts\Guild\Emoji;
use Discord\Repository\Guild\MemberRepository;
use Discord\DiscordCommandClient;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;
use Discord\Parts\User\Member;
use Psr\Log\LoggerInterface;

class DiscordCommands
{
    private DiscordCommandClient $client;
    private LoggerInterface $logger;

    public function __construct(DiscordCommandClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function registerCommands(): void
    {
        $this->registerAssignRoleToUserCommand();
        $this->registerClearCommand();
        $this->registerSendEmojiCommand();
    }

    private function registerAssignRoleToUserCommand(): void
    {
        $this->client->registerCommand('assignUserToRole', function (Message $message, array $parameters) {
            $author = $message->author;
            if ($this->isRealUser($author) || !$this->hasPermission($author, 'administrator')) {
                return;
            }
            if (count($parameters) !== 2) {
                $message->reply('Wrong arguments. Must be exactly two arguments.');
            }
            /** @var $guild Guild */
            /** @var $role Role */
            /** @var $user Member */
            $username = $parameters[0];
            $roleName = $parameters[1];
            $guild = $this->client->guilds->get('id', $author->guild_id);

            $roleSelectionKey = filter_var($roleName, FILTER_VALIDATE_INT) === false ? 'name' : 'id';
            $role = $guild->roles->get($roleSelectionKey, $roleName);

            if (filter_var($username, FILTER_VALIDATE_INT) === false) {
                $user = $this->getUserByName($guild->members, $username);
            } else {
                $user = $guild->members->get('id', $username);
            }

            if ($user === null || $role === null) {
                $this->logger->error(
                    'User or role not found',
                    [
                        'guild' => $guild->id
                    ]
                );
                return;
            }

            $user->addRole($role)->then(
                function () use ($user, $role, $author, $message) {
                    $message->delete();
                    $this->logger->info(
                        sprintf(
                            '"%s" added to role "%s"',
                            $user->username,
                            $role->name
                        ),
                        [
                            'guild' => $author->guild_id
                        ]
                    );
                },
                function (string $reason) use ($author) {
                    $this->logger->error(
                        sprintf(
                            'Cannot assign role to user. Reason "%s"',
                            $reason
                        ),
                        [
                            'guild' => $author->guild_id
                        ]
                    );
                }
            );
        }, [
            'description' => 'Assign user to role',
        ]);
    }

    private function registerClearCommand(): void
    {
        $this->client->registerCommand('clear', function (Message $message) {
            if ($message->author->id === $this->client->id || !$message->author->getPermissions()->administrator) {
                return;
            }
            $message->channel->getMessageHistory([])->then(
                function (Collection $collection) use ($message) {
                    foreach ($collection as $item) {
                        $item->delete()->then(
                            function () {
                            },
                            function (string $reason) use ($message) {
                                $this->logger->error(
                                    sprintf(
                                        'Command "clear" failed with reason "%s"',
                                        $reason
                                    ),
                                    [
                                        'guild' => $message->author->guild_id
                                    ]
                                );
                            }
                        );
                    }
                },
                function (string $reason) use ($message) {
                    $this->logger->error(
                        sprintf(
                            'Command "clear" failed with reason "%s"',
                            $reason
                        ),
                        [
                            'guild' => $message->author->guild_id
                        ]
                    );
                }
            );
        }, [
            'description' => 'Removes all messages from the channel',
        ]);
    }

    private function getUserByName(MemberRepository $repository, string $username): ?Member
    {
        $userParts = explode('#', $username);
        foreach ($repository as $user) {
            if ($user->username === $userParts[0] && $user->discriminator === $userParts[1]) {
                return $user;
            }
        }
        return null;
    }

    private function registerSendEmojiCommand(): void
    {
        $this->client->registerCommand('sendEmoji', function (Message $message) {
            if ($message->author->id === $this->client->id) {
                return;
            }
            $message->author->guild->emojis->freshen();
            $message->channel->sendMessage($message->author->guild->emojis->filter(
                function (Emoji $emoji) {
                    if ($emoji->name === 'BearHug') {
                        return $emoji;
                    }
                    return null;
                }
            ));
        }, [
            'description' => 'Send Emoji to the called command channel',
        ]);
    }

    private function isRealUser(Member $author): bool
    {
        return $author->id === $this->client->id;
    }

    private function hasPermission(Member $author, string $string): bool
    {
        return $author->getPermissions()->$string;
    }
}
