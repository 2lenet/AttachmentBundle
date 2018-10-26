<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Lle\AttachmentBundle\Service\AttachmentManager;


final class ThumbAction{

    private $manager;

    public function __construct(AttachmentManager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(Request $request): Response{
        $file = $this->manager->find((int)$request->get('id'));
        if($this->manager->isImage($file)) {
            $response = new BinaryFileResponse($file->getPath());
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
            $response->headers->set('Content-Type', ($mimeTypeGuesser->isSupported()) ? $mimeTypeGuesser->guess($file->getPath()) : 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename());
        }else{
            if($this->manager->getIconByMimeType($file->getMimetype())){
                $response = new BinaryFileResponse($this->manager->getIconByMimeType($file->getMimetype()));
            }else{
                $tempFile = tempnam(sys_get_temp_dir(), 'tmp');
                $image = imagecreate(100,100);
                $noir = imagecolorallocate($image, 0, 0, 0);
                $blue = imagecolorallocate($image, 58, 153, 220);
                $blanc = imagecolorallocate($image, 255, 255, 255);
                imagefill($image, 0, 0, $blue);
                $ext = strtoupper(substr(basename($file->getMimetype()),0,3)).'/'. strtoupper(substr($file->getType(),0,3));
                imagestring($image, 5, 23, 45, $ext, $blanc);
                imagepng($image, $tempFile);
                return new BinaryFileResponse($tempFile);
            }
        }
        return $response;
    }

}