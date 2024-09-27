<?php declare(strict_types=1);

namespace Rz;

use danog\MadelineProto\SimpleEventHandler;
use Rz\Plugins\{
    StartPlugin,
    MemberHandlerPlugin,
};

final class BotEventHandler extends SimpleEventHandler
{
    public function getReportPeers(): array
    {
        return [ADMIN];
    }

    public static function getPlugins(): array
    {
        return [
            MemberHandlerPlugin::class,
            StartPlugin::class,
        ];
    }
}
