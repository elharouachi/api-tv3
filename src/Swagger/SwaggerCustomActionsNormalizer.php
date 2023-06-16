<?php

namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerCustomActionsNormalizer implements NormalizerInterface
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
        $docs['paths'] = $docs['paths']->getArrayCopy();
        $docs['definitions'] = $docs['definitions']->getArrayCopy();

        return $docs;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
