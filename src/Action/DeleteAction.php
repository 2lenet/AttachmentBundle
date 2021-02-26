<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Action;


use Lle\AttachmentBundle\Service\AttachmentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


final class DeleteAction{

    private $manager;

    public function __construct(AttachmentManager $manager){
        $this->manager = $manager;
    }

    public function __invoke(Request $request): Response{
        $this->manager->deleteById((int)$request->get('id'));
        if($request->isXmlHttpRequest()){
            return new JsonResponse(['success'=>true]);
        }
        return new RedirectResponse($request->server->get('HTTP_REFERER') ?? '/');
    }

}