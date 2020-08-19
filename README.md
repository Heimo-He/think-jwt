# think-jwt
基于thinkphp6的JWT权限验证插件

### 支持特性
- hash对称加密
- OpenSSL非对称加密
- header或body获取
- 无需设置refreshToken，设置时间token自动更新

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

### 应用中对路由使用注册jwt校验中间件

```php
Route::group('hello', function(){
    
    ...
	
    Route::rule('hello/:name','hello');
    
})->middleware(\heimo\auth\middleware\Jwt::class);
```

### 生成token

```php
Auth::make()->generateToken();

Auth::make()->refreshToken();//手动刷新token
```
