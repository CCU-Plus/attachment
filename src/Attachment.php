<?php

namespace CCUPlus\Attachment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use SoftDeletes;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'model_id' => 'integer',
        'user_id' => 'integer',
        'size' => 'integer',
        'downloads' => 'integer',
    ];

    /**
     * Get all of the owning attachmentable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     *
     * @codeCoverageIgnore
     */
    public function attachmentable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        $this->increment('downloads');

        $stream = Storage::disk(config('attachments.disk'))
            ->readStream($this->getAttribute('path'));

        return response()->stream(function () use ($stream) {
            if (ob_get_length()) {
                ob_clean();
            }

            fpassthru($stream);
        },
            200,
            [
                'Content-Type' => $this->getAttribute('mime_type'),
                'Content-Length' => $this->getAttribute('size'),
                'Content-Disposition' => sprintf('attachment; filename="%s"', $this->getAttribute('filename')),
            ]
        );
    }
}
