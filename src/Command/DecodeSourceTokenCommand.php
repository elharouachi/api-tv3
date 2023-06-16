<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecodeSourceTokenCommand extends Command
{
    protected static $defaultName = 'app:source-token:decode';

    protected function configure()
    {
        $this
            ->setDescription('Decodes a source token')
            ->addArgument('source-token', InputArgument::REQUIRED, 'The source token to decode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceToken = $input->getArgument('source-token');
        dump(
            json_decode(base64_decode($sourceToken), true)
        );

        return 0;
    }
}
