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
        if(strstr($file->getMimetype(), 'image')) {
            $response = new BinaryFileResponse($file->getPath());
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
            $response->headers->set('Content-Type', ($mimeTypeGuesser->isSupported()) ? $mimeTypeGuesser->guess($file->getPath()) : 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename());
        }else{
            $response = new BinaryFileResponse($this->manager->getIconByMimeType($file->getMimetype()));
        }

        return $response;
    }

}