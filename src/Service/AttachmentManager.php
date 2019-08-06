<?php
declare(strict_types=1);
namespace Lle\AttachmentBundle\Service;

use Doctrine\ORM\EntityRepository;
use Lle\AttachmentBundle\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;


class AttachmentManager{

    private $em;
    private $kernel;
    private $parameters;
    private $router;
    private $attachements;

    public function __construct(EntityManagerInterface $em,  Kernel $kernel, ParameterBagInterface $parameters, Router $router){
        $this->em = $em;
        $this->kernel = $kernel;
        $this->parameters = $parameters;
        $this->router = $router;
    }


    protected function getUploadDir(Attachment  $attachment = null): string
    {
        $path = $this->parameters->get('lle.attachment.directory').'/';
        if (!$attachment) return $path;
        return $path . str_replace('\\','-',$attachment->getObjectClass()) . '/'.$attachment->getObjectId().'/';
    }

    public function load(string $class, array $ids): void{
        $this->attachements[$class] = [];
        $qb  = $this->getRepository()->createQueryBuilder('a')
            ->where('a.objectId IN (:ids)')
            ->andWhere('a.objectClass = :class')
            ->setParameters(['ids'=>$ids, 'class'=> $class]);
        foreach($qb->getQuery()->execute() as $file){
            /* @var \Lle\AttachmentBundle\Entity\Attachment $file */
            if(!isset($this->attachements[$class][$file->getObjectId()])){
                $this->attachements[$class][$file->getObjectId()] = [];
            }
            $this->attachements[$class][$file->getObjectId()][] = $file;
        }
    }

    public function hasList():bool {
        return (bool) $this->parameters->get('lle.attachment.show_list');
    }

    public function deleteById(int $id): void{
        $file = $this->find($id);
        $this->em->remove($file);
        $this->em->flush();
        unlink($this->kernel->getRootDir().'/../'.$file->getPath());
    }

    public function addFile(UploadedFile $media, $class, $id, $field): ?Attachment{

        if ($media->isValid()) {
            $file = new Attachment();
            $file->setObjectId($id);
            $file->setObjectClass($class);
            $file->setObjectField($field);
            $file->setFilename($media->getClientOriginalName());
            $file->setSize($media->getSize());
            $file->setMimeType($media->getMimetype());
            $file->setType($media->getClientOriginalExtension());

            return $this->moveAndSave($file, $media, $id);
        }
        return null;
    }

    public function addFileObject(File $media, $class, $id, $field=null): ?Attachment{

        $file = new Attachment();
        $file->setObjectId($id);
        $file->setObjectClass($class);
        $file->setObjectField($field);
        $file->setFilename($media->getFilename());
        $file->setSize($media->getSize());
        $file->setMimeType($media->getMimetype());
        $file->setType($media->guessExtension());

        return $this->moveAndSave($file, $media, $id);
    }

    private function moveAndSave(Attachment $file, $media, $id) {

        $uploadDir = $this->kernel->getRootDir().'/../'.$this->getUploadDir($file);

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $name = md5($file->getFilename().$id.microtime()).'.'.$file->getType();
        $media->move($uploadDir, $name);

        $file->setPath($this->getUploadDir($file).$name);

        $this->em->persist($file);
        $this->em->flush();
        
        return $file;
    }

    public function getAbsolutePath($file) {
        return $this->kernel->getRootDir() . '/../' . $file->getPath();
    }

    public function getRepository(): EntityRepository {
        return $this->em->getRepository(Attachment::class);
    }

    public function find(int $id): ?Attachment{
        return $this->getRepository()->find($id);
    }
    
    public function findAll(object $entity, $field=null): iterable{
        $class = $this->getClass($entity);
        $identifier = $this->getIdentifierValue($entity, $field);
        if(isset($this->attachements[$class]) && isset($this->attachements[$class][$identifier])){
            return $this->attachements[$class][$identifier];
        }
        return $this->getRepository()->findBy(['objectClass' => $class, 'objectId' => $identifier, 'objectField'=> $field]);
    }

    public function getUniqueId(object $entity, $field): string{
        if($field){
            return str_replace('\\','-',get_class($entity)).$this->getIdentifierValue($entity).'-'.$field;
        }else{
            return str_replace('\\','-',get_class($entity)).$this->getIdentifierValue($entity);
        }

    }

    public function getIconByMimeType($mimetype){
        if(strstr($mimetype,'cloud')) {
            return __DIR__ . '/../Resources/public/image/cloud.png';
        }
        return null;
    }

    public function getPublicIcon(Attachment $attachment){
        return $this->router->generate('lle_attachment_thumb',['id' => $attachment->getId()]);
    }

    public function generateRemoveUrl(Attachment $attachment){
        return $this->router->generate('lle_attachment_delete', ['id' => $attachment->getId()]);
    }

    public function generateDownloadUrl(Attachment $attachment){
        return $this->router->generate('lle_attachment_download', ['id' => $attachment->getId()]);
    }

    public function isImage(Attachment $attachment):bool{
        return (bool)strstr($attachment->getMimetype(), 'image');
    }


    private function getIdentifierValue(object $entity){
        $meta = $this->em->getClassMetadata(get_class($entity));
        return $meta->getIdentifierValues($entity)[$meta->getSingleIdentifierFieldName()];
    }

    private function getClass(object $entity){
        $meta = $this->em->getClassMetadata(get_class($entity));
        return $meta->getReflectionClass()->getName();
    }

    public function getConfig(): array{
        return [
            'needConfirmRemove' => (bool) $this->parameters->get('lle.attachment.need_confirm_remove')
        ];
    }



}