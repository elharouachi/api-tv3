<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    use UserCommandTrait;

    protected static $defaultName = 'app:user-create';

    private $helper;
    private $output;
    private $input;
    private $passwordEncoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create an API User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $this->output->writeln(
            [
                'User Create',
                '============',
                '',
            ]
        );

        $this->helper = $this->getHelper('question');

        $io = new SymfonyStyle($this->input, $this->output);
        $io->note('This is Video API "User Creation" Command');

        $user = new User();

        // get the Username
        $username = $this->getInput('<info>Please enter the Username: </info>');
        $user->setUsername($username);

        // get the Password
        $plainPassword = $this->getInput('<info>Please enter the plain password: </info>');
        $encodedPwd = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPwd);

        // get the Role(s)
        $roles = '';
        $output->writeln('<info>Roles:</info>');
        foreach ($this->rolesList as $key => $role) {
            $output->writeln("<info>$key: $role</info>");
        }
        while (!$this->isRolesListValid($roles)) {
            $roles = $this->getInput('<info>Please enter the Role(s) comma separated (","): </info>');
        }
        $user->setRoles($this->getRolesFromKeys($roles));

        // get the Description
        $description = $this->getInput('<info>Please enter the user description: </info>');
        $user->setDescription($description);

        $this->em->persist($user);
        $this->em->flush();

        $this->writeSuccessMessage('User Created !');

        return 0;
    }
}
