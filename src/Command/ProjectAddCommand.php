<?php

namespace App\Command;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'lograt:project:add',
    description: 'Add a new project',
)]
class ProjectAddCommand extends Command
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
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the new Project')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $entityManager = $this->doctrine->getManager();

        $project = new Project();
        $project->setName($name);
        $project->setCreatedAt(new \DateTimeImmutable('now'));
        $project->setUpdatedAt(new \DateTimeImmutable('now'));

        $entityManager->persist($project);
        $entityManager->flush();

        $io->success('a new project by the name '. $name .' has been created');

        return Command::SUCCESS;
    }
}
