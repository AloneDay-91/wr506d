<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['actor:list']]
        ),
        new Get(
            normalizationContext: ['groups' => ['actor:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['actor:read']],
            denormalizationContext: ['groups' => ['actor:write']]
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            normalizationContext: ['groups' => ['actor:read']],
            denormalizationContext: ['groups' => ['actor:write']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            normalizationContext: ['groups' => ['actor:read']],
            denormalizationContext: ['groups' => ['actor:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(SearchFilter::class, properties:
    ['lastname' => 'start', 'firstname' => 'start', 'bio' => 'partial', 'photo' => 'partial'])]
#[ApiFilter(DateFilter::class, properties: ['dob','dof'])]

class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['actor:list', 'actor:read', 'movie:list', 'movie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string', message: 'The name must be a string.')]
    #[Groups(['actor:read', 'actor:write'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Type(type: 'string', message: 'The firstname must be a string.')]
    #[Groups(['actor:read', 'actor:write'])]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['actor:read', 'actor:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $dob = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['actor:read', 'actor:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTime $dod = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Type(type: 'string', message: 'The bio must be a string.')]
    #[Groups(['actor:read', 'actor:write'])]
    private ?string $bio = null;

   /* #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Type(type: 'string', message: 'The photo must be a string.')]
    private ?string $photo = null;*/

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'actors')]
    #[Groups(['actor:read'])]
    private Collection $movies;

    #[ORM\Column]
    #[Groups(['actor:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'actors')]
    #[Groups(['actor:read', 'actor:write'])]
    private ?MediaObject $photo = null;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getDob(): ?\DateTime
    {
        return $this->dob;
    }

    public function setDob(?\DateTime $dob): static
    {
        $this->dob = $dob;

        return $this;
    }

    public function getDod(): ?\DateTime
    {
        return $this->dod;
    }

    public function setDod(?\DateTime $dod): static
    {
        $this->dod = $dod;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        $this->movies->removeElement($movie);

        return $this;
    }

    public function getPhoto(): ?MediaObject
    {
        return $this->photo;
    }

    public function setPhoto(?MediaObject $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Nom complet (virtuel)
     */
    #[Groups(['actor:list', 'actor:read', 'movie:list', 'movie:read'])]
    public function getFullName(): string
    {
        return trim($this->lastname . ' ' . $this->firstname);
    }

    /**
     * Âge calculé (virtuel)
     */
    #[Groups(['actor:list'])]
    public function getAge(): ?int
    {
        if ($this->dob === null) {
            return null;
        }
        // Si décédé, calcule l'âge au moment du décès
        $reference = $this->dod ?? new \DateTime();

        return $this->dob->diff($reference)->y;
    }

    /**
     * Indique si l'acteur est décédé (virtuel)
     */
    #[Groups(['actor:list'])]
    public function getIsDead(): bool
    {
        return $this->dod !== null;
    }
}
