<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Service;

use Lle\AttachmentBundle\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;


class AttachmentManager{

    private $em;
    private $kernel;
    private $parameters;

    public function __construct(EntityManagerInterface $em,  Kernel $kernel, ParameterBagInterface $parameters){
        $this->em = $em;
        $this->kernel = $kernel;
        $this->parameters = $parameters;
    }


    protected function getUploadDir(Attachment  $attachment = null): string
    {
        $path = $this->kernel->getRootDir().'/../'.$this->parameters->get('lle.attachment.directory').'/';
        if(!$attachment) return $path;
        return $path . str_replace('\\','-',$attachment->getObjectClass()) . '/'.$attachment->getObjectId().'/';
    }

    public function hasList(){
        return $this->parameters->get('lle.attachment.show_list');
    }

    public function deleteById(int $id): void{
        $file = $this->find($id);
        $this->em->remove($file);
        $this->em->flush();
        unlink($file->getPath());
    }

    public function addFile(UploadedFile $media, $class, $id): ?Attachment{
        if ($media->isValid()) {
            $file = new Attachment();
            $file->setObjectId($id);
            $file->setObjectClass($class);
            $file->setFilename($media->getClientOriginalName());
            $file->setSize($media->getSize());
            $file->setMimeType($media->getMimetype());

            $uploadDir = $this->getUploadDir($file);

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $name = md5($file->getFilename().$id.microtime()).'.'.$media->getClientOriginalExtension();
            $media->move($uploadDir, $name);

            $file->setPath($uploadDir.$name);

            $this->em->persist($file);
            $this->em->flush();
            return $file;
        }
        return null;
    }

    public function getRepository(){
        return $this->em->getRepository(Attachment::class);
    }

    public function find(int $id): ?Attachment{
        return $this->getRepository()->find($id);
    }

    public function findAll(object $entity): iterable{

        return $this->getRepository()->findBy(['objectClass' => get_class($entity), 'objectId' => $this->getIdentifierValue($entity)]);
    }

    public function getUniqueId(object $entity): string{
        return str_replace('\\','-',get_class($entity)).$this->getIdentifierValue($entity);
    }

    public function getIconByMimeType($mimetype){
        return __DIR__.'/../Resources/public/image/cloud.png';
    }

    public function getPublicIconByMimeType($mimetype){
        return '/bundles/lleattachment/image/cloud.png';
    }

    public function isImage(Attachment $attachment):bool{
        return (bool)strstr($attachment->getMimetype(), 'image');
    }


    private function getIdentifierValue(object $entity){
        $meta = $this->em->getClassMetadata(get_class($entity));
        $identifier = $meta->getSingleIdentifierFieldName();
        return $meta->getIdentifierValues($entity)[$meta->getSingleIdentifierFieldName()];
    }



}