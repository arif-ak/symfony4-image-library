<?php

namespace App\Services;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;

class CustomService
{
    /** @var ImageRepository */
    private $imageRepository;

    /**
     * CustomImageManager constructor.
     *
     * @param ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * Function to process limit and offset for get query.
     */
    public function validatePageFilters($limit,$page)
    {
        $result = [];
        $result['limit'] = preg_match('/^\d+$/',$limit) ? (int) $limit : 0;
        $result['page'] = preg_match('/^\d+$/',$page) ? (int) $page : Image::DEFAULT_PAGE;
        
        $result['totalItemsCount'] = $this->imageRepository->totalItemsCount();
        $result['totalPages'] = $result['limit'] ? (int) ceil($result['totalItemsCount'] / $result['limit']) : Image::DEFAULT_PAGE;

        return $result;
    }

}