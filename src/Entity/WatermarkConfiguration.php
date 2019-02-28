<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Spatie\Image\Manipulations;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WatermarkConfigurationRepository")
 */
class WatermarkConfiguration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Watermark")
     * @ORM\JoinColumn(nullable=false)
     */
    private $watermark;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=1, max=100)
     */
    private $opacity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice({
     *     Manipulations::POSITION_TOP_LEFT: "Top left",
     *     Manipulations::POSITION_TOP: "Top",
     *     Manipulations::POSITION_TOP_RIGHT: "Top right",
     *     Manipulations::POSITION_LEFT: "Left",
     *     Manipulations::POSITION_CENTER: "Center",
     *     Manipulations::POSITION_RIGHT: "Right",
     *     Manipulations::POSITION_BOTTOM_LEFT: "Bottom left",
     *     Manipulations::POSITION_BOTTOM: "Bottom",
     *     Manipulations::POSITION_BOTTOM_RIGHT: "Bottom right"
     * })
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $crop;

    /**
     * @ORM\Column(type="float")
     */
    private $paddingX;

    /**
     * @ORM\Column(type="float")
     */
    private $paddingY;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\Choice({
     *     Manipulations::UNIT_PIXELS: "px pixels",
     *     Manipulations::UNIT_PERCENT: "% percent"
     * })
     */
    private $paddingUnit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWatermark(): ?Watermark
    {
        return $this->watermark;
    }

    public function setWatermark(?Watermark $watermark): self
    {
        $this->watermark = $watermark;

        return $this;
    }

    public function getOpacity(): ?int
    {
        return $this->opacity;
    }

    public function setOpacity(int $opacity): self
    {
        $this->opacity = $opacity;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

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

    public function getPaddingX(): ?float
    {
        return $this->paddingX;
    }

    public function setPaddingX(float $paddingX): self
    {
        $this->paddingX = $paddingX;

        return $this;
    }

    public function getPaddingY(): ?float
    {
        return $this->paddingY;
    }

    public function setPaddingY(float $paddingY): self
    {
        $this->paddingY = $paddingY;

        return $this;
    }

    public function getPaddingUnit(): ?string
    {
        return $this->paddingUnit;
    }

    public function setPaddingUnit(string $paddingUnit): self
    {
        $this->paddingUnit = $paddingUnit;

        return $this;
    }
}
