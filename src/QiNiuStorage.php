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

use Qiniu\Auth;
use Qiniu\Processing\Operation;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
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
        $uploadManager = new UploadManager();

        list($result, $error) = $uploadManager->putFile(
            $this->getQiNiuAuth()->uploadToken(config('filesystems.disks.qiniu.bucket')),
            basename($filename),
            $file->getRealPath()
        );

        if ($error !== null) {
            throw new StoreErrorException(trans('ERROR_UNKNOWN'));
        } else {
            // Anything todo here ?
        }
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
        $bucketManager = new BucketManager($this->getQiNiuAuth());

        list($iterms, $marker, $error) = $bucketManager->listFiles(config('filesystems.disks.qiniu.bucket'), '', '', $size);

        if ($error !== null) {
            throw new StoreErrorException(trans('ERROR_UNKNOWN'));
        } else {
            $files = [];
            foreach (collect($iterms)->sortBy('putTime', SORT_REGULAR, true)->toArray() as $file ) {
                $files[] = [
                    'url'   => $this->getQiNiuUrl($file['key']),
                    'mtime' => $file['putTime'],
                ];
            }

            return $files;
        }
    }

    /**
     * Make the url of file.
     *
     * @param $filename
     *
     * @return mixed
     */
    public function getUrl($filename)
    {
        return $this->getQiNiuUrl(basename($filename));
    }

    /**
     * Get QiNiu auth object.
     *
     * @return string
     */
    protected function getQiNiuAuth()
    {
        return new Auth(config('filesystems.disks.qiniu.key'), config('filesystems.disks.qiniu.secret'));
    }


    /**
     * Get QiNiu url base on file key.
     *
     * @param $key
     * @return string
     */
    protected function getQiNiuUrl($key)
    {
        return $this->getQiNiuOperation()->buildUrl($key, [], config('filesystems.disks.qiniu.protocol'));
    }

    /**
     * Get QiNiu operation object.
     *
     * @return Operation
     */
    protected function getQiNiuOperation()
    {
        return new Operation(config('filesystems.disks.qiniu.domain'));
    }

}
