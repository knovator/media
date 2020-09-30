<?php

namespace Knovators\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Knovators\Support\Traits\HasModelEvent;
use Knovators\Support\Traits\HasSlug;

/**
 * Class Media
 * @package Knovators\Media\Models
 * @method static where(string $string, $image)
 */
class Media extends Model
{

    use HasSlug, HasModelEvent, SoftDeletes;

    protected $appends = ['url'];


    protected $table = 'files';

    protected $fillable = [
        'name',
        'title',
        'alt',
        'link',
        'type',
        'slug',
        'uri',
        'mime_type',
        'file_size',
        'width',
        'height',
        'status',
        'created_by',
        'deleted_by',
    ];

    protected $slugColumn = 'slug';

    protected $slugifyColumns = ['name', 'id'];


    /**
     * @return string
     */
    public function getUrlAttribute() {
        return config('media.storage_app_url') . DIRECTORY_SEPARATOR . config('media.base_folder')
            . DIRECTORY_SEPARATOR . $this->uri;

    }


}
