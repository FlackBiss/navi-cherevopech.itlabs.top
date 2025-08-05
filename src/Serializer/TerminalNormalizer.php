<?php

namespace App\Serializer;

use App\Entity\Terminal;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class TerminalNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Terminal $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['node'] = $object->getNode()?->getId();
        $data['area'] = $object->getArea()?->getId();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Terminal;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Terminal::class => true,
        ];
    }
}