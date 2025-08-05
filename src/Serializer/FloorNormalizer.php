<?php

namespace App\Serializer;

use App\Entity\Floor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

readonly class FloorNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private StorageInterface    $storage
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Floor $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['mapImage'] = $this->storage->resolveUri($object, 'mapImageFile');

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Floor;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Floor::class => true,
        ];
    }
}