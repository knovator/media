<?php


namespace Knovators\Media\Repository;

use Knovators\Media\Models\Media;
use Knovators\Support\Traits\BaseRepository;

/**
 * Class MediaRepository
 * @package Knovators\Media\Repository
 */
class MediaRepository extends BaseRepository
{

    /**
     * Configure the Model
     *
     **/
    public function model() {

        if ($model = config('media.model')) {
            return $model;
        }

        return Media::class;
    }


    /**
     * @param $input
     * @return
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getMediaFileList($input = []) {

        $masters = $this->model->orderByDesc('id')->select('id', 'name', 'type', 'mime_type');

        if (isset($input['user_id'])) {
            $masters = $masters->where('created_by', $input['user_id']);
        }
        $masters = datatables()->of($masters)
                               ->make(true);
        $this->resetModel();

        return $masters;
    }


}
