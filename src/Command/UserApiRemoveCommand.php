<?php

namespace App\Command;

use App\Entity\Apitoken;
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
    name: 'lograt:user:tokenremove',
    description: 'removes an api token from a user',
)]
class UserApiRemoveCommand extends Command
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
            ->addArgument('token', InputArgument::REQUIRED, 'the token to be deleted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tokenhash = $input->getArgument('token');

        $entityManager = $this->doctrine->getManager();

        $token = $entityManager->getRepository(Apitoken::class)->findOneBy(['token'=>$tokenhash]);

        if($token === null) {
            $io->warning('token not found');

            return Command::FAILURE;
        }



        $entityManager->remove($token);
        $entityManager->flush();

        $io->success('Apitoken has been removed');

        return Command::SUCCESS;
    }
}
