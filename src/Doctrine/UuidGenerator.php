<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Id\UuidGenerator as DoctrineUuidGenerator;

class UuidGenerator extends AbstractIdGenerator
{
    private static $generatorClass = DoctrineUuidGenerator::class;

    /**
     * @var AbstractIdGenerator
     */
    private $generator;

    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        $generator = $this->getGenerator();

        return $generator->generate($em, $entity);
    }

    private function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new self::$generatorClass();
        }

        return $this->generator;
    }

    public static function useSequentialGenerator()
    {
        self::$generatorClass = SequentialUuidGenerator::class;
    }
}
