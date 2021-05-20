<?php

namespace App;

use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

trait HandleTrait
{
    private $_queryBus;


    private function setQueryBus($bus)
    {
        $this->_queryBus = $bus;
    }

    private function handle($message)
    {
        if (!$this->_queryBus instanceof MessageBusInterface) {
            throw new LogicException(sprintf('You must provide a "%s" instance in the "%s::$_queryBus" property, "%s" given.', MessageBusInterface::class, static::class, get_debug_type($this->_queryBus)));
        }

        $envelope = $this->_queryBus->dispatch($message);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }

        if (\count($handledStamps) > 1) {
            $handlers = implode(', ', array_map(function (HandledStamp $stamp): string {
                return sprintf('"%s"', $stamp->getHandlerName());
            }, $handledStamps));

            throw new LogicException(sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__, \count($handledStamps), $handlers));
        }

        return $handledStamps[0]->getResult();
    }
}
