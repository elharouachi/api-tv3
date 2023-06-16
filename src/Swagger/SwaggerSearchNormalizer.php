<?php

namespace App\Swagger;

use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerSearchNormalizer implements NormalizerInterface
{
    private const UNKNOWN_TYPE = 'unknown';
    private const ELASTIC_TYPE_TO_OPENAPI_TYPE = [
        'date' => 'string',
        'boolean' => 'boolean',
        'integer' => 'integer',
        'keyword' => 'string',
        'text' => 'string',
    ];
    private const ELASTIC_TYPE_TO_OPENAPI_FORMAT = [
        'date' => 'date-time',
        'keyword' => 'exact match',
        'index_keyword' => 'partial match',
    ];

    /**
     * @var NormalizerInterface
     */
    private $decorated;



    /**
     * @var IriConverter
     */
    private $iriConverter;

    public function __construct(NormalizerInterface $decorated, IriConverter $iriConverter)
    {
        $this->decorated = $decorated;

        $this->iriConverter = $iriConverter;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

       /* \array_unshift(
            $docs['tags'],
            [
                'name' => 'Search',
                'description' => \file_get_contents(
                    \sprintf('%s/Resources/documentation/search.md', \dirname(__DIR__))
                ),
            ]
        );*/

        return $docs;
    }

    private function addSearchDefinition(array &$docs, string $entityName, string $entityEndpoint, string $searchDefinitionName): void
    {
        $readDefinitionRef = $docs['paths'][$entityEndpoint]['get']['responses'][200]['schema']['items']['$ref'] ?? null;

        if (null === $readDefinitionRef) {
            return;
        }

        $docs['definitions'][$searchDefinitionName] = [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'description' => \sprintf('List of matching %s resources', $entityName),
                    '$ref' => $readDefinitionRef,
                ],
                'totalItems' => [
                    'type' => 'integer',
                    'description' => 'Total number of items',
                ],
                'totalPages' => [
                    'type' => 'integer',
                    'description' => 'Total number of pages',
                ],
            ],
        ];
    }

    private function addSearchPath(
        array &$docs,
        TypeConfig $typeConfiguration,
        string $entityName,
        string $entityEndpoint,
        string $searchDefinitionName
    ): void {
        $baseEndpoint = \basename($entityEndpoint);
        $endpoint = \sprintf('/v1/api/search/%s', $baseEndpoint);

        $searchDefinitionRef = \sprintf('#/definitions/%s', $searchDefinitionName);
        $pathDefinition = [
            'tags' => [$entityName],
            'consumes' => ['application/json'],
            'produces' => ['application/json'],
            'summary' => \sprintf('Searches in the collection of %s resources.', $entityName),
            'responses' => [
                200 => [
                    'description' => 'Search result',
                    'schema' => ['$ref' => $searchDefinitionRef],
                ],
                400 => ['description' => 'Invalid input'],
            ],
        ];
        $pathDefinitionPost = $pathDefinition;
        $pathDefinitionGet = $pathDefinition;
        $pathDefinitionPost['parameters'] = [
            [
                'in' => 'body',
                'schema' => [
                    'type' => 'object',
                    'properties' => $this->getSchemaFromMapping($typeConfiguration->getMapping()),
                ],
            ],
        ];
        $pathDefinitionGet['parameters'] = [
            [
                'in' => 'query',
                'name' => 'query',
                'required' => true,
                'description' => 'The search query body. See the POST search request to know available fields.',
                'type' => 'string',
            ],
        ];

        $docs['paths'][$endpoint]['post'] = $pathDefinitionPost;
        $docs['paths'][$endpoint]['get'] = $pathDefinitionGet;
    }

    private function getSchemaFromMapping(array $mapping, array &$schema = [], string $propertiesPrefix = ''): array
    {
        foreach ($mapping['properties'] as $propertyName => $propertyConfiguration) {
            $fullPropertyName = $propertiesPrefix.$propertyName;

            if ('nested' === $propertyConfiguration['type']) {
                $newPropertiesPrefix = $propertiesPrefix.$propertyName.'.';
                $this->getSchemaFromMapping($propertyConfiguration, $schema, $newPropertiesPrefix);
            }
        }

        \ksort($schema);

        return $schema;
    }

    private function getOpenApiTypeFromElasticType(string $elasticType, ?string $elasticAnalyzer): array
    {
        $propertyDefinition = ['type' => self::ELASTIC_TYPE_TO_OPENAPI_TYPE[$elasticType] ?? self::UNKNOWN_TYPE];

        if (\array_key_exists($elasticAnalyzer, self::ELASTIC_TYPE_TO_OPENAPI_FORMAT)) {
            $propertyDefinition['format'] = self::ELASTIC_TYPE_TO_OPENAPI_FORMAT[$elasticAnalyzer];
        } elseif (\array_key_exists($elasticType, self::ELASTIC_TYPE_TO_OPENAPI_FORMAT)) {
            $propertyDefinition['format'] = self::ELASTIC_TYPE_TO_OPENAPI_FORMAT[$elasticType];
        }

        return $propertyDefinition;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
