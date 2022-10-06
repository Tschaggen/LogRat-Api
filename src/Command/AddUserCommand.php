<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use LogRat\Core\Service\UserHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'lograt:user:add',
    description: 'Creates a new User',
)]
class AddUserCommand extends Command
{
    private $doctrine;

    public function __construct(ManagerRegistry $entityManager)
    {
        $this->doctrine = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the new User')
            ->addArgument('security_level', InputArgument::OPTIONAL, 'Access Level of The new User')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $security_level = $input->getArgument('security_level');

        $entityManager = $this->doctrine->getManager();

        $user = new User();
        $user->setUsername($name);
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

        if ($security_level) {
            $user->setSecurityLevel($security_level);
        }
        else {
            $user->setSecurityLevel(UserHandler::SECURITY_LEVEL_NONE);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('a new user by the name '. $name .' has been created');

        return Command::SUCCESS;
    }
}
