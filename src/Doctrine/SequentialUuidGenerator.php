<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class SequentialUuidGenerator extends AbstractIdGenerator
{
    /**
     * @var array
     */
    private static $counterList = [];

    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        $entityClassName = \get_class($entity);
        $entityCounter = $this->getEntityCounter($em, $entityClassName);
        $generatedId = $this->generateId($entityClassName, $entityCounter);
        $this->incrementEntityCounter($entityClassName);

        return $generatedId;
    }

    public function generateId($entityClassName, $counter)
    {
        $hash = \sha1($counter.$entityClassName);
        $uuidPartList = [
            \substr($hash, 0, 8),
            \substr($hash, 8, 4),
            \substr($hash, 12, 4),
            \substr($hash, 16, 4),
            \substr($hash, 20, 12),
        ];
        $generatedId = \implode('-', $uuidPartList);

        return $generatedId;
    }

    private function getEntityCounter(EntityManager $entityManager, $entityClassName)
    {
        if (!\array_key_exists($entityClassName, self::$counterList)) {
            $numberOfExistingEntities = \count($entityManager->getRepository($entityClassName)->findAll());
            self::$counterList[$entityClassName] = $numberOfExistingEntities + 1;
        }

        return self::$counterList[$entityClassName];
    }

    private function incrementEntityCounter($entityClassName)
    {
        ++self::$counterList[$entityClassName];
    }

    public static function reset()
    {
        self::$counterList = [];
    }
}
