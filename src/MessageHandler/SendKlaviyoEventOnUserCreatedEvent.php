<?php declare(strict_types=1);
/**
 * @see https://github.com/klaviyo/php-klaviyo
 */

namespace App\MessageHandler;

use App\Entity\User;
use App\HandleTrait;
use App\Message\UserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Klaviyo\Klaviyo;
use Klaviyo\Model\EventModel as KlaviyoEvent;

final class SendKlaviyoEventOnUserCreatedEvent implements EventHandlerInterface
{
    use HandleTrait;

    protected $manager;
    protected $stopwatch;
    protected $logger;
    protected $klaviyo;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $queryBus, LoggerInterface $logger, Klaviyo $klaviyo)
    {
        $this->manager   = $manager;
        $this->logger    = $logger;
        $this->klaviyo   = $klaviyo;
        $this->stopwatch = new Stopwatch(true);
        $this->setQueryBus($queryBus);
    }

    public function __invoke(UserCreatedEvent $message)
    {
        $this->stopwatch->start(__FUNCTION__, __CLASS__);
        $appEnv = getenv('APP_ENV');
        if ('prod' != $appEnv) {
            // Only do this in a prod environment
            return;
        }

        $user = $this->manager->getRepository(User::class)->find($message->getPayloadValue('id'));

        $klaviyoEvent = new KlaviyoEvent([
            'event'               => 'Registered',
            'customer_properties' => [
                '$email' => $user->getEmail(),
            ],
            'properties' => [],
            'time' => strtotime($message->getMetadataValue('timestamp')),
        ]);
        $this->klaviyo->publicAPI->track($klaviyoEvent);

        $stopwatchEvent = $this->stopwatch->stop(__FUNCTION__);
        $this->logger->debug('Completed {category}::{name} in "{duration} (ms)" and used {memory}MB', [
            'category' => $stopwatchEvent->getCategory(),
            'name'     => $stopwatchEvent->getName(),
            'duration' => $stopwatchEvent->getDuration(),
            'memory'   => ($stopwatchEvent->getMemory() / 1024 / 1024),
        ]);
    }
}
