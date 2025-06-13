<?php

namespace App\Entity\Gite;

use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\Gite\GiteImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GiteImageRepository::class)]
#[Vich\Uploadable]
class GiteImage
{
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[Vich\UploadableField(mapping: 'gite_images', fileNameProperty: 'name')]
    private ?File $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'giteImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gite $gite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGite(): ?Gite
    {
        return $this->gite;
    }

    public function setGite(?Gite $gite): static
    {
        $this->gite = $gite;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): static
    {
        $this->image = $image;

        $this->setUpdatedAt(new DateTimeImmutable());

        return $this;
    }
}
