<?php

namespace App\Services;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Symfony\Component\HttpFoundation\Response;

class CustomFileManager
{
    const UPLOAD_SUCCESS = Response::HTTP_OK;
    const UPLOAD_SUCCESS_MESSAGE = 'File(s) uploaded successfully';
    const UPLOAD_ERROR = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;
    const UPLOAD_ERROR_MESSAGE = 'File format not supported';
    const UPLOAD_PARTIAL = Response::HTTP_PARTIAL_CONTENT;
    const UPLOAD_PARTIAL_MESSAGE = 'One or more files not uploaded';

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * CustomFileManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveImages($files)
    {   
        $uploadedFilesCounter = 0; //counter to track uploaded/discarded files

        // Condition to change single file entry into an array entry, if frontend uses 'file' key instead of 'file[]' key
        if(!is_array($files))   
            $files = [$files];

        foreach($files as $file)
        {   
            if ($this->validateForImage($file)) {
                
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                // 'images/original' folder created when image is moved
                $file->move(
                    Image::IMAGE_PATH,     
                    $fileName
                );

                //creating 'images/thumbnail' folder path if it does not exist
                if(!file_exists(Image::THUMBNAIL_PATH)) 
                    mkdir(Image::THUMBNAIL_PATH);

                $thumbnail = ImageManager::make(Image::IMAGE_PATH . $fileName)->resize(Image::THUMBNAIL_WIDTH, Image::THUMBNAIL_HEIGHT); //creating thumbnail from original image using 'Intervention/Image' bundle
                $thumbnail->save(Image::THUMBNAIL_PATH.$fileName);  //saving thumbnail to thumbnail folder

                // saving image details to database
                $image = new Image();
                $image->setImageUrl(Image::IMAGE_PATH.$fileName);
                $image->setThumbnailUrl(Image::THUMBNAIL_PATH.$fileName);
                $image->setOriginalFileName($file->getClientOriginalName());
                $this->entityManager->persist($image);

                $uploadedFilesCounter++;

            } else {
                continue;
            }
        }

        $this->entityManager->flush();

        //response message based on number of files uploaded/discarded
        if($uploadedFilesCounter == count($files))
            return ['code' => self::UPLOAD_SUCCESS, 'message' => self::UPLOAD_SUCCESS_MESSAGE];
        elseif($uploadedFilesCounter == 0)
            return ['code' => self::UPLOAD_ERROR, 'message' => self::UPLOAD_ERROR_MESSAGE];
        elseif($uploadedFilesCounter < count($files))
            return ['code' => self::UPLOAD_PARTIAL, 'message' => self::UPLOAD_PARTIAL_MESSAGE];
        
    }

    /**
     * Validation for Image file(s)
     */
    public function validateForImage($file)
    {
        $isValid = true;
        //check whether file is image or not based on file extension
        if(!(in_array(strtolower($file->guessExtension()),Image::$supportedImageExtensions))) 
            $isValid = false;
        
        //TODO : more validations for image file(s)

        return $isValid;
    }
    
    /**
     * Generate unique file name
     */
    public function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}