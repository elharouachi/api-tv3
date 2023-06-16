<?php

declare(strict_types=1);

namespace App\Entity;

abstract class BaseApiEntity
{
    abstract public function getId(): int;

    public function normalize(array &$data): void
    {
    }

    public function denormalize(array &$data, string $apiVersion): void
    {
    }

    // API Platform return it as a field in the JSON output if called "getNotifiableFields"
    public function returnNotifiableFields(): array
    {
        return [];
    }

    protected function handleMultipleIriConversion(
        array &$data,
        string $field,
        array $entityDataList,
        string $entityIriName,
        string $apiVersion
    ) {
        $result = [];

        foreach ($entityDataList as $entityData) {
            if (!empty($entityData['id'])) {
                $item = \sprintf('/%s/%s/%s', $apiVersion, $entityIriName, $entityData['id']);
            }
            $result[] = $item;
        }
        $data[$entityIriName] = $result;
    }

    protected function handleFlatMultipleIriConversion(
        array &$data,
        string $field,
        array $entityDataList,
        string $entityIriName,
        string $apiVersion
    ) {
        $this->handleMultipleIriConversion($data, $field, $entityDataList, $entityIriName, $apiVersion);

        $result = [];

        foreach ($data[$field] as $entityData) {
            if (!empty($entityData['id'])) {
                $result[] = $entityData['id'];
            }
        }

        $data[$field] = $result;
    }
}
