<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'lograt:user:setpassword',
    description: 'Add a short description for your command',
)]
class UserPasswordCommand extends Command
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
            ->addArgument('password', InputArgument::REQUIRED, 'the new user password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('user');
        $password = $input->getArgument('password');

        $entityManager = $this->doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);

        if($user === null) {
            $io->warning('user not found');

            return Command::FAILURE;
        }

        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $user->setPassword(hash_hmac('sha256',$password,'0'));

        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('password set');

        return Command::SUCCESS;
    }
}
