# Version 1.2.0

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