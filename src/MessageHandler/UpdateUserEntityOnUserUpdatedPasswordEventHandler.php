<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\HandleTrait;
use App\Message\UserUpdatedPasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateUserEntityOnUserUpdatedPasswordEventHandler implements MessageHandlerInterface
{
    use HandleTrait;

    protected $manager;
    protected $stopwatch;
    protected $logger;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $queryBus, LoggerInterface $logger)
    {
        $this->manager   = $manager;
        $this->logger    = $logger;
        $this->stopwatch = new Stopwatch(true);
        $this->setQueryBus($queryBus);
    }

    public function __invoke(UserUpdatedPasswordEvent $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);
        $entity = $this->manager->getRepository(User::class)->find($message->getPayloadValue('id'));

        if (null === $entity) {
            throw new UnrecoverableMessageHandlingException('User not found in database');
        }

        $entity->setPassword($message->getPayloadValue('password'));
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
