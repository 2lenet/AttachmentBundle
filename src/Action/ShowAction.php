<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Action;

use Doctrine\ORM\EntityManagerInterface;
use Lle\AttachmentBundle\Service\AttachmentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


final class ShowAction{

    private $manager;
    private $twig;

    public function __construct(AttachmentManager $manager, \Twig_Environment $twig){
        $this->manager = $manager;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, $item, $options): Response{
        $field = null;
        if (array_key_exists ( 'field' , $options )) {
            $field = $options['field'];
        }
        return new Response($this->twig->render('@LleAttachment/show.html.twig',

            [
                'docs' => $this->manager->findAll($item, $field),
                'list' => $this->manager->hasList(),
                'config' => $this->manager->getConfig(),
                'entity' => $item,
                'field' => $field
            ]
        ));
    }

}