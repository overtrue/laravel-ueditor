<?php
/**
 * StorageManager.php.
 *
 * This file is part of the laravel-ueditor.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Overtrue\LaravelUEditor;

use Illuminate\Http\Request;
use Illuminate\Support\Manager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class StorageManager.
 */
class StorageManager extends Manager
{
    /**
     * Upload a file.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $config = $this->getUploadConfig($request->get('action'));

        if (!$request->hasFile($config['field_name'])) {
            return $this->error('UPLOAD_ERR_NO_FILE');
        }

        $file = $request->file($config['field_name']);

        if ($error = $this->fileHasError($file, $config)) {
            return $this->error($error);
        }

        $filename = $this->getFilename($file, $config);

        $response = [
            'state'    => 'SUCCESS',
            'url'      => $this->getUrl($filename),
            'title'    => $filename,
            'original' => $file->getClientOriginalName(),
            'type'     => $file->getExtension(),
            'size'     => $file->getSize(),
        ];

        try {
            $this->store($file, $filename);
        } catch (StoreErrorException $e) {
            return $this->error($e->getMessage());
        }

        return response()->json($response);
    }

    /**
     * List all files of dir.
     *
     * @param string $path
     * @param int    $start
     * @param int    $size
     * @param array  $allowFiles
     *
     * @return Response
     */
    public function listFiles($path, $start, $size = 20, array $allowFiles = [])
    {
        $files = $this->lists($path, $start, $size, $allowFiles);

        return [
            'state' => empty($files) ? 'no match file' : 'SUCCESS',
            'list'  => $files,
            'start' => $start,
            'total' => count($files),
        ];
    }

    /**
     * Return default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['ueditor.disk'];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function createLocalDriver()
    {
        return new LocalStorage($this);
    }

    /**
     * Make qiniu storage.
     *
     * @return \Overtrue\LaravelUEditor\QiNiuStorage
     */
    public function createQiniuDriver()
    {
        return new QiNiuStorage($this);
    }

    /**
     * Validate the input file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param array                                               $config
     *
     * @return bool|string
     */
    public function fileHasError(UploadedFile $file, array $config)
    {
        $error = false;

        if (!$file->isValid()) {
            $error = $file->getError();
        } elseif ($file->getSize() > $config['max_size']) {
            $error = 'upload.ERROR_SIZE_EXCEED';
        } elseif (!empty($config['allow_files']) &&
            !in_array('.'.$file->guessExtension(), $config['allow_files'])) {
            $error = 'ERROR_TYPE_NOT_ALLOWED';
        }

        return $error;
    }

    /**
     * Get the new filename of file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param array                                               $config
     *
     * @return string
     */
    protected function getFilename(UploadedFile $file, array $config)
    {
        $ext = '.'.($file->getClientOriginalExtension() ?: $file->guessClientExtension());

        return str_finish($this->formatPath($config['path_format']), '/').md5($file->getFilename()).$ext;
    }

    /**
     * Get configuration of current action.
     *
     * @param string $action
     *
     * @return array
     */
    public function getUploadConfig($action)
    {
        $upload = config('ueditor.upload');

        $prefixes = [
            'image', 'scrawl', 'snapscreen', 'catcher', 'video', 'file',
            'imageManager', 'fileManager',
        ];

        $config = [];

        foreach ($prefixes as $prefix) {
            if ($action == $upload[$prefix.'ActionName']) {
                $config = [
                    'action'      => array_get($upload, $prefix.'ActionName'),
                    'field_name'  => array_get($upload, $prefix.'FieldName'),
                    'max_size'    => array_get($upload, $prefix.'MaxSize'),
                    'allow_files' => array_get($upload, $prefix.'AllowFiles', []),
                    'path_format' => array_get($upload, $prefix.'PathFormat'),
                ];
                break;
            }
        }

        return $config;
    }

    /**
     * Make error response.
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message)
    {
        return response()->json(['state' => trans("ueditor::upload.{$message}")]);
    }

    /**
     * Format the storage path.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function formatPath($path)
    {
        $time = time();
        $partials = explode('-', date('Y-y-m-d-H-i-s'));
        $replacement = ['{yyyy}', '{yy}', '{mm}', '{dd}', '{hh}', '{ii}', '{ss}'];
        $path = str_replace($replacement, $partials, $path);
        $path = str_replace('{time}', $time, $path);

        //替换随机字符串
        if (preg_match("/\{rand\:([\d]*)\}/i", $path, $matches)) {
            $length = min($matches[1], strlen(PHP_INT_MAX));
            $path = preg_replace("/\{rand\:[\d]*\}/i", str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT), $path);
        }

        return $path;
    }
}
