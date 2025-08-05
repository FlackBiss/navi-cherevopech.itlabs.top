<?php

namespace App\Serializer;

use App\Entity\Area;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AreaNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Area $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['floor'] = $object->getFloor()?->getId();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Area;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Area::class => true,
        ];
    }
}