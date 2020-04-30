<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    const IMAGE_PATH = 'uploads/images/original/';
    const THUMBNAIL_PATH = 'uploads/images/thumbnail/';
    public static $supportedImageExtensions = ['jpg','jpeg','png','gif','bmp','wbmp','webp','xpm','xbm'];
    const THUMBNAIL_WIDTH = 320;
    const THUMBNAIL_HEIGHT = 240;
    // const DEFAULT_ENTRY_PER_PAGE = 15;
    const DEFAULT_PAGE = 1; 

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $thumbnailUrl;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $originalFileName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(?string $thumbnailUrl): self
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

}
