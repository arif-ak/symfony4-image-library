<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\FileUploadType;
use App\Services\CustomFileManager;
use App\Services\CustomService;
use Intervention\Image\ImageManagerStatic as ImageManager;

class ImageController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("api/image", name="image_list")
     */
    public function indexAction(Request $request, ImageRepository $imageRepository, CustomService $customService)
    {
        $limit = $request->query->get('limit');
        $page = $request->query->get('page');

        $filterData = $customService->validatePageFilters($limit,$page);    //logic to calculate number of pages and entries per page

        $images = $imageRepository->findImages($filterData['limit'],$filterData['page']);

        return [
            'code' => Response::HTTP_OK,
            'items' => count($images),
            'limit' => $filterData['limit'],
            'page' =>  $filterData['page'],
            'data' => $images
        ];
    }

    /**
     * @Rest\Post("api/image/upload", name="image_upload")
     */
    public function insertImageAction(Request $request,CustomFileManager $customFileManager)
    {
        $files = $request->files->get('file');

        $response = $customFileManager->saveImages($files); //image saving service

        return [
            'code' => $response['code'],
            'message' => $response['message']
        ];
    }

    /**
     * @Route("/image-library", name="image_library")
     */
    public function adminIndexAction(Request $request, ImageRepository $imageRepository, CustomFileManager $customFileManager, CustomService $customService)
    {

        $limit = $request->query->get('limit');
        $page = $request->query->get('page');

        $filterData = $customService->validatePageFilters($limit,$page);    //validate limit and page values, logic to calculate number of pages and entries per page
        $images = $imageRepository->findImages($filterData['limit'],$filterData['page']);
        
        $form = $this->createForm(FileUploadType::class, []); //form to get files
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $files = $request->files->get('file_upload')['file'];
            $response = $customFileManager->saveImages($files); //image saving service

            $this->addFlash('message', $response['message']);

            return $this->redirectToRoute('image_library');
        }
     
        return $this->render('image/imageLibrary.html.twig', [
            'numberOfItems' => $filterData['totalItemsCount'],
            'totalPages' => $filterData['totalPages'],
            'page' => $filterData['page'],
            'images' => $images,
            'form' => $form->createView()
        ]);
    }
}
