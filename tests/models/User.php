<?php

use CCUPlus\Attachment\Traits\HasAttachmentTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasAttachmentTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
