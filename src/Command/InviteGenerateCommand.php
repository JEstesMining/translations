<?php

namespace App\Command;

use App\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Messenger\MessageBusInterface;

class InviteGenerateCommand extends Command
{
    protected static $defaultName = 'app:invite:generate';
    protected static $defaultDescription = 'Generate Invite Codes for use with registration';

    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'How many invites to generate', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 0; $i < $input->getOption('count'); $i++) {
            // Use ulid as the random character set to use to generate the code
            $characterSet = (string) new Ulid();
            $code = '';
            for ($a = 0; $a < 8; $a++) { // 8 = How long the code is
                $code .= $characterSet[mt_rand(0, 25)]; // grab a random character
            }
            $payload = [
                'code' => $code,
            ];
            $metadata = [
                'timestamp' => (new \DateTime())->format('c'),
            ];
            $this->commandBus->dispatch(new Message\InviteGenerateCommand($payload, $metadata));
            $output->writeln(sprintf('Invite "%s" Generated', $payload['code']));
        }

        return Command::SUCCESS;
    }
}
