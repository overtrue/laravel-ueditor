<?php
/**
 * LocalStorage.php.
 *
 * This file is part of the laravel-ueditor.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Overtrue\LaravelUEditor;

use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LocalStorage.
 */
class LocalStorage implements StorageInterface
{
    /**
     * Store file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string                                              $filename
     *
     * @return mixed
     *
     * @throws \Overtrue\LaravelUEditor\StoreErrorException
     */
    public function store(UploadedFile $file, $filename)
    {
        $directory = public_path(dirname($filename));

        if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
            throw new StoreErrorException(trans('ERROR_CREATE_DIR'));
        } elseif (!is_writable($directory)) {
            throw new StoreErrorException(trans('ERROR_DIR_NOT_WRITEABLE'));
        }

        try {
            return $file->move($directory, basename($filename));
        } catch (Exception $e) {
            throw new StoreErrorException(trans('ERROR_FILE_MOVE'));
        }
    }

    /**
     * List files of path.
     *
     * @param string $path
     * @param int    $start
     * @param int    $size
     * @param array  $allowFiles
     *
     * @return array
     */
    public function lists($path, $start, $size = 20, array $allowFiles = [])
    {
        $filesIterator = Finder::create()->files()->in(public_path($path))
            ->sortByChangedTime()
            ->filter(function ($file) use ($allowFiles) {
                if (empty($allowFiles)) {
                    return true;
                }

                return in_array('.'.pathinfo($file->getRelativePathname(), PATHINFO_EXTENSION), $allowFiles);
            });

        $files = [];

        foreach ($filesIterator as $file) {
            $files[] = [
                'url' => asset($path.'/'.$file->getRelativePathname()),
                'mtime' => $file->getMTime(),
            ];
        }

        return $files;
    }

    /**
     * Make the url of file.
     *
     * @param string $filename
     *
     * @return mixed
     */
    public function getUrl($filename)
    {
        return asset($filename);
    }
}
