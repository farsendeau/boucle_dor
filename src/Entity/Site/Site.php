<?php

namespace App\Entity\Site;

use App\Repository\Site\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[Vich\Uploadable]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = '';

    #[ORM\Column(length: 255)]
    private ?string $address = '';

    #[ORM\Column(length: 8)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 32)]
    private ?string $city = '';

    #[ORM\Column(length: 16)]
    private ?string $country = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mapUrl = null;

    #[Vich\UploadableField(mapping: 'site_map_image', fileNameProperty: 'mapImageName')]
    private ?File $mapImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mapImageName = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'site', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $links;

    public function __construct()
    {
        $this->links = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getMapUrl(): ?string
    {
        return $this->mapUrl;
    }

    public function setMapUrl(string $mapUrl): static
    {
        $this->mapUrl = $mapUrl;

        return $this;
    }

    public function getMapImage(): ?File
    {
        return $this->mapImage;
    }

    public function setMapImage(?File $mapImage): static
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setSite($this);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        if ($this->links->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getSite() === $this) {
                $link->setSite(null);
            }
        }

        return $this;
    }

    public function getMapImageName(): ?string
    {
        return $this->mapImageName;
    }

    public function setMapImageName(?string $mapImageName): static
    {
        $this->mapImageName = $mapImageName;

        return $this;
    }
}
