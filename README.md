# Laravel-UEditor

UEditor integration for Laravel 5.

# 使用

## 安装

```shell
composer require "overtrue\laravel-ueditor"
```

## 配置

1. 添加下面一行到 `config/app.php` 中 `providers` 部分：

```php
Overtrue\LaravelUEditor\UEditorServiceProvider::class,
```

2. 发布配置文件与资源

```php
php artisan vendor:publish
```

3. 模板引入编辑器

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

TODO

# License

MIT
