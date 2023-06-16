<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateUserCommand extends Command
{
    use UserCommandTrait;

    protected static $defaultName = 'app:user-update';

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
            ->setDescription('Update an API User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $this->output->writeln(
            [
                'User Update',
                '============',
                '',
            ]
        );

        $this->helper = $this->getHelper('question');

        $io = new SymfonyStyle($this->input, $this->output);
        $io->note('This is Video API "User Update" Command [ PRESS *ENTER* TO KEEP THE ACTUAL VALUE ! ]');

        // find User
        $user = $this->findUserByUsernameOrID();
        if (null === $user) {
            return 0;
        }

        // update the Username
        $username = $this->getInput('<info>Please enter the Username: </info>', false);
        if (!empty($username)) {
            $user->setUsername($username);
        }

        // update the Password
        $plainPassword = $this->getInput('<info>Please enter the plain password: </info>', false);
        if (!empty($plainPassword)) {
            $encodedPwd = $this->passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPwd);
        }

        // update the Role(s)
        $output->writeln('<info>Roles:</info>');
        foreach ($this->rolesList as $key => $role) {
            $output->writeln("<info>$key: $role</info>");
        }
        $roles = $this->getInput('<info>Please enter the Role(s) comma separated (","): </info>', false);
        while (null !== $roles && !$this->isRolesListValid($roles)) {
            $roles = $this->getInput('<info>Please enter the Role(s) comma separated (","): </info>');
        }
        if (!empty($roles)) {
            $user->setRoles($this->getRolesFromKeys($roles));
        }

        // update the Description
        $description = $this->getInput('<info>Please enter the user Description: </info>', false);
        if (!empty($description)) {
            $user->setDescription($description);
        }

        $this->em->flush();

        $this->writeSuccessMessage('User Updated !');

        return 0;
    }
}
