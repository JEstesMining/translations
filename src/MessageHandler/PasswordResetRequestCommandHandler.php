<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Entity\UserEventStore;
use App\Message\PasswordResetRequestCommand;
use App\Message\PasswordResetRequestedEvent;
use App\HandleTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class PasswordResetRequestCommandHandler implements CommandHandlerInterface
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

    public function __invoke(PasswordResetRequestCommand $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);

        // 1. get user
        $entity = $this->manager->getRepository(User::class)->find($message->getPayloadValue('id'));
        if (null === $entity) {
            //throw new UnrecoverableMessageHandlingException('User not found in database');
            return;
        }

        // 2. Check password request ttl
        //    a. if not exipred, exit
        if ($entity->isPasswordRequestNonExpired(3600)) {
            //throw new UnrecoverableMessageHandlingException('Password Request has not expired');
            return;
        }

        // 3. Generate Confirmation token
        $token = (string) new Ulid();
        $entity->setConfirmationToken($token);

        // 4. Update User Entity with time requested and token
        $entity->setPasswordRequestedAt(new \DateTime());

        // 5. persist data to db
        //$this->manager->persist($entity);
        //$this->manager->flush();

        $event = (new UserEventStore())
            ->setEventId((string) new Ulid())
            ->setEventType('PasswordResetRequested')
            ->setAggregateRootId($message->getPayloadValue('id'))
            ->setCreatedAt(new \DateTimeImmutable($message->getMetadataValue('timestamp')))
            ->setPayload($message->getPayload())
            ->setMetadata($message->getMetadata())
        ;
        //$this->manager->persist($event);
        //$this->manager->flush();

        $this->eventBus->dispatch(new PasswordResetRequestedEvent($message->getPayload(), $message->getMetadata()));
        $this->dispatcher->dispatch(new GenericEvent($event), 'user.password_reset_requested');

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
