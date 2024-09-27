<?php declare(strict_types=1);

namespace Rz\Filters;

use Attribute;

use Rz\Plugins\MemberHandlerPlugin;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\EventHandler\Update;
use danog\MadelineProto\EventHandler\Message;
use danog\MadelineProto\EventHandler\Filter\Filter;

#[Attribute(Attribute::TARGET_METHOD)]
final class FilterJoined extends Filter
{
    private EventHandler $API;

    public function initialize(EventHandler $API): Filter
    {
        $this->API = $API;
        return $this;
    }

    public function apply(Update $update): bool
    {
        return $update instanceof Message &&
            $this->API->getPlugin(MemberHandlerPlugin::class)->getUserStatus($update->senderId);
    }
}
