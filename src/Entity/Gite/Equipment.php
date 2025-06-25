<?php

namespace App\Entity\Gite;

use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\Gite\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[Vich\Uploadable]
class Equipment
{
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'gite_equipment_images', fileNameProperty: 'pictoName')]
    private ?File $picto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictoName = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
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

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getPicto(): ?File
    {
        return $this->picto;
    }

    public function setPicto(?File $picto): static
    {
        $this->picto = $picto;

        return $this;
    }

    public function getPictoName(): ?string
    {
        return $this->pictoName;
    }

    public function setPictoName(?string $pictoName): static
    {
        $this->pictoName = $pictoName;

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
}
