<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        minMessage: 'la description doit avoir au minimum de {{ limit }} caractères',
        maxMessage: 'la description ne doit pas avoir plus de  {{ limit }} caractères'
        )]
    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    #[Assert\Length(
        min: 10,
        max: 50,
        minMessage: 'la description doit avoir au minimum de {{ limit }} caractères',
        maxMessage: 'la description ne doit pas avoir plus de  {{ limit }} caractères'
        )]
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[Assert\File(
        maxSize: '1024k',
        extensions: ['jpg', 'jpeg', 'png', 'pdf'],
        extensionsMessage: 'Please upload a valid file',
    )]
    private UploadedFile $file ;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

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

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(uploadedFile $file): static
    {
        $this->file = $file;

        return $this;
    }
}
