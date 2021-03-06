# Version 2.0.0

## Usage

Init service container with constructor parameters
```php
 $container = new \flotzilla\Container\Container(
     [
         'EmptyTestClassDI' => EmptyTestClass::class, // class without dependencies, init by classname
         'TestClassDI' => [TestClass::class, 'message'], // class with constructor string parameter
         'TestClassDI2' => function () { // closure, that returns new class instance
             return new TestClass('test');
         },
        'ClosureDI' => function ($x, $y) { // closure, that returns sum result
             return $x + $y;
         },

         'TestClassWithDependecyDI' => [TestClassWithDependecy::class, 'TestClassDI'] // class with dependency of another service
     ]
 );
```

Or with setter
```php
use \flotzilla\Container\Container;
$container = new Container();

$container->set('LoggerDI', function () { return new Logger();});
$container->set('ClosureDI', function ($x, $y) { return $x + $y;});
$container->set('EmptyTestClassDI', ClassWithoutConstructor::class);
$container->set('ClassDIWithDependency', [SomeClass::class, 'message']);
$container->set('AnotherClassWithDIDependency', [TestClass::class, 'LoggerDI']);
```

Get your component in code
```php
$logger = $container->get('LoggerDI');
```

Get your closure with arguments
```php
$container->set('ClosureDI', function ($x, $y) { return $x + $y;});
$logger = $container->getWithParameters('ClosureDI', [1, 2]); // will return 3
```

