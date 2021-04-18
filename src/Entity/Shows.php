<?php

namespace App\Entity;

use App\Repository\ShowsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ShowsRepository::class)
 * @Vich\Uploadable
 */
class Shows
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $location;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface
     */
    private $expected_at;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface
     */
    private $updated_at;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @var array<int, string>
     */
    private $metaKeywords = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     * @var array<int, string>
     */
    private $keywords = [];

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     * @var string
     */
    private $slug;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="images", fileNameProperty="image")
     */
    private $imageFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getExpectedAt(): ?\DateTimeInterface
    {
        return $this->expected_at;
    }

    public function setExpectedAt(\DateTimeInterface $expected_at): self
    {
        $this->expected_at = $expected_at;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array<int, string>|null
     */
    public function getMetaKeywords(): ?array
    {
        return $this->metaKeywords;
    }

    /**
     * Undocumented function
     *
     * @param array<int, string>|null $metaKeywords
     * @return self
     */
    public function setMetaKeywords(?array $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array<int, string>|null
     */
    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    /**
     * Undocumented function
     *
     * @param array<int, string>|null $keywords
     * @return self
     */
    public function setKeywords(?array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if (null !== $imageFile) {
            $this->updated_at = new \DateTimeImmutable();
        }
    }
}
