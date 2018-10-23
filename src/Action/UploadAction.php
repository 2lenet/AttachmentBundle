<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Action;

use Lle\AttachmentBundle\Service\AttachmentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Attachment;


final class UploadAction{


    private $manager;

    public function __construct(AttachmentManager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(Request $request): Response{
        $success = $this->manager->addFile($request->files->get('file'), $request->get('class'), $request->get('id'));
        return new JsonResponse(['success' => $success]);
    }

}