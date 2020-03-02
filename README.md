[![MIT License][license-shield]][license-url]

# Container
Dependency injection PHP FIG PSR-11 container implementation

## Requirements

`php > 7.1`

## Install
via Composer

```bash
$ composer install flotzilla/container
```

## Usage

Init service container with constructor parameters
```php
 $containerStack = new \flotzilla\Container\Container(
     [
         'ConfigDI' => function () use ($x) { return new \stdClass($x);},  // some Service unique DI key 
         'AnotherDi' => function () { return new Service;}
     ]
 );
```

Or with setter
```php
use \flotzilla\Container\Container;
$container = new Container();
$container->set('LoggerDI', function () { return new Logger();});
```

You can pass container to service as constructor argument
```php
$container->set('LoggerDI', function (ContainerInterface $container) {
    return new Logger($container);
});
```

Get your component in code
```php
class Controller
{
    private $container; 
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->get('ServiceDI'); // some Service unique DI key    
    }
}
```

## Testing

```bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](https://github.com/flotzilla/container/blob/master/LICENCE.md) for more information.

[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=flat-square
[license-url]: https://github.com/flotzilla/container/blob/master/LICENCE.md