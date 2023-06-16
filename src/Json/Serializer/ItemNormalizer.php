<?php

declare(strict_types=1);

namespace App\Json\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Entity\BaseApiEntity;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ItemNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public const FORMAT = 'json';

    /**
     * @var AbstractItemNormalizer
     */
    private $decorated;

    /**
     * @var IriConverterInterface
     */
    private $iriConverter;

    /**
     * @var string
     */
    private $apiVersion;

    public function __construct(
        AbstractItemNormalizer $decorated,
        IriConverterInterface $iriConverter,
        $apiVersion
    ) {
        $this->decorated = $decorated;
        $this->iriConverter = $iriConverter;
        $this->apiVersion = $apiVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return self::FORMAT === $format
            && $this->decorated->supportsNormalization($data, $format);
    }

    /**
     * This function is used to convert IRI output to valid UUID string.
     *
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        // Set timezone to UTC
        $context[DateTimeNormalizer::TIMEZONE_KEY] = 'UTC';

        $response = $this->decorated->normalize($object, $format, $context);
        if ($object instanceof BaseApiEntity) {
            if (\is_array($response)) {
                $object->normalize($response);
            }
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return self::FORMAT === $format
            && $this->decorated->supportsDenormalization($data, $type, $format);
    }

    /**
     * This function is used to convert IRI input to valid UUID string.
     *
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $object = new $type();
        if ($object instanceof BaseApiEntity) {
            $object->denormalize($data, $this->apiVersion);
        }

        if (!\array_key_exists('datetime_format', $context)) {
            $context['datetime_format'] = \DateTimeInterface::RFC3339;
        }

        return $this->decorated->denormalize($data, $type, $format, $context);
    }
}
