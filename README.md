# think-jwt
基于thinkphp6的JWT权限验证插件

### 支持特性
- hash对称加密
- OpenSSL非对称加密
- 自动刷新token
- header或body获取

### 安装

```shell
composer require heimo/think-jwt
```

## Example

### 配置文件`config/jwt.php`，公私玥使用openssl生成

```shell
$ openssl
$ OpenSSL> genrsa -out rsa_private_key.pem 1024
$ OpenSSL> rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
```

### 应用中`middleware.php`注册jwt校验中间件

```php
<?php
// 全局中间件定义文件
return [

    ...

    \heimo\auth\middleware\Jwt::class //增加这行
];
```

### 生成token

```php
Auth::make()->generateToken();
```