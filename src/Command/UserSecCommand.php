<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'lograt:user:changeseclvl',
    description: 'changes the users security level',
)]
class UserSecCommand extends Command
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
            ->addArgument('user', InputArgument::REQUIRED, 'the name of the User')
            ->addArgument('seclvl', InputArgument::REQUIRED, 'the new users security level')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('user');
        $sec = $input->getArgument('seclvl');

        $entityManager = $this->doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);

        if($user === null) {
            $io->warning('user not found');

            return Command::FAILURE;
        }
        if($sec > 4 ||$sec < 0) {
            $io->warning('security level out of bounds');

            return Command::FAILURE;
        }

        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $user->setSecurityLevel($sec);

        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('security level changed');

        return Command::SUCCESS;
    }
}
