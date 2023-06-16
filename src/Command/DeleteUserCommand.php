<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteUserCommand extends Command
{
    use UserCommandTrait;

    protected static $defaultName = 'app:user-delete';

    private $helper;
    private $output;
    private $input;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete an API User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $this->output->writeln(
            [
                'User Delete',
                '============',
                '',
            ]
        );

        $this->helper = $this->getHelper('question');

        $io = new SymfonyStyle($this->input, $this->output);
        $io->note('This is Movie API "User Delete" Command');

        // find User
        $user = $this->findUserByUsernameOrID();
        if (null === $user) {
            return 0;
        }

        // Delete User
        $this->em->remove($user);
        $this->em->flush();

        $this->writeSuccessMessage('User Deleted !');

        return 0;
    }
}
