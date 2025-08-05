<?php

namespace App\Serializer;

use App\Entity\Tenant;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

readonly class TenantNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')] private NormalizerInterface $normalizer, private StorageInterface    $storage
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Tenant $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['image'] = $this->storage->resolveUri($object, 'imageFile');

        $data['medias'] = array_values(array_filter(
            $object->getMedias()->map(function ($media) {
                return $this->storage->resolveUri($media, 'mediaFile');
            })->toArray()
        ));

        $data['node'] = $object->getNode()?->getId();
        $data['area'] = $object->getArea()?->getId();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Tenant;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Tenant::class => true,
        ];
    }
}