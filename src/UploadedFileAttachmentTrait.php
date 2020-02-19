<?php

namespace Lle\AttachmentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadedFileAttachmentTrait
{

    private $uploadedFilesAttachment;
    private $uploadedFileAttachment;
    private $uploadedFilesAttachmentField = null;

    /**
     * @param UploadedFile[] $uploadedFilesAttachment
     */
    public function setUploadedFilesAttachment($uploadedFilesAttachment){
        $this->uploadedFilesAttachment = $uploadedFilesAttachment;
        return $this;
    }

    /**
     * @return UploadedFile[]
     */
    public function getUploadedFilesAttachment(){
        return $this->uploadedFilesAttachment;
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    public function setUploadedFileAttachment(?UploadedFile $uploadedFile){
        $this->uploadedFileAttachment = $uploadedFile;
        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFileAttachment():?UploadedFile{
        return $this->uploadedFileAttachment;
    }

    public function getUploadedFilesAttachmentField(): ?string{
        return $this->uploadedFilesAttachmentField;
    }

    public function setUploadedFilesAttachmentField(string $uploadedFilesAttachmentField){
        $this->uploadedFilesAttachmentField = $uploadedFilesAttachmentField;
        return $this;
    }
}
