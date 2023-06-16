<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerEntityDescriptionNormalizer implements NormalizerInterface
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
        // by default, entity description (taken from its PHPDoc) is not shown
        $docs = $this->decorated->normalize($object, $format, $context);
        $refList = [];

        foreach ($docs['paths'] as $pathDefinition) {
            $methodDefinition = \current($pathDefinition);
            $ref = $methodDefinition['responses']['200']['schema']['items']['$ref'] ?? null;

            if (null === $ref || 0 !== \strpos($ref, '#/definitions/')) {
                continue;
            }

            $ref = \substr($ref, 14);
            $refList[$ref] = null;
        }

        $refList = \array_keys($refList);

        foreach ($refList as $ref) {
            $entityName = \preg_replace('#-.*#', '', $ref);
            $entityDescription = $docs['definitions'][$ref]['description'] ?? null;

            $docs['tags'][] = ['name' => $entityName, 'description' => $entityDescription];
        }

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
