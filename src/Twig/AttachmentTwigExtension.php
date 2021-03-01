<?php
declare(strict_types=1);

namespace Lle\AttachmentBundle\Twig;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Lle\AttachmentBundle\Service\AttachmentManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class AttachmentTwigExtension extends AbstractExtension
{

    private $manager;

    public function __construct(AttachmentManager $manager)
    {
        $this->manager = $manager;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('render_attachment', array($this, 'renderAttachment'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new TwigFunction('list_attachment', array($this, 'listAttachment'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new TwigFunction('get_attachments', array($this, 'getAttachments'), array('is_safe' => array('html'), 'needs_environment' => true))
        );
    }

    public function renderAttachment(Environment $env, object $item, $field=null)
    {
        return $env->render('@LleAttachment/twig_extension/render_attachment.html.twig',[
            'docs' => $this->manager->findAll($item, $field),
            'item' => $item,
            'field' => $field,
            'class' => get_class($item),
            'unique_id' => $this->manager->getUniqueId($item, $field),
            'config' => $this->manager->getConfig()
        ]);
    }
    public function getAttachments(Environment $env, object $item, $field=null)
    {
        return $this->manager->findAll($item, $field);
    }
    public function listAttachment(Environment $env, object $item, $field=null)
    {
        return $env->render('@LleAttachment/twig_extension/list_attachment.html.twig',[
            'docs' => $this->manager->findAll($item, $field)
        ]);
    }

    public function getName()
    {
        return 'attachment_extension';
    }
}
