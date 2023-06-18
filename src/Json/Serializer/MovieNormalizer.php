<?php

declare(strict_types=1);

namespace App\Json\Serializer;

use App\Entity\Movie;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MovieNormalizer implements  DenormalizerInterface
{
    private $itemNormalizer;

    public function __construct(ItemNormalizer $itemNormalizer)
    {
        $this->itemNormalizer = $itemNormalizer;
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
