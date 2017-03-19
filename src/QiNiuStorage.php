<?php
/**
 * QiNiuStorage.php.
 *
 * This file is part of the laravel-ueditor.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Overtrue\LaravelUEditor;

use Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class QiNiuStorage.
 */
class QiNiuStorage implements StorageInterface
{
    /**
     * Store file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string $filename
     *
     * @return mixed
     */
    public function store(UploadedFile $file, $filename)
    {
        Storage::disk('qiniu')->writeStream($filename, fopen($file->getRealPath(), 'r'));
    }

    /**
     * List files of path.
     *
     * @param string $path
     * @param int $start
     * @param int $size
     * @param array $allowFiles
     *
     * @return array
     */
    public function lists($path, $start, $size = 20, array $allowFiles = [])
    {
        $contents = Storage::disk('qiniu')->listContents($path,true);
        return collect($contents)->map(function ($file) {
            $files['url'] = $this->getUrl('/'.$file['path']);
            $files['mtime'] = $file['timestamp'];
            return $files;
        });
    }

    /**
     * Make the url of file.
     *
     * @param $filename
     *
     * @return string
     */
    public function getUrl($filename)
    {
        return 'http://' . config('filesystems.disks.qiniu.domain') . $filename;
    }
}
