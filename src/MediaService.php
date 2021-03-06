<?php

namespace Knovators\Media;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Knovators\Media\Http\Requests\CreateRequest;
use Knovators\Media\Models\Media;
use Knovators\Media\Repository\MediaRepository;
use Knovators\Support\Helpers\HTTPCode;
use Knovators\Support\Helpers\UploadService;
use Knovators\Support\Traits\APIResponse;
use Illuminate\Http\Request;

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
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CreateRequest $request) {
        $input = $request->all();
        try {
            $user = Auth::user();
            if (isset($input['file'])) {
                $media = $this->uploadFile($input);
            } elseif (isset($input['files'])) {
                $media = $this->uploadFiles($input, $user->id ?? null);
            }

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
     * @param $file
     * @param $input
     * @return mixed
     * @throws Exception
     */
    protected function uploadFile($input, $file = null) {
        try {
            $baseFolder = config('media.base_folder');
            $mediaPath = config('media.driver');
            $file = $file ?? $input['file'];
            $name = UploadService::getFileName($file);
            $mime = $file->getClientMimeType();
            $sizeDetails = @getimagesize($file->getRealPath());
            [$width, $height] = $sizeDetails;
            $folder = UploadService::getFileLocation($mime);
            $dbPath = UploadService::getDBFilePath($input['type'], $folder);
            $path = "{$baseFolder}/{$dbPath}";
            UploadService::storeMedia($file, $path, $name, $mediaPath);
            $attributes = [
                'name'      => $name,
                'type'      => $input['type'] ?? null,
                'uri'       => $this->setFileUri($dbPath, $name),
                'mime_type' => $mime,
                'width'     => $width,
                'height'    => $height,
                'title'     => $input['title'] ?? null,
                'alt'       => $input['alt'] ?? null,
                'link'      => $input['link'] ?? null,
                'file_size' => $file->getSize(),
            ];
            return $this->mediaRepository->create($attributes);

        } catch (Exception $exception) {
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
     * @param $userId
     * @param $input
     * @return array
     * @throws Exception
     */
    protected function uploadFiles($input, $userId = null) {
        $media['ids'] = [];
        try {
            foreach ($input['files'] as $key => $file) {
                $mediaObject = $this->uploadFile($input, $file);
                $media['ids'][] = $mediaObject->id;
            }
            return $media;
        } catch (Exception $exception) {
            throw $exception;
        }


    }

    /**
     * @param Media $media
     * @return JsonResponse
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
     * @return JsonResponse
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
     * @return JsonResponse
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

    /**
     * @param         $uri
     * @param         $extension
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function imageParse($uri, $extension, Request $request) {
        $disk = ($request->has('disk')) ? Storage::disk($request->get('disk')) : Storage::disk('public');
        $uri = explode('/', $uri);
        $uriPop = array_pop($uri);
        $checkIfNeedsToResize = $this->checkIfNeedsToResize($uri);
        $fileName = $uriPop . '.' . $extension;
        $sizeFolder = $checkIfNeedsToResize ? array_pop($uri) : null;
        $uri = implode('/', $uri);
        $oldFile = $uriPop . '.' . $this->getFileExtension($uri, $uriPop);
        $newPath = $disk->path($uri . (!is_null($sizeFolder) ? '/' . $sizeFolder : null));
        if (!file_exists($newPath)) {
            File::makeDirectory($newPath);
        }
        $image = Image::make($disk->path($uri . '/' . $oldFile));
        if ($checkIfNeedsToResize) {
            $dimensions = explode('x', $sizeFolder);
            $image = $image->resize(Arr::first($dimensions), Arr::last($dimensions));
        }
        $image = $image->encode($extension, 85)
                       ->save($newPath . '/' . $fileName, 85, $extension);

        return $image->response($extension);
    }

    /**
     * @param $uri
     * @return bool
     */
    protected function checkIfNeedsToResize($uri) {
        $last = last($uri);
        $resolutions = explode('x', $last);

        return count($resolutions) > 1 && preg_match('~[0-9]+~', $last);
    }

    /**
     * @param $uri
     * @param $uripop
     * @return |null
     * @throws Exception
     */
    protected function getFileExtension($uri, $uripop) {
        $mimeTypes = config("media.validate.mimes");
        $fileUri = $uri . DIRECTORY_SEPARATOR . $uripop;
        $mimeTypes = explode(',', $mimeTypes);
        $extension = null;
        foreach ($mimeTypes as $mimeType) {
            if (is_null($extension)) {
                $extension = file_exists($fileUri . '.' . $mimeType) ? $mimeType : null;
            }
        }
        if (is_null($extension)) {
            throw new Exception('File does not exist.');
        }

        return $extension;
    }


}
