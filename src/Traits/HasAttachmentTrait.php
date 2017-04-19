<?php

namespace CCUPlus\Attachment\Traits;

use CCUPlus\Attachment\Attachment;
use CCUPlus\Attachment\FileAdder\FileAdder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasAttachmentTrait
{
    /**
     * Get all attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'model');
    }

    /**
     * @param UploadedFile $file
     *
     * @return mixed
     */
    public function addFile(UploadedFile $file)
    {
        return app(FileAdder::class)
            ->setModel($this)
            ->setFile($file);
    }
}
