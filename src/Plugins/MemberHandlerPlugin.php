<?php declare(strict_types=1);

namespace Rz\Plugins;

use danog\MadelineProto\ParseMode;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\PluginEventHandler;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;

use danog\MadelineProto\EventHandler\Channel\ChannelParticipant;
use danog\MadelineProto\EventHandler\Participant\{Banned, Myself, Left};

final class MemberHandlerPlugin extends PluginEventHandler
{
    private int   $targetChatId = 0;
    private array $allowedUsers = [];

    public function __sleep(): array
    {
        return ['allowedUsers'];
    }

    public function onStart(): void
    {
        $this->targetChatId = $this->getId(CHANNEL_USERNAME);
    }

    #[Handler]
    public function handleMessage(PrivateMessage&Incoming $message): void
    {
        $userId = $message->senderId;
        if (!isset($this->allowedUsers[$userId])) {
            $this->allowedUsers[$userId] = $this->isParticipant($userId);
        }
        if (!$this->allowedUsers[$userId]) {
            $message->reply(
                \sprintf(
                    "Please join [our channel](https://t.me/%s) to use the bot!",
                    \str_replace('@', '', CHANNEL_USERNAME),
                ),
                ParseMode::MARKDOWN,
                noWebpage: true,
            );
        }
    }

    #[Handler]
    public function handleParticipant(ChannelParticipant $update): void
    {
        if ($update->chatId !== $this->targetChatId) {
            return;
        }

        $participant = $update->newParticipant;
        if ($participant instanceof Left || $participant instanceof Banned) {
            $this->allowedUsers[$participant->peer] = false;
        } elseif (!$participant instanceof Myself) {
            /** @var object{userId: int} $participant */
            $this->allowedUsers[$participant->userId] = true;
        }
    }

    private function isParticipant(int $userId): bool
    {
        try {
            $status = $this->channels->getParticipant(
                channel: CHANNEL_USERNAME,
                participant: $userId,
            )['participant']['_'] ?? 'channelParticipantLeft';
        } catch (RPCErrorException $e) {
            if ($e->rpc !== 'USER_NOT_PARTICIPANT') {
                throw $e;
            }
            $status = 'channelParticipantLeft';
        }
        return !\in_array(
            $status,
            ['channelParticipantLeft', 'channelParticipantBanned'],
            true,
        );
    }

    public function getUserStatus(int $userId): ?bool
    {
        return $this->allowedUsers[$userId] ?? null;
    }

    public function getAllowedUsers(): array
    {
        return $this->allowedUsers;
    }
}
