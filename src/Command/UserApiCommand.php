<?php

namespace App\Command;

use App\Entity\Apitoken;
use App\Entity\Project;
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
    name: 'lograt:user:tokenadd',
    description: 'adds a new api token for a user',
)]
class UserApiCommand extends Command
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('user');

        $entityManager = $this->doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);

        if($user === null) {
            $io->warning('user not found');

            return Command::FAILURE;
        }

        $token = new Apitoken();
        $token->setToken(hash_hmac('sha256',(string) rand(1,1000000000000),$username));
        $token->setUsers($user);
        $token->setCreatedAt(new \DateTimeImmutable('now'));
        $token->setUpdatedAt(new \DateTimeImmutable('now'));

        $entityManager->persist($token);
        $entityManager->flush();

        $io->success("a new Api Token was generated \n\n\n".$token->getToken());

        return Command::SUCCESS;
    }
}
