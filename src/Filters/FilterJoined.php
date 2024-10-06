<?php declare(strict_types=1);

namespace Rz\Filters;

use Attribute;

use Rz\Plugins\MemberHandlerPlugin;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\EventHandler\Update;
use danog\MadelineProto\EventHandler\InlineQuery;
use danog\MadelineProto\EventHandler\AbstractMessage;
use danog\MadelineProto\EventHandler\Query\ButtonQuery;
use danog\MadelineProto\EventHandler\Filter\Filter;

#[Attribute(Attribute::TARGET_METHOD)]
final class FilterJoined extends Filter
{
    private $plugin = null;
    private EventHandler $API;

    public function initialize(EventHandler $API): Filter
    {
        $this->API = $API;
        return $this;
    }

    public function apply(Update $update): bool
    {
        $this->plugin ??= $this->API->getPlugin(MemberHandlerPlugin::class);
        if ($update instanceof AbstractMessage) {
            $userId = $update->senderId;
        } elseif ($update instanceof ButtonQuery || $update instanceof InlineQuery) {
            $userId = $update->userId;
        }
        return isset($userId) ? $this->plugin->getUserStatus($userId) : false;
    }
}
