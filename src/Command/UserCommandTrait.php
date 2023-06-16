<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

trait UserCommandTrait
{
    private $rolesList = [
        '1' => 'ROLE_READ_DEFAULT',
        '2' => 'ROLE_WHRITE_THIRD_PARTY',
        '3' => 'ROLE_WHRITE_FILM_RECORDS',
        '4' => 'ROLE_WHRITE_SENSITIVE',
        '5' => 'ROLE_WRITE_OBJECT',
        '6' => 'ROLE_ADMIN',
    ];

    /**
     * @param string $message
     * @param bool $notEmpty
     *
     * @return mixed
     */
    private function getInput($message, $notEmpty = true)
    {
        $question = new Question($message);

        $value = $this->helper->ask($this->input, $this->output, $question);

        while ($notEmpty && empty($value)) {
            $question = new Question($message);
            $value = $this->helper->ask($this->input, $this->output, $question);
        }

        return $value;
    }

    /**
     * @return User|null
     */
    private function findUserByUsernameOrID()
    {
        $usernameOrID = \trim($this->getInput('<info>Please enter the Username or the User ID: </info>'));

        $user = $this->em->getRepository(User::class)->find($usernameOrID);
        if (!$user) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $usernameOrID]);
        }

        if (!$user) {
            $this->writeNonSuccessMessage('User Not Found !');
        }

        return $user;
    }

    /**
     * @return array
     */
    private function getRolesFromKeys(string $roles)
    {
        $list = \explode(',', $roles);

        return \array_map(
            function ($key) {
                return $this->rolesList[$key];
            },
            $list
        );
    }

    private function isRolesListValid(string $roles): bool
    {
        return \preg_match('/^([1-6](,[1-6])*)$/', $roles);
    }

    private function writeSuccessMessage(string $message)
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $separator = \str_repeat(' ', \strlen($message) + 2);

        $io->newLine();
        $io->writeln(" <bg=green;fg=white>$separator</>");
        $io->writeln(\sprintf(' <bg=green;fg=white> %s </>', $message));
        $io->writeln(" <bg=green;fg=white>$separator</>");
        $io->newLine();
    }

    private function writeNonSuccessMessage(string $message)
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $separator = \str_repeat(' ', \strlen($message) + 2);

        $io->newLine();
        $io->writeln(" <bg=yellow;fg=white>$separator</>");
        $io->writeln(\sprintf(' <bg=yellow;fg=white> %s </>', $message));
        $io->writeln(" <bg=yellow;fg=white>$separator</>");
        $io->newLine();
    }
}
