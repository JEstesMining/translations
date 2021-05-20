<?php

namespace App\MessageHandler;

use App\Message\UserCreateCommand;

final class UserCreateCommandHandler implements CommandHandlerInterface
{
    public function __invoke(UserCreateCommand $message)
    {
        // do something with your message
    }
}
