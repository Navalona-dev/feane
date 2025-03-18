<?php

namespace App\Entity;

use Serializable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HomePageRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: HomePageRepository::class)]
#[Vich\Uploadable]
class HomePage implements Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backgroundColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backgroundImage = null;

    #[Vich\UploadableField(mapping: 'homePage_images', fileNameProperty: 'backgroundImage')]
    public ?File $backgroundFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $homepageButton = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'homePage_images', fileNameProperty: 'image')]
    public ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonColorHover = null;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: HomePageBlock::class)]
    private Collection $homePageBlock;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    #[Vich\UploadableField(mapping: 'homePage_images', fileNameProperty: 'video')]
    public ?File $videoFile = null;

    public function __construct()
    {
        $this->homePageBlock = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?string $backgroundImage): static
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    public function setBackgroundFile(File $backgroundImage )
    {
        $this->backgroundFile = $backgroundImage;
    }


    public function getBackgroundFile()
    {
        return $this->backgroundFile;
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getHomepageButton(): ?string
    {
        return $this->homepageButton;
    }

    public function setHomepageButton(?string $homepageButton): static
    {
        $this->homepageButton = $homepageButton;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getButtonColor(): ?string
    {
        return $this->buttonColor;
    }

    public function setButtonColor(?string $buttonColor): static
    {
        $this->buttonColor = $buttonColor;

        return $this;
    }

    public function getButtonColorHover(): ?string
    {
        return $this->buttonColorHover;
    }

    public function setButtonColorHover(?string $buttonColorHover): static
    {
        $this->buttonColorHover = $buttonColorHover;

        return $this;
    }

    /**
     * @return Collection<int, HomePageBlock>
     */
    public function getHomePageBlock(): Collection
    {
        return $this->homePageBlock;
    }

    public function addHomePageBlock(HomePageBlock $homePageBlock): static
    {
        if (!$this->homePageBlock->contains($homePageBlock)) {
            $this->homePageBlock->add($homePageBlock);
            $homePageBlock->setHomePage($this);
        }

        return $this;
    }

    public function removeHomePageBlock(HomePageBlock $homePageBlock): static
    {
        if ($this->homePageBlock->removeElement($homePageBlock)) {
            // set the owning side to null (unless already changed)
            if ($homePageBlock->getHomePage() === $this) {
                $homePageBlock->setHomePage(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->image,
            $this->video,
            $this->backgroundImage,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->image,
            $this->video,
            $this->backgroundImage,
        ) = unserialize($serialized);
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function setVideoFile(File $video )
    {
        $this->videoFile = $video;
    }


    public function getVideoFile()
    {
        return $this->videoFile;
    }
}
