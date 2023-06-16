<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerPropertiesNormalizer implements NormalizerInterface
{

    /**
     * @var NormalizerInterface
     */
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $this->updateNestedObjectDefinitions($docs);
        $this->modifyPutSummaries($docs);

        return $docs;
    }

    private function updateNestedObjectDefinitions(array &$docs): void
    {
        foreach ($docs['definitions'] as $definitionName => $definition) {
            if (!\array_key_exists('properties', $definition)) {
                continue;
            }

            foreach ($definition['properties'] as $propertyName => $property) {
                $property = \is_object($property) ? $property->getArrayCopy() : $property;
                $propertyRef = $this->getPropertyRef($property);

                if (empty($propertyRef)) {
                    continue;
                }

                $targetDefinitionName = \substr($propertyRef, 14);

                if (!\array_key_exists($targetDefinitionName, $docs['definitions'])) {
                    continue;
                }

                $targetDefinition = &$docs['definitions'][$targetDefinitionName];

                if (\array_key_exists('description', $property)) {
                    // override description for this field
                    // by default, it is the description of the target entity
                    $targetDefinition['description'] = $property['description'];
                }

                if (
                    $this->isWriteDefinitionName($targetDefinitionName)
                    && $this->isNestedObjectThatNeedId($targetDefinitionName, $targetDefinition->getArrayCopy())
                ) {
                    // add an "id" property on nested objects for POST/PUT methods
                    $idProperty = [
                        'id' => [
                            'type' => 'string',
                            'description' => 'The entity Id (if ommited, a new entity will be created)',
                            'example' => '01234567-890a-bcde-f0123456789abcdef',
                        ],
                    ];

                    if (!\array_key_exists('properties', $targetDefinition)) {
                        $targetDefinition['properties'] = [];
                    }

                    $targetDefinition['properties'] = $idProperty + $targetDefinition['properties'];
                }
            }
        }
    }

    private function getPropertyRef(array $propertyDefinition): ?string
    {
        if (\array_key_exists('items', $propertyDefinition)) {
            $propertyDefinition = $propertyDefinition['items'];
        }

        return \array_key_exists('$ref', $propertyDefinition) && 0 === \strpos($propertyDefinition['$ref'], '#/definitions/')
            ? $propertyDefinition['$ref']
            : null;
    }

    private function isWriteDefinitionName(string $name): bool
    {
        return \strpos($name, '-write_') > 0;
    }

    private function isNestedObjectThatNeedId(string $definitionName, array $definition): bool
    {
        if ('object' !== $definition['type']) {
            return false;
        } elseif (\array_key_exists('properties', $definition) && \array_key_exists('id', $definition['properties'])) {
            return false;
        }

        return true;
    }

    private function modifyPutSummaries(array &$docs): void
    {
        foreach ($docs['paths'] as $url => $definition) {
            if (!\array_key_exists('put', $definition)) {
                continue;
            }

            $docs['paths'][$url]['put']['summary'] = \str_replace('Replaces the', 'Updates the', $definition['put']['summary']);
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
