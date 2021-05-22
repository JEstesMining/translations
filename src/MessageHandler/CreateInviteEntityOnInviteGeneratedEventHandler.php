<?php

namespace App\MessageHandler;

use App\Entity\Invite;
use App\Message\InviteGeneratedEvent;
use App\HandleTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class CreateInviteEntityOnInviteGeneratedEventHandler implements EventHandlerInterface
{
    use HandleTrait;

    protected $manager;
    protected $stopwatch;

    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger, MessageBusInterface $queryBus)
    {
        $this->manager   = $manager;
        $this->stopwatch = new Stopwatch(true);
        $this->logger    = $logger;

        $this->setQueryBus($queryBus);
    }

    public function __invoke(InviteGeneratedEvent $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);

        $entity = (new Invite())
            ->setCode($message->getPayloadValue('code'))
        ;
        $this->manager->persist($entity);
        $this->manager->flush();

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
