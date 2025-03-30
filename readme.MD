### Facade and container 
An advanced container and facade architecture, The aim is to automatically bind dependency injections.

to install

```console
composer require zeus/facade
```


The short code for the using of the following
```php


use Zeus\Facade\AbstractFacade;
use Zeus\Facade\Container;

require_once 'vendor/autoload.php';

class Login extends AbstractFacade
{
    public static function getFacade(): string
    {
        return 'login';
    }
}


$container = new Container();
AbstractFacade::setContainer($container);


$container->bind(LoginServiceInterface::class, LoginService::class);
$container->bind('login', LoginController::class);


echo Login::login('john_doe', 'password123') . PHP_EOL; // "Login successful for john_doe at 2025-03-28 14:30:45"
```
The fake
```php

use \Zeus\Facade\Tests\stubs\Http;

Http::fake('get',function (){
    return 'fake';
})

echo Http::get(); //fake
```
or middleware 
```php

Http::middleware(function (string $method,array $parameters,Closure $next){
    return $next($method,$parameters);
});
```

