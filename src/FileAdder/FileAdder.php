<?php

namespace CCUPlus\Attachment\FileAdder;

use CCUPlus\Attachment\Attachment;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileAdder
{
    /**
     * Laravel Model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Symfony UploadedFile.
     *
     * @var UploadedFile
     */
    protected $file;

    /**
     * Attachment filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Save upload file metadata to database.
     *
     * @return Attachment
     */
    public function save() : Attachment
    {
        $attachment = new Attachment([
            'user_id' => request()->user() ? request()->user()->getKey() : null,
            'sha512' => hash_file('sha512', $this->file->getPathname()),
            'filename' => $this->getFilename(),
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'path' => $this->moveFile(),
        ]);

        return $this->model->attachments()->save($attachment);
    }

    /**
     * Copy upload file to storage.
     *
     * @return string
     */
    protected function moveFile() : string
    {
        $target = sprintf('attachments');

        return Storage::disk(config('attachments.disk'))
            ->putFileAs($target, $this->file, $this->getFilename());
    }

    /**
     * @param Model $model
     *
     * @return FileAdder
     */
    public function setModel(Model $model) : FileAdder
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param UploadedFile $file
     *
     * @return FileAdder
     */
    public function setFile(UploadedFile $file) : FileAdder
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename() : string
    {
        if (! is_null($this->filename)) {
            return $this->filename;
        }

        $filename = $this->file->getClientOriginalName();

        $extension = $this->file->getClientOriginalExtension();

        if ($len = strlen($extension)) {
            $filename = substr($filename, 0, -($len + 1));
        }

        return $this->setFilename($filename)->getFilename();
    }

    /**
     * @param string $filename
     *
     * @return FileAdder
     */
    public function setFilename(string $filename) : FileAdder
    {
        $this->filename = $this->sanitizeFilename($filename);

        return $this;
    }

    /**
     * Sanitizes a filename, replacing whitespace with dashes.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function sanitizeFilename(string $filename) : string
    {
        $specialChars = array_merge(
            ['<', '>', ':', '"', '/', '\\', '|', '?', '*'],
            array_map('chr', range(0, 31))
        );

        return str_replace($specialChars, '-', $filename);
    }
}
