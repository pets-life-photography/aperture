<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoTypeRepository")
 */
class PhotoType
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="boolean")
     */
    private $watermark;

    /**
     * @ORM\Column(type="boolean")
     */
    private $crop;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWatermark(): ?bool
    {
        return $this->watermark;
    }

    public function setWatermark(bool $watermark): self
    {
        $this->watermark = $watermark;

        return $this;
    }

    public function getCrop(): ?bool
    {
        return $this->crop;
    }

    public function setCrop(bool $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
