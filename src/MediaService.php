<?php

namespace Knovators\Media;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Knovators\Media\Http\Requests\CreateRequest;
use Knovators\Media\Models\Media;
use Knovators\Media\Repository\MediaRepository;
use Knovators\Support\Helpers\HTTPCode;
use Knovators\Support\Helpers\UploadService;
use Knovators\Support\Traits\APIResponse;

/**
 * Trait MediaService
 * @package Knovators\Media
 */
trait MediaService
{

    use APIResponse;


    protected $mediaRepository;

    /**
     * AnnouncementController constructor.
     * @param MediaRepository $mediaRepository
     */
    public function __construct(
        MediaRepository $mediaRepository
    ) {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(CreateRequest $request) {
        $input = $request->all();
        try {
            $user = Auth::user();
            $media = $this->uploadFiles($input, $user->id);
            return $this->sendResponse($media,
                trans('media::messages.uploaded', ['module' => 'Media']),
                HTTPCode::CREATED);

        } catch (Exception $exception) {
            Log::error($exception);

        }

        return $this->sendResponse(null, __('media::messages.something_wrong'),
            HTTPCode::UNPROCESSABLE_ENTITY, $exception);


    }


    /**
     * @param $userId
     * @param $input
     * @return array
     * @throws \Exception
     */
    private function uploadFiles($input, $userId) {

        $media['ids'] = [];
        $baseFolder = config('media.base_folder');
        $driver = config('media.driver');
        try {
            DB::beginTransaction();
            foreach ($input['files'] as $key => $file) {

                $name = UploadService::getFileName($file);
                $mime = $file->getClientMimeType();
                $folder = UploadService::getFileLocation($mime);
                $dbPath = UploadService::getDBFilePath($userId, $folder);
                $path = "$baseFolder/{$dbPath}";

                UploadService::storeMedia($file, $path, $name, $driver);

                $attributes = [
                    'name'      => $name,
                    'type'      => $input['type'],
                    'uri'       => $this->setFileUri($dbPath, $name),
                    'mime_type' => $mime,
                    'file_size' => $file->getSize()
                ];

                array_push($media['ids'], $this->mediaRepository->create($attributes)->id);
            }

            DB::commit();

            return $media;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }


    }

    /**
     * @param $path
     * @param $name
     * @return string
     */
    private function setFileUri($path, $name) {
        return $path . DIRECTORY_SEPARATOR . $name;
    }


    /**
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Media $media) {
        try {
            $media->delete();

            return $this->sendResponse($media,
                trans('media::messages.deleted', ['module' => 'Media']),
                HTTPCode::CREATED);

        } catch (Exception $exception) {
            Log::error($exception);

        }

        return $this->sendResponse(null, __('media::messages.something_wrong'),
            HTTPCode::UNPROCESSABLE_ENTITY, $exception);
    }


    /**
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function userImages() {
        try {
            $input['user_id'] = Auth::user()->id;
            $files = $this->mediaRepository->getMediaFileList($input);

            return $this->sendResponse($files,
                trans('media::messages.retrieved', ['module' => 'Media']),
                HTTPCode::CREATED);

        } catch (Exception $exception) {
            Log::error($exception);

        }

        return $this->sendResponse(null, __('media::messages.something_wrong'),
            HTTPCode::UNPROCESSABLE_ENTITY, $exception);
    }


    /**
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            $files = $this->mediaRepository->getMediaFileList();

            return $this->sendResponse($files,
                trans('media::messages.retrieved', ['module' => 'Media']),
                HTTPCode::CREATED);

        } catch (Exception $exception) {
            Log::error($exception);

        }

        return $this->sendResponse(null, __('media::messages.something_wrong'),
            HTTPCode::UNPROCESSABLE_ENTITY, $exception);
    }


}
