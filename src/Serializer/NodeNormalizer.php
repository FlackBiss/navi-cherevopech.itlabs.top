<?php

namespace App\Serializer;

use App\Entity\Node;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class NodeNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Node $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $nodes = [];

        foreach ($object->getNodes() as $node) {
            $nodes[] = $node->getId();
        }

        $data['nodes'] = $nodes;

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Node;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Node::class => true,
        ];
    }
}