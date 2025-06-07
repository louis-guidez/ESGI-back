<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlChemin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateUpload = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlChemin(): ?string
    {
        return $this->urlChemin;
    }

    public function setUrlChemin(?string $urlChemin): static
    {
        $this->urlChemin = $urlChemin;

        return $this;
    }

    public function getDateUpload(): ?\DateTime
    {
        return $this->dateUpload;
    }

    public function setDateUpload(?\DateTime $dateUpload): static
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }
}
