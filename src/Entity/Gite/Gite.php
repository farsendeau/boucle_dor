<?php

namespace App\Entity\Gite;

use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\Gite\GiteRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GiteRepository::class)]
#[Vich\Uploadable]
class Gite
{
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'gite_background_images', fileNameProperty: 'backgroundImageName')]
    private ?File $backgroundImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backgroundImageName = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $onHomepage = false;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'gite', cascade: ['persist', 'remove'] ,orphanRemoval: true)]
    private Collection $equipments;

    /**
     * @var Collection<int, GiteImage>
     */
    #[ORM\OneToMany(targetEntity: GiteImage::class, mappedBy: 'gite', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $giteImages;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
        $this->giteImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

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

    public function getBackgroundImage(): ?File
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?File $backgroundImage): static
    {
        $this->backgroundImage = $backgroundImage;

        $this->setUpdatedAt(new DateTimeImmutable());

        return $this;
    }

    public function getBackgroundImageName(): ?string
    {
        return $this->backgroundImageName;
    }

    public function setBackgroundImageName(?string $backgroundImageName): static
    {
        $this->backgroundImageName = $backgroundImageName;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
            $equipment->setGite($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipments->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getGite() === $this) {
                $equipment->setGite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GiteImage>
     */
    public function getGiteImages(): Collection
    {
        return $this->giteImages;
    }

    public function addGiteImage(GiteImage $giteImage): static
    {
        if (!$this->giteImages->contains($giteImage)) {
            $this->giteImages->add($giteImage);
            $giteImage->setGite($this);
        }

        return $this;
    }

    public function removeGiteImage(GiteImage $giteImage): static
    {
        if ($this->giteImages->removeElement($giteImage)) {
            // set the owning side to null (unless already changed)
            if ($giteImage->getGite() === $this) {
                $giteImage->setGite(null);
            }
        }

        return $this;
    }

    public function isOnHomepage(): bool
    {
        return $this->onHomepage;
    }

    public function setOnHomepage(bool $onHomepage): static
    {
        $this->onHomepage = $onHomepage;

        return $this;
    }
}
