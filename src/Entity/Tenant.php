<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\SearchController;
use App\Controller\Tenant\TenantUpdateController;
use App\Dto\TenantDto;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\TenantRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Patch(
            controller: TenantUpdateController::class,
            input: TenantDto::class
        ),
        new GetCollection(
            uriTemplate: '/tenants/search',
            controller: SearchController::class,
            openapi: new Model\Operation(
                parameters: [
                    new Parameter(
                        name: 'q',
                        in: 'query',
                        description: 'Search query string',
                        required: true,
                        schema: ['type' => 'string']
                    ),
                ],
            ),
            output: false,
            read: false,
            name: 'search',
        ),
        new Get(),
    ],
    normalizationContext: ['groups' => ['tenant:read']],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['floor:read', 'tenant:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['floor:read', 'tenant:read', 'category:read'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'tenants')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Area $area = null;

    /**
     * @var Collection<int, TenantMedia>
     */
    #[ORM\OneToMany(targetEntity: TenantMedia::class, mappedBy: 'tenant', cascade: ['all'])]
    #[Groups(['floor:read', 'tenant:read', 'category:read'])]
    private Collection $medias;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['floor:read', 'tenant:read', 'category:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['tenant:read'])]
    private ?array $searchAliases = [];

    #[ORM\ManyToOne(inversedBy: 'tenants')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Node $node = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $link = null;

    #[Vich\UploadableField(mapping: 'tenant_images', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'tenants')]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?Category $category = null;

    /**
     * @var Collection<int, Queries>
     */
    #[ORM\OneToMany(targetEntity: Queries::class, mappedBy: 'tenant')]
    private Collection $queries;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->queries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection<int, TenantMedia>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(TenantMedia $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setTenant($this);
        }

        return $this;
    }

    public function removeMedia(TenantMedia $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getTenant() === $this) {
                $media->setTenant(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[Groups(['tenant:read'])]
    public function getTitleAsLatin(): ?string
    {
        return $this->transliterate($this->getTitle() ?? '');
    }

    #[Groups(['tenant:read'])]
    public function getTitleAsCyrillic(): ?string
    {
        return $this->reverseTransliterate($this->getTitle() ?? '');
    }

    #[Groups(['tenant:read'])]
    public function getTitleFromCyrillicKeyboard(): ?string
    {
        return $this->convertLayoutToLatin($this->getTitle() ?? '');
    }

    #[Groups(['tenant:read'])]
    public function getTitleFromLatinKeyboard(): ?string
    {
        return $this->convertLayoutToCyrillic($this->getTitle() ?? '');
    }

    private function transliterate(string $text): string
    {
        $converter = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh',
            'з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o',
            'п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c',
            'ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya',
        ];

        $text = mb_strtolower($text);
        $result = '';
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);
            $result .= $converter[$char] ?? $char;
        }
        return $result;
    }

    private function reverseTransliterate(string $text): string
    {
        $converter = [
            'shch' => 'щ',
            'yo'   => 'ё',
            'zh'   => 'ж',
            'ch'   => 'ч',
            'sh'   => 'ш',
            'yu'   => 'ю',
            'ya'   => 'я',
            'a' => 'а','b' => 'б','v' => 'в','g' => 'г','d' => 'д',
            'e' => 'е','z' => 'з','i' => 'и','j' => 'й','k' => 'к',
            'l' => 'л','m' => 'м','n' => 'н','o' => 'о','p' => 'п',
            'r' => 'р','s' => 'с','t' => 'т','u' => 'у','f' => 'ф',
            'h' => 'х','c' => 'ц','y' => 'ы','э' => 'э'
        ];

        $text = mb_strtolower($text);
        $result = '';

        while ($text !== '') {
            $matched = false;
            foreach (array_keys($converter) as $latin) {
                if (str_starts_with($text, $latin)) {
                    $result .= $converter[$latin];
                    $text = mb_substr($text, mb_strlen($latin));
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $result .= mb_substr($text, 0, 1);
                $text = mb_substr($text, 1);
            }
        }

        return $result;
    }

    private function convertLayoutToLatin(string $input): string
    {
        $map = [
            'а'=>'f','б'=>'<','в'=>'d','г'=>'u','д'=>'l','е'=>'t','ё'=>'`','ж'=>';','з'=>'p','и'=>'b','й'=>'q',
            'к'=>'r','л'=>'k','м'=>'v','н'=>'y','о'=>'j','п'=>'g','р'=>'h','с'=>'c','т'=>'n','у'=>'e','ф'=>'a',
            'х'=>'[','ц'=>'w','ч'=>'x','ш'=>'i','щ'=>'o','ъ'=>']','ы'=>'s','ь'=>'m','э'=>'\'','ю'=>'.','я'=>'z',
            'А'=>'F','Б'=>'>','В'=>'D','Г'=>'U','Д'=>'L','Е'=>'T','Ё'=>'~','Ж'=>':','З'=>'P','И'=>'B','Й'=>'Q',
            'К'=>'R','Л'=>'K','М'=>'V','Н'=>'Y','О'=>'J','П'=>'G','Р'=>'H','С'=>'C','Т'=>'N','У'=>'E','Ф'=>'A',
            'Х'=>'{','Ц'=>'W','Ч'=>'X','Ш'=>'I','Щ'=>'O','Ъ'=>'}','Ы'=>'S','Ь'=>'M','Э'=>'"','Ю'=>'>','Я'=>'Z',
        ];

        return strtr($input, $map);
    }

    private function convertLayoutToCyrillic(string $input): string
    {
        $map = [
            'f' => 'а', '<' => 'б', 'd' => 'в', 'u' => 'г', 'l' => 'д', 't' => 'е', '`' => 'ё', ';' => 'ж',
            'p' => 'з', 'b' => 'и', 'q' => 'й', 'r' => 'к', 'k' => 'л', 'v' => 'м', 'y' => 'н', 'j' => 'о',
            'g' => 'п', 'h' => 'р', 'c' => 'с', 'n' => 'т', 'e' => 'у', 'a' => 'ф', '[' => 'х', 'w' => 'ц',
            'x' => 'ч', 'i' => 'ш', 'o' => 'щ', ']' => 'ъ', 's' => 'ы', 'm' => 'ь', '\'' => 'э', '.' => 'ю',
            'z' => 'я', '>' => 'ю'
        ];

        return strtr($input, $map);
    }

    public function getSearchAliases(): array
    {
        return $this->searchAliases ?? [];
    }

    public function setSearchAliases(array $searchAliases): static
    {
        $this->searchAliases = $searchAliases;
        return $this;
    }

    public function getNode(): ?Node
    {
        return $this->node;
    }

    public function setNode(?Node $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Queries>
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    public function addQuery(Queries $query): static
    {
        if (!$this->queries->contains($query)) {
            $this->queries->add($query);
            $query->setTenant($this);
        }

        return $this;
    }

    public function removeQuery(Queries $query): static
    {
        if ($this->queries->removeElement($query)) {
            // set the owning side to null (unless already changed)
            if ($query->getTenant() === $this) {
                $query->setTenant(null);
            }
        }

        return $this;
    }
}
