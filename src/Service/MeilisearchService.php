<?php

namespace App\Service;

use App\Entity\Tenant;
use App\Repository\TenantRepository;
use Meilisearch\Client;
use Symfony\Component\Serializer\SerializerInterface;

readonly class MeilisearchService
{
    private Client $client;

    private const string INDEX_NAME = 'tenants_1';

    public function __construct(
        private SerializerInterface $serializer,
        private TenantRepository $tenantRepository,
    ) {
        $this->client = new Client('http://127.0.0.1:7700');
    }

    private function configureIndexes(): void
    {
        $index = $this->client->index(self::INDEX_NAME);

        $index->updateSearchableAttributes([
            'title',
            'titleAsLatin',
            'titleAsCyrillic',
            'titleFromCyrillicKeyboard',
            'titleFromLatinKeyboard',
            'searchAliases',
        ]);

        $index->updateFilterableAttributes([
            'categoryId',
        ]);
    }

    public function indexTenants(): int
    {
        $index = $this->client->index(self::INDEX_NAME);
        $tenants = $this->tenantRepository->findAll();

        $docs = [];
        foreach ($tenants as $tenant) {
            $docs[] = json_decode(
                $this->serializer->serialize($tenant, 'json', ['groups' => ['tenant:read']]),
                true
            );
        }

        $index->addDocuments($docs);
        $this->configureIndexes();

        return count($docs);
    }

    public function indexTenant(Tenant $tenant): void
    {
        $index = $this->client->index(self::INDEX_NAME);

        $doc = json_decode(
            $this->serializer->serialize($tenant, 'json', ['groups' => ['tenant:read']]),
            true
        );

        $index->addDocuments([$doc]);
    }

    public function deleteTenantFromIndex(Tenant $tenant): void
    {
        $index = $this->client->index(self::INDEX_NAME);
        $index->deleteDocument($tenant->getId());
    }

    public function search(string $query, array $categoryIds = []): array
    {
        $tenantIndex = $this->client->index(self::INDEX_NAME);

        $params = [
            'q' => $query,
        ];

        if (!empty($categoryIds)) {
            $filter = array_map(fn($id) => "categoryId = $id", $categoryIds);
            $params['filter'] = implode(' OR ', $filter);
        }

        $tenantHits = $tenantIndex->search($query, $params)->getHits();

        return [
            'tenants' => $tenantHits,
        ];
    }
}
