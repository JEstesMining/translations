<?php

namespace App\MessageHandler;

use App\Entity\UserEventStore;
use App\Message\UserCreateCommand;
use App\Message\UserCreatedEvent;
use App\Message\InviteUseCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class UserCreateCommandHandler implements CommandHandlerInterface
{
    protected $manager;
    protected $stopwatch;
    protected $eventBus;
    protected $dispatcher;
    protected $commandBus;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $eventBus, EventDispatcherInterface $dispatcher, LoggerInterface $logger, MessageBusInterface $commandBus)
    {
        $this->manager    = $manager;
        $this->stopwatch  = new Stopwatch(true);
        $this->eventBus   = $eventBus;
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;
        $this->commandBus = $commandBus;
    }

    public function __invoke(UserCreateCommand $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);

        if ($message->hasPayloadValue('invite_code')) {
            $this->commandBus->dispatch(new InviteUseCommand([
                'code' => $message->getPayloadValue('invite_code'),
            ], array_merge($message->getMetadata(), [
                'user_id'   => $message->getPayloadValue('id'),
            ])));
        }

        $event = (new UserEventStore())
            ->setEventId((string) new Ulid())
            ->setEventType('Created')
            ->setAggregateRootId($message->getPayloadValue('id'))
            ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
            ->setPayload($message->getPayload())
            ->setMetadata($message->getMetadata())
        ;
        $this->manager->persist($event);
        $this->manager->flush();

        $this->eventBus->dispatch(new UserCreatedEvent($message->getPayload(), $message->getMetadata()));
        $this->dispatcher->dispatch(new GenericEvent($event), 'user.created');

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
