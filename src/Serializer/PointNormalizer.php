<?php

namespace App\Serializer;

use App\Entity\Point;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class PointNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Point $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['floor'] = $object->getFloor()?->getId();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Point;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Point::class => true,
        ];
    }
}