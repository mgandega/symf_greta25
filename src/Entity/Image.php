<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 20,
        max: 50,
        minMessage: 'La description doit avoir au minimum {{ limit }} caractères',
        maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères'
    )]
    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    // Ce champ stocke le chemin du fichier
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    // Ce champ n'est pas mappé en base de données
    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['application/pdf'],
        mimeTypesMessage: 'Veuillez uploader un fichier PDF valide.'
    )]
    private ?File $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): static
    {
        $this->alt = $alt;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;
        return $this;
    }

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Returns the URL of the image as a string.
     * If the URL is not set, returns 'Image'.
     *
     * @return string
     */

/******  e12644ac-8fa9-4f4f-81f1-a0ffab308ced  *******/
    public function __toString(): string
    {
        return $this->url ?? 'Image';
    }
}
