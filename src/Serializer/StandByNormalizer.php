<?php

namespace App\Serializer;

use App\Entity\StandBy;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

readonly class StandByNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private StorageInterface    $storage
    )
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var StandBy $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['media'] = $this->storage->resolveUri($object, 'mediaFile');

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof StandBy;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            StandBy::class => true,
        ];
    }
}