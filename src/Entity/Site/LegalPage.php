<?php

namespace App\Entity\Site;

use App\Repository\Site\LegalPageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LegalPageRepository::class)]
class LegalPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aboutTitleFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aboutTitleEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutContentFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutContentEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gcsTitleFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gcsTitleEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $gcsContentFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $gcsContentEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legalNoticeTitleFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legalNoticeTitleEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $legalNoticeContentFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $legalNoticeContentEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personalDataTitleFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personalDataTitleEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $personalDataContentFr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $personalDataContentEn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAboutTitleFr(): ?string
    {
        return $this->aboutTitleFr;
    }

    public function setAboutTitleFr(?string $aboutTitleFr): void
    {
        $this->aboutTitleFr = $aboutTitleFr;
    }

    public function getAboutTitleEn(): ?string
    {
        return $this->aboutTitleEn;
    }

    public function setAboutTitleEn(?string $aboutTitleEn): void
    {
        $this->aboutTitleEn = $aboutTitleEn;
    }

    public function getAboutContentFr(): ?string
    {
        return $this->aboutContentFr;
    }

    public function setAboutContentFr(?string $aboutContentFr): void
    {
        $this->aboutContentFr = $aboutContentFr;
    }

    public function getAboutContentEn(): ?string
    {
        return $this->aboutContentEn;
    }

    public function setAboutContentEn(?string $aboutContentEn): void
    {
        $this->aboutContentEn = $aboutContentEn;
    }

    public function getGcsTitleFr(): ?string
    {
        return $this->gcsTitleFr;
    }

    public function setGcsTitleFr(?string $gcsTitleFr): void
    {
        $this->gcsTitleFr = $gcsTitleFr;
    }

    public function getGcsTitleEn(): ?string
    {
        return $this->gcsTitleEn;
    }

    public function setGcsTitleEn(?string $gcsTitleEn): void
    {
        $this->gcsTitleEn = $gcsTitleEn;
    }

    public function getGcsContentFr(): ?string
    {
        return $this->gcsContentFr;
    }

    public function setGcsContentFr(?string $gcsContentFr): void
    {
        $this->gcsContentFr = $gcsContentFr;
    }

    public function getGcsContentEn(): ?string
    {
        return $this->gcsContentEn;
    }

    public function setGcsContentEn(?string $gcsContentEn): void
    {
        $this->gcsContentEn = $gcsContentEn;
    }

    public function getLegalNoticeTitleFr(): ?string
    {
        return $this->legalNoticeTitleFr;
    }

    public function setLegalNoticeTitleFr(?string $legalNoticeTitleFr): void
    {
        $this->legalNoticeTitleFr = $legalNoticeTitleFr;
    }

    public function getLegalNoticeTitleEn(): ?string
    {
        return $this->legalNoticeTitleEn;
    }

    public function setLegalNoticeTitleEn(?string $legalNoticeTitleEn): void
    {
        $this->legalNoticeTitleEn = $legalNoticeTitleEn;
    }

    public function getLegalNoticeContentFr(): ?string
    {
        return $this->legalNoticeContentFr;
    }

    public function setLegalNoticeContentFr(?string $legalNoticeContentFr): void
    {
        $this->legalNoticeContentFr = $legalNoticeContentFr;
    }

    public function getLegalNoticeContentEn(): ?string
    {
        return $this->legalNoticeContentEn;
    }

    public function setLegalNoticeContentEn(?string $legalNoticeContentEn): void
    {
        $this->legalNoticeContentEn = $legalNoticeContentEn;
    }

    public function getPersonalDataTitleFr(): ?string
    {
        return $this->personalDataTitleFr;
    }

    public function setPersonalDataTitleFr(?string $personalDataTitleFr): void
    {
        $this->personalDataTitleFr = $personalDataTitleFr;
    }

    public function getPersonalDataTitleEn(): ?string
    {
        return $this->personalDataTitleEn;
    }

    public function setPersonalDataTitleEn(?string $personalDataTitleEn): void
    {
        $this->personalDataTitleEn = $personalDataTitleEn;
    }

    public function getPersonalDataContentFr(): ?string
    {
        return $this->personalDataContentFr;
    }

    public function setPersonalDataContentFr(?string $personalDataContentFr): void
    {
        $this->personalDataContentFr = $personalDataContentFr;
    }

    public function getPersonalDataContentEn(): ?string
    {
        return $this->personalDataContentEn;
    }

    public function setPersonalDataContentEn(?string $personalDataContentEn): void
    {
        $this->personalDataContentEn = $personalDataContentEn;
    }
}
