<?php

namespace App\EventSubscriber;

use App\Entity\UserEventStore;
use App\Message\UserLoggedInEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;

/**
 */
class DispatchInteractiveLoginToEventBusSubscriber implements EventSubscriberInterface
{
    private $eventBus;
    private $manager;

    public function __construct(MessageBusInterface $eventBus, EntityManagerInterface $manager)
    {
        $this->eventBus = $eventBus;
        $this->manager  = $manager;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user    = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();
        $payload = [
            'id' => (string) $user->getId(),
        ];
        $metadata = [
            'http_user_agent' => $request->server->get('HTTP_USER_AGENT'),
            'client_ip'       => $request->getClientIp(),
            'timestamp'       => (new \DateTime())->format('c'),
        ];

        // Store Event
        // @note Should this dispatch a command?
        $eventStore = (new UserEventStore())
            ->setEventId((string) new Ulid())
            ->setEventType('LoggedIn')
            ->setAggregateRootId((string) $user->getId())
            ->setCreatedAt(new \DateTimeImmutable($metadata['timestamp']))
            ->setPayload($payload)
            ->setMetadata($metadata)
        ;
        $this->manager->persist($eventStore);
        $this->manager->flush();

        $this->eventBus->dispatch(new UserLoggedInEvent($payload, $metadata));
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}
