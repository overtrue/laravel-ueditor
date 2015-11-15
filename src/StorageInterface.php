<?php
/**
 * StorageInterface.php.
 *
 * This file is part of the laravel-ueditor.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Overtrue\LaravelUEditor;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * Store file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string                                              $filename
     *
     * @return mixed
     */
    public function store(UploadedFile $file, $filename);

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
    public function lists($path, $start, $size = 20, array $allowFiles = []);

    /**
     * Make the url of file.
     *
     * @param $filename
     *
     * @return mixed
     */
    public function getUrl($filename);
}
