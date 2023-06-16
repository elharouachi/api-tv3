<?php

namespace App\Command;

use App\Entity\Movie;
use App\Http\IMDB\ImdbApiRequester;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportMissingMoviePosterCommand extends Command
{
    private const MOVIES_PER_PAGE = 100;

    protected static $defaultName = 'app:import-missing-movie-poster';
    private $imdbApiRequester;

    private $logger;
    private $entityManager;

    public function __construct(
        LoggerInterface $logger,
        ImdbApiRequester $imdbApiRequester,
        EntityManagerInterface $entityManager
    ) {
        $this->imdbApiRequester = $imdbApiRequester;
        $this->entityManager = $entityManager;

        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Retrieve missing poster movie from move label');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        /** @var MovieRepository $movieRepository */
        $movieRepository = $this->entityManager->getRepository(Movie::class);
        $movieCount = $movieRepository->getMovieCount();
        $io = new SymfonyStyle($input, $output);
        $io->progressStart($movieCount);
        $offset = 0;

        while (true) {
            $this->entityManager->clear();
            $movieList = $movieRepository->getMoviesNosHasPosters($offset, self::MOVIES_PER_PAGE);

            $offset += self::MOVIES_PER_PAGE;

            /** @var Movie $movie */
            foreach ($movieList as $movie) {
                $title = $movie->getTitle();
                $reponse = $this->imdbApiRequester->getMovieDetail(trim($title));

                if ($reponse) {
                    foreach ($reponse as $item) {
                        if (!empty($item['image']['url'])) {
                            $movie->setPoster($item['image']['url']);
                            break;
                        }
                    }
                }

                if ($title) {
                    --$offset;
                }
            }

            $this->entityManager->flush();
            $io->progressAdvance(count($movieList));

            if (0 === count($movieList)) {
                break;
            }
        }

        $this->entityManager->flush();
        $io->progressFinish();

        return 0;
    }
}
