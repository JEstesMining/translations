<?php

namespace App\MessageHandler;

use App\Entity\UserEventStore;
use App\Message\UserUpdatePasswordCommand;
use App\Message\UserUpdatedPasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class UserUpdatePasswordCommandHandler implements CommandHandlerInterface
{
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

    public function __invoke(UserUpdatePasswordCommand $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);
        $event = (new UserEventStore())
            ->setEventId((string) new Ulid())
            ->setEventType('UpdatedPassword')
            ->setAggregateRootId($message->getPayloadValue('id'))
            ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
            ->setPayload($message->getPayload())
            ->setMetadata($message->getMetadata())
        ;
        $this->manager->persist($event);
        $this->manager->flush();

        $this->eventBus->dispatch(new UserUpdatedPasswordEvent($message->getPayload(), $message->getMetadata()));
        $this->dispatcher->dispatch(new GenericEvent($event), 'user.updated_password');

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
