<?php

namespace Knovators\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Media
 * @package Knovators\Media\Http\Resources
 */
class Media extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'width'     => $this->width,
            'height'    => $this->height,
            'type'      => $this->type,
            'mime_type' => $this->mime_type,
            'url'       => $this->url,
            'title'     => $this->title,
            'alt'       => $this->alt,
            'link'      => $this->link,
        ];
    }
}
