<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class BaseFixtures extends Fixture
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getFixturesDirectory(): string
    {
        $customPath = \getenv('FIXTURES_DIR');

        return $customPath ? $customPath : \sprintf('%s/../Resources/fixtures', __DIR__);
    }

    /**
     * @param string $fixturesFile
     *
     * @return array
     */
    protected function loadFixtures($fixturesFile)
    {
        $fixturesPath = \sprintf(
            '%s/%s.yaml',
            $this->getFixturesDirectory(),
            \substr(\strrchr($fixturesFile, '\\'), 1)
        );

        if (!\file_exists($fixturesPath)) {
            return [];
        }

        return Yaml::parseFile($fixturesPath);
    }

    /**
     * @param string $date
     *
     * @return \DateTime
     */
    protected function getDate($date)
    {
        return \DateTime::createFromFormat('d-m-Y H:i:s', $date);
    }

    /**
     * @param object $entity
     */
    protected function disableEntityIdGenerator($entity)
    {
        $metadata = $this->entityManager->getClassMetaData(\get_class($entity));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
    }
}
