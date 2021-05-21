<?php

namespace App\MessageHandler;

use App\Entity\UserEventStore;
use App\HandleTrait;
use App\Message\UserUpdateSettingCommand;
use App\Message\UserUpdatedLocaleEvent;
use App\Message\UserUpdatedTimezoneEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class UserUpdateSettingCommandHandler implements CommandHandlerInterface
{
    use HandleTrait;

    protected $manager;
    protected $stopwatch;
    protected $eventBus;
    protected $dispatcher;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $eventBus, EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->manager    = $manager;
        $this->stopwatch  = new Stopwatch(true);
        $this->eventBus   = $eventBus;
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;
    }

    public function __invoke(UserUpdateSettingCommand $message)
    {
        // @todo
        // check payload and if user has keep settings the same, we
        // do not want to store and event or dispatch anything
        $this->stopwatch->start(__FUNCTION__, __CLASS__);

        // Locale
        if ($message->hasPayloadValue('locale')) {
            $event = (new UserEventStore())
                ->setEventId((string) new Ulid())
                ->setEventType('UpdatedLocale')
                ->setAggregateRootId($message->getPayloadValue('id'))
                ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
                ->setPayload([
                    'id'     => $message->getPayloadValue('id'),
                    'locale' => $message->getPayloadValue('locale')
                ])
                ->setMetadata($message->getMetadata())
            ;
            $this->manager->persist($event);
            $this->manager->flush();
            $this->eventBus->dispatch(new UserUpdatedLocaleEvent($message->getPayload(), $message->getMetadata()));
            $this->dispatcher->dispatch(new GenericEvent($event), 'user.updated_locale');
        }

        // Timezone
        if ($message->hasPayloadValue('timezone')) {
            $event = (new UserEventStore())
                ->setEventId((string) new Ulid())
                ->setEventType('UpdatedTimezone')
                ->setAggregateRootId($message->getPayloadValue('id'))
                ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
                ->setPayload([
                    'id'       => $message->getPayloadValue('id'),
                    'timezone' => $message->getPayloadValue('timezone'),
                ])
                ->setMetadata($message->getMetadata())
            ;
            $this->manager->persist($event);
            $this->manager->flush();
            $this->eventBus->dispatch(new UserUpdatedTimezoneEvent($message->getPayload(), $message->getMetadata()));
            $this->dispatcher->dispatch(new GenericEvent($event), 'user.updated_timezone');
        }

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
