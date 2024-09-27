<?php declare(strict_types=1);

namespace Rz\Plugins;

use danog\MadelineProto\PluginEventHandler;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;

use Rz\Filters\FilterJoined;
use danog\MadelineProto\EventHandler\Filter\FilterCommand;
use danog\MadelineProto\EventHandler\Filter\Combinator\FiltersAnd;

final class StartPlugin extends PluginEventHandler
{
    #[FiltersAnd(new FilterJoined, new FilterCommand('start'))]
    public function handleMessage(PrivateMessage&Incoming $message): void
    {
        $message->reply("Hiya! ;)");
    }
}
