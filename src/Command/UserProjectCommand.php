<?php

namespace App\Command;

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
    name: 'lograt:user:addproject',
    description: 'add a new project to the user',
)]
class UserProjectCommand extends Command
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
            ->addArgument('project', InputArgument::REQUIRED, 'project')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('user');
        $projectname = $input->getArgument('project');

        $entityManager = $this->doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);
        $project = $entityManager->getRepository(Project::class)->findOneBy(['name'=>$projectname]);

        if($user === null) {
            $io->warning('user not found');

            return Command::FAILURE;
        }

        if($project === null) {
            $io->warning('project not found');

            return Command::FAILURE;
        }

        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $user->addProject($project);

        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('project added to user');

        return Command::SUCCESS;
    }
}
