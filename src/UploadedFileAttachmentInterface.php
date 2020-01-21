<?php

namespace Lle\AttachmentBundle;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Bundle\Bundle;

interface UploadedFileAttachmentInterface
{
    public function setUploadedFilesAttachment($attachments);
    public function getUploadedFilesAttachment();
    public function setUploadedFileAttachment(?UploadedFile $uploadedFile);
    public function getUploadedFileAttachment():?UploadedFile;
    public function getUploadedFilesAttachmentField():?string;
    public function setUploadedFilesAttachmentField(string $field);
    
}
