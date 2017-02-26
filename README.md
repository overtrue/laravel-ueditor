# Laravel-UEditor

UEditor integration for Laravel 5.

# 使用

## 安装

```shell
$ composer require "overtrue/laravel-ueditor:~1.0"
```

## 配置

1. 添加下面一行到 `config/app.php` 中 `providers` 部分：

    ```php
    Overtrue\LaravelUEditor\UEditorServiceProvider::class,
    ```

2. 发布配置文件与资源

    ```php
    $ php artisan vendor:publish
    ```

3. 模板引入编辑器

    这行的作用是引入编辑器需要的 css,js 等文件，所以你不需要再手动去引入它们。

    ```php
    @include('vendor.ueditor.assets')
    ```

4. 编辑器的初始化

    ```html
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
        });
    </script>

    <!-- 编辑器容器 -->
    <script id="container" name="content" type="text/plain"></script>
    ```

# 说明

在 `config/ueditor.php` 配置 `disk` 为 `'local'` 情况下，上传路径在：`public/uploads/` 下，确认该目录存在并可写。

如果要修改上传路径，请在 `config/ueditor.php` 里各种类型的上传路径，但是都在 public 下。

# 七牛支持

如果你想使用七牛云储存，需要进行下面几个简单的操作：

1.配置 `config/ueditor.php` 的 `disk` 为 `qiniu`:

```php
'disk' => 'qiniu'
```

2.在 `config/filesystems.php` 添加下面的配置：

```php
's3' => [
        'driver' => 's3',
        'key' => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
        'region' => env('AWS_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
    
// 下面是添加的配置

'qiniu' => [
    'protocol' => 'http', // 域名对于的协议 http 或 https，默认 http
    'bucket' => env('QINIU_BUCKET_NAME'), // 七牛存储空间名字（bucket name），推荐使用公开空间
    'domain' => env('QINIU_BUCKET_DOMAIN'), // 七牛分配的域名
    'key' => env('QINIU_ACCESS_KEY'),
    'secret' => env('QINIU_SECRET_KEY'),
],
```

3.在 `.env` 文件添加配置：

```php
QINIU_BUCKET_NAME=
QINIU_BUCKET_DOMAIN=
QINIU_ACCESS_KEY=
QINIU_SECRET_KEY=
```
> 七牛的 `access_key` 和 `secret_Key` 可以在这里找到：https://portal.qiniu.com/user/key ,在创建 `bucket`
（空间）的时候，推荐大家都使用公开的空间。

# License

MIT
