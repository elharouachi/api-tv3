<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerAuthNormalizer implements NormalizerInterface
{
    private const LOGIN_URI = '/v1/auth';
    private const TAG_NAME = 'Authentication';

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
        \array_unshift($docs['tags'], ['name' => self::TAG_NAME]);

        $this->addLoginCheckDefinition($docs);
        $this->addAuthorizationHeaderToDefinitions($docs);

        return $docs;
    }

    private function addLoginCheckDefinition(array &$docs)
    {
        $authDefinition = [
            self::LOGIN_URI => [
                'post' => [
                    'tags' => [self::TAG_NAME],
                    'consumes' => ['application/json'],
                    'produces' => ['application/json'],
                    'summary' => 'Authenticates a user.',
                    'parameters' => [
                        [
                            'name' => 'credentials',
                            'in' => 'body',
                            'description' => 'Authentication credentials',
                            'schema' => ['$ref' => '#/definitions/Auth'],
                        ],
                    ],
                    'responses' => [
                        200 => [
                            'description' => 'Authentication response',
                            'schema' => ['$ref' => '#/definitions/Auth-response'],
                        ],
                        400 => ['description' => 'Invalid input'],
                        401 => ['description' => 'Bad credentials'],
                    ],
                ],
            ],
        ];
        $authRequestSchema = [
            'type' => 'object',
            'description' => 'Authentication credentials',
            'required' => ['username', 'password'],
            'properties' => [
                'username' => ['description' => 'Username', 'type' => 'string'],
                'password' => ['description' => 'Password', 'type' => 'string'],
            ],
        ];
        $authResponseSchema = [
            'type' => 'object',
            'description' => 'Authentication response',
            'required' => ['token'],
            'properties' => [
                'token' => [
                    'description' => 'Authentication token',
                    'type' => 'string',
                    'example' => 'RKHmOGbwluKopCwO-mcHlGHjlNEOp7b5FwgSvmx8ExO5BfeE3iiY5K7yE-e9Uq...',
                ],
                'tokenDuration' => [
                    'description' => 'Duration of the authentication token in seconds',
                    'type' => 'string',
                    'example' => 3600,
                ],
                'tokenExpirationDate' => [
                    'description' => 'Expiration date of the authentication token',
                    'type' => 'string',
                    'example' => '2019-02-16T09:14:36+00:00',
                ],
            ],
        ];

        $docs['paths'] = $authDefinition + $docs['paths'];
        $docs['definitions']['Auth'] = $authRequestSchema;
        $docs['definitions']['Auth-response'] = $authResponseSchema;
    }

    private function addAuthorizationHeaderToDefinitions(array &$docs)
    {
        $excludeUris = [self::LOGIN_URI];
        $authorizationDefinition = [
            'name' => 'Authorization',
            'in' => 'header',
            'description' => \sprintf('Bearer *{token}* (see ["%s"](#tag/%s) to know how to get a token)', self::TAG_NAME, self::TAG_NAME),
            'type' => 'string',
            'required' => true,
        ];

        foreach ($docs['paths'] as $uri => $methodList) {
            if (\in_array($uri, $excludeUris)) {
                continue;
            }

            foreach ($methodList as $method => $definition) {
                $docs['paths'][$uri][$method]['parameters'][] = $authorizationDefinition;
            }
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
