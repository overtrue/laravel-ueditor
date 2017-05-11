# Laravel-UEditor

UEditor integration for Laravel 5.

# 使用

> 视频教程：https://www.laravist.com/series/awesome-laravel-packages/episodes/7

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
    $ php artisan vendor:publish --provider="Overtrue\LaravelUEditor\UEditorServiceProvider"
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

1. 在 `config/ueditor.php` 配置 `disk` 为 `'public'` 情况下，上传路径在：`public/uploads/` 下，确认该目录存在并可写。
2. 如果要修改上传路径，请在 `config/ueditor.php` 里各种类型的上传路径，但是都在 public 下。
3. 请在 `.env` 中正确配置 `APP_URL` 为你的当前域名，否则可能上传成功了，但是无法正确显示。

# 七牛支持

如果你想使用七牛云储存，需要进行下面几个简单的操作：

1.安装和配置 [laravel-filesystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu)

2.配置 `config/ueditor.php` 的 `disk` 为 `qiniu`:

```php
'disk' => 'qiniu'
```

3.剩下时间打局 LOL，已经完事了。

> 七牛的 `access_key` 和 `secret_key` 可以在这里找到：https://portal.qiniu.com/user/key ,在创建 `bucket` （空间）的时候，推荐大家都使用公开的空间。

# License

MIT
