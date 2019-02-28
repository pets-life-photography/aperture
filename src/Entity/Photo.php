<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="blob")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $contentType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WatermarkConfiguration", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $watermarkConfiguration;

    /**
     * @ORM\Column(type="json_array")
     */
    private $metaData;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    private $width;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    private $height;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhotoVersion", mappedBy="photo", orphanRemoval=true, cascade={"persist"})
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getWatermarkConfiguration(): ?WatermarkConfiguration
    {
        return $this->watermarkConfiguration;
    }

    public function setWatermarkConfiguration(?WatermarkConfiguration $configuration): self
    {
        $this->watermarkConfiguration = $configuration;

        return $this;
    }

    public function getMetaData()
    {
        return $this->metaData;
    }

    public function setMetaData($metaData): self
    {
        $this->metaData = $metaData;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return Collection|PhotoVersion[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(PhotoVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions[] = $version;
            $version->setPhoto($this);
        }

        return $this;
    }

    public function removeVersion(PhotoVersion $version): self
    {
        if ($this->versions->contains($version)) {
            $this->versions->removeElement($version);
            // set the owning side to null (unless already changed)
            if ($version->getPhoto() === $this) {
                $version->setPhoto(null);
            }
        }

        return $this;
    }
}
