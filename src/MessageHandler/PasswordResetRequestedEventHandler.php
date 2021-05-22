<?php

namespace App\MessageHandler;

use App\Message\PasswordResetRequestedEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PasswordResetRequestedEventHandler implements MessageHandlerInterface
{
    public function __invoke(PasswordResetRequestedEvent $message)
    {
        // do something with your message
    }
}
