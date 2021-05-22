<?php

namespace App\MessageHandler;

use App\Entity\Invite;
use App\Entity\InviteEventStore;
use App\Message\InviteGenerateCommand;
use App\Message\InviteGeneratedEvent;
use App\HandleTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class InviteGenerateCommandHandler implements CommandHandlerInterface
{
    use HandleTrait;

    protected $manager;
    protected $stopwatch;
    protected $eventBus;
    protected $dispatcher;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $eventBus, EventDispatcherInterface $dispatcher, LoggerInterface $logger, MessageBusInterface $queryBus)
    {
        $this->manager    = $manager;
        $this->stopwatch  = new Stopwatch(true);
        $this->eventBus   = $eventBus;
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;

        $this->setQueryBus($queryBus);
    }

    public function __invoke(InviteGenerateCommand $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);

        $entity = $this->manager->getRepository(Invite::class)->findOneBy([
            'code' => $message->getPayloadValue('code'),
        ]);
        if ($entity) {
            // Code already available for use
            return;
        }

        $event = (new InviteEventStore())
            ->setEventId((string) new Ulid())
            ->setEventType('Generated')
            ->setAggregateRootId($message->getPayloadValue('code'))
            ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
            ->setPayload($message->getPayload())
            ->setMetadata($message->getMetadata())
        ;
        $this->manager->persist($event);
        $this->manager->flush();

        $this->eventBus->dispatch(new InviteGeneratedEvent($message->getPayload(), $message->getMetadata()));
        $this->dispatcher->dispatch(new GenericEvent($event), 'invite.generated');

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
