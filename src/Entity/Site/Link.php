<?php

namespace App\Entity\Site;

use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\Site\LinkRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[Vich\Uploadable]
class Link
{
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[Vich\UploadableField(mapping: 'site_link_images', fileNameProperty: 'pictoName')]
    private ?File $picto = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictoName = null;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPicto(): ?File
    {
        return $this->picto;
    }

    public function setPicto(?File $picto): static
    {
        $this->picto = $picto;

        $this->setUpdatedAt(new DateTime());

        return $this;
    }

    public function getPictoName(): ?string
    {
        return $this->pictoName;
    }

    public function setPictoName(?string $imageName): static
    {
        $this->pictoName = $imageName;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }
}
