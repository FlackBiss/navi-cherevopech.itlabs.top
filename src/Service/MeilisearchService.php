<?php

namespace App\Service;

use App\Entity\Infrastructure;
use App\Repository\CategoryRepository;
use App\Repository\InfrastructureRepository;
use App\Repository\TenantRepository;
use Meilisearch\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Tenant;
use App\Entity\Category;

readonly class MeilisearchService
{
    private Client $client;
    public function __construct(
        private SerializerInterface    $serializer,
        private TenantRepository       $tenantRepository,
        private CategoryRepository     $categoryRepository,
    ) {
        $this->client = new Client('http://127.0.0.1:7700');
    }

    private function configureIndexes(): void
    {
        $this->client->index('tenants')->updateSearchableAttributes([
            'title',
            'titleAsLatin',
            'titleAsCyrillic',
            'titleFromCyrillicKeyboard',
            'titleFromLatinKeyboard',
            'searchAliases',
        ]);

        $this->client->index('categories')->updateSearchableAttributes([
            'title',
            'titleAsLatin',
            'titleAsCyrillic',
            'titleFromCyrillicKeyboard',
            'titleFromLatinKeyboard',
            'searchAliases',
        ]);

        $this->client->index('infrastructures')->updateSearchableAttributes([
            'title',
            'titleAsLatin',
            'titleAsCyrillic',
            'titleFromCyrillicKeyboard',
            'titleFromLatinKeyboard',
            'searchAliases',
        ]);
    }

    public function indexTenants(): int
    {
        $index = $this->client->index('tenants');
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

    public function indexCategories(): int
    {
        $index = $this->client->index('categories');
        $categories = $this->categoryRepository->findAll();

        $docs = [];
        foreach ($categories as $category) {
            $docs[] = json_decode(
                $this->serializer->serialize($category, 'json', ['groups' => ['category:read']]),
                true
            );
        }

        $index->addDocuments($docs);
        $this->configureIndexes();

        return count($docs);
    }

    public function search(string $query): array
    {
        $tenantIndex = $this->client->index('tenants');
        $categoryIndex = $this->client->index('categories');
        $infrastructureIndex = $this->client->index('infrastructures');

        $tenantHits = $tenantIndex->search($query)->getHits();
        $tenantIds = array_column($tenantHits, 'id');

        $tenants = $this->tenantRepository->findBy(['id' => $tenantIds]);

        $tenantsById = [];
        foreach ($tenants as $tenant) {
            $tenantsById[$tenant->getId()] = $tenant;
        }

        foreach ($tenantHits as &$hit) {
            $id = $hit['id'];
            if (isset($tenantsById[$id])) {
                $hit['work'] = $tenantsById[$id]->getWork();
            }
        }

        $categoryHits = $categoryIndex->search($query)->getHits();

        $infrastructureHits = $infrastructureIndex->search($query)->getHits();

        return [
            'tenants' => $tenantHits,
            'categories' => $categoryHits,
            'infrastructures' => $infrastructureHits,
        ];
    }


    public function indexTenant(Tenant $tenant): void
    {
        $index = $this->client->index('tenants');

        $doc = json_decode(
            $this->serializer->serialize($tenant, 'json', ['groups' => ['tenant:read']]),
            true
        );

        $index->addDocuments([$doc]);
    }

    public function deleteTenantFromIndex(Tenant $tenant): void
    {
        $index = $this->client->index('tenants');
        $index->deleteDocument($tenant->getId());
    }

    public function indexCategory(Category $category): void
    {
        $index = $this->client->index('categories');

        $doc = json_decode(
            $this->serializer->serialize($category, 'json', ['groups' => ['category:read']]),
            true
        );

        $index->addDocuments([$doc]);
    }

    public function deleteCategoryFromIndex(Category $category): void
    {
        $index = $this->client->index('categories');
        $index->deleteDocument($category->getId());
    }
}
