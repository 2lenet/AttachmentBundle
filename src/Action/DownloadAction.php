<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Lle\AttachmentBundle\Service\AttachmentManager;


final class DownloadAction{

    private $manager;

    public function __construct(AttachmentManager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(Request $request): Response{
        $file = $this->manager->find((int)$request->get('id'));
        $path = $this->manager->getAbsolutePath($file);
        $response = new BinaryFileResponse($path);
        $mimeTypeGuesser = new MimeTypes();
        $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($path) ?? 'text/plain');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename());

        return $response;
    }

}
