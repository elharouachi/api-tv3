<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListUsersCommand extends Command
{
    protected static $defaultName = 'app:user-list';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('List all API Users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            [
                'List all Users',
                '==============',
                '',
            ]
        );

        $users = $this->em->getRepository(User::class)->findAll();

        $outputStyle = new OutputFormatterStyle('yellow', null, ['bold']);
        $output->getFormatter()->setStyle('pres', $outputStyle);

        $dataOutput = \sprintf('%s<info>Number of Users:</info> %d%s%s', "\n", \count($users), "\n", "\n");
        foreach ($users as $key => $user) {
            /* @var User $user */
            $dataOutput .= \sprintf(
                '<pres>User:</pres> %d%s
            <pres>ID:</pres> %s
            <pres>Username:</pres> %s
            <pres>Roles:</pres> %s
            <pres>Description:</pres> %s
            <pres>Last Connection:</pres> %s
            <pres>Created At:</pres> %s%s',
                $key + 1,
                "\n",
                $user->getId(),
                $user->getUsername(),
                json_encode($user->getRoles()),
                $user->getDescription(),
                $user->getLastConnection() ? $user->getLastConnection()->format('Y-m-d\TH:i:sP') : '',
                $user->getCreatedAt()->format('Y-m-d\TH:i:sP'),
                "\n"
            );
        }

        $output->writeln($dataOutput);

        return 0;
    }
}
