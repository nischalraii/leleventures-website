<?php

namespace App\Helper;

use Facade\FlareClient\Stacktrace\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class Reply
 * @package App\Classes
 */
class Files
{

    /**
     * @param $image
     * @param $dir
     * @param null $width
     * @param int $height
     * @param $crop
     * @return string
     * @throws \Exception
     */
    public static function upload($image, $dir, $width = null, $height = 800, $crop = false)
    {

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $image;
        $folder = $dir . '/';

        if (!$uploadedFile->isValid()) {
            throw new \Exception('File was not uploaded correctly');
        }

        $newName = self::generateNewFileName($uploadedFile->getClientOriginalName());

        $tempPath = public_path('user-uploads/temp/' . $newName);
        /** Check if folder exits or not. If not then create the folder */
        if (!\File::exists(public_path('user-uploads/' . $folder))) {
            $result = \File::makeDirectory(public_path('user-uploads/' . $folder), 0775, true);
        }

        $newPath = $folder . '/' . $newName;

        /** @var UploadedFile $uploadedFile */
        $uploadedFile->move(public_path('user-uploads/temp'), $newName);

        if (!empty($crop)) {
            // Crop image
            if (isset($crop[0])) {
                // To store the multiple images for the copped ones
                foreach ($crop as $cropped) {
                    $image = Image::make($tempPath);

                    if (isset($cropped['resize']['width']) && isset($cropped['resize']['height'])) {

                        $image->crop(floor($cropped['width']), floor($cropped['height']), floor($cropped['x']), floor($cropped['y']));

                        $fileName = str_replace('.', '_' . $cropped['resize']['width'] . 'x' . $cropped['resize']['height'] . '.', $newName);
                        $tempPathCropped = public_path('user-uploads/temp') . '/' . $fileName;
                        $newPathCropped = $folder . '/' . $fileName;

                        // Resize in Proper format
                        $image->resize($cropped['resize']['width'], $cropped['resize']['height'], function ($constraint) {
                            //$constraint->aspectRatio();
                            // $constraint->upsize();
                        });

                        $image->save($tempPathCropped);

                        \Storage::put($newPathCropped, \File::get($tempPathCropped), ['public']);

                        // Deleting cropped temp file
                        \File::delete($tempPathCropped);
                    }

                }
            } else {
                $image = Image::make($tempPath);
                $image->crop(floor($crop['width']), floor($crop['height']), floor($crop['x']), floor($crop['y']));
                $image->save();
            }

        }

        if (($width || $height)) {
            // Crop image
            $image = Image::make($tempPath);
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->save();
        }

        \Storage::put($newPath, \File::get($tempPath), ['public']);

        // Deleting temp file
        \File::delete($tempPath);

        return $newName;
    }

    public static function generateNewFileName($currentFileName)
    {
        $ext = strtolower(\File::extension($currentFileName));
        $newName = md5(microtime());

        if ($ext === '') {
            return $newName;
        }

        return $newName . '.' . $ext;
    }

    public static function deleteFile($image, $folder)
    {
        $dir = trim($folder, '/');
        $path = $dir . '/' . $image;
        if (!\File::exists(public_path($path))) {
            \Storage::delete($path);
        }

        return true;
    }

    /**
     * @param $image
     * @param $dir
     * @param null $width
     * @param int $height
     * @return string
     * @throws \Exception
     */
    public static function uploadResize($image, $dir, $width = null, $height = 800)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $image;
        $folder = $dir . '/';

        if (!$uploadedFile->isValid()) {
            throw new \Exception('File was not uploaded correctly');
        }

        $newName = self::generateNewFileName($uploadedFile->getClientOriginalName());

        /** Check if folder exits or not. If not then create the folder */
        if (!\File::exists(public_path('user-uploads/' . $folder))) {
            $result = \File::makeDirectory(public_path('user-uploads/' . $folder), 0775, true);
        }

        $newPath = $folder . '/' . $newName;
        
        $image = Image::make($image->path());

        $image->orientate();

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save(public_path('user-uploads/') . $newPath);
   
        return $newName;
    }

    public static function uploadLocalOrS3($uploadedFile, $dir)
    {
        if (!$uploadedFile->isValid()) {
            throw new \Exception('File was not uploaded correctly');
        }
        if (config('filesystems.default') === 'local') {
            
            $fileName = self::upload($uploadedFile, $dir, false, false, false);
            
            // self::storeSize($uploadedFile, $dir, $fileName);
            
            return $fileName;
        }
    }
}