# 测试使用问题
## 1.上传没加入中间件控制

## 2.php7报错

                ErrorException in StorageManager.php line 217:
        
                rand() expects parameter 2 to be integer, float given
        
        
# 3.需开启phpinfo扩展


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

上传路径在：`public/uploads/` 下，确认该目录存在并可写。

如果要修改上传路径，请在 `config/ueditor.php` 里各种类型的上传路径，但是都在 public 下。

# License

MIT
