<?php

namespace App\Entity;

use Serializable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HomePageBlockRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: HomePageBlockRepository::class)]
#[Vich\Uploadable]
class HomePageBlock implements Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $homePageBlockButton = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backgroundColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'homePage_images', fileNameProperty: 'image')]
    public ?File $imageFile = null;

    #[ORM\ManyToOne(inversedBy: 'homePageBlock')]
    private ?HomePage $homePage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

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

    public function getHomePageBlockButton(): ?string
    {
        return $this->homePageBlockButton;
    }

    public function setHomePageBlockButton(?string $homePageBlockButton): static
    {
        $this->homePageBlockButton = $homePageBlockButton;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): static
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function setImageFile(File $image )
    {
        $this->imageFile = $image;
    }


    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getHomePage(): ?HomePage
    {
        return $this->homePage;
    }

    public function setHomePage(?HomePage $homePage): static
    {
        $this->homePage = $homePage;

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->image,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->image,
        ) = unserialize($serialized);
    }
}
