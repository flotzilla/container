# Container
Dependency injection component

## Requirements

`php > 7.1`

## Install
via Composer

```bash
$ composer install bbyte/container
```

## Usage

Init container with constructor
```php
 $containerStack = new \bbyte\Container\Container(
     [
         'ConfigDI' => function () use ($x) { return new \stdClass($x);},  // some Service unique DI key 
         'AnotherDi' => function () { return new Service;}
     ]
 );
```

Or with setter 
```php
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
public function __construct(    
        ContainerInterface $container
    ){
    $container->get('ServiceDI'); // some Service unique DI key    
}
}
```

## Testing

```bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](https://github.com/flotzilla/container/blob/master/LICENCE.md) for more information.
