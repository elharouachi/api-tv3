<?php

declare(strict_types=1);

namespace App\Json\Serializer;

use App\Entity\Movie;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class VideoNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $itemNormalizer;

    public function __construct(ItemNormalizer $itemNormalizer)
    {
        $this->itemNormalizer = $itemNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $this->itemNormalizer->supportsNormalization($data, $format)
            && ($data instanceof Movie);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {

    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $this->itemNormalizer->supportsDenormalization($data, $format)
            && ($data instanceof Movie);
    }
}
