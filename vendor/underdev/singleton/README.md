[![Latest Stable Version](https://poser.pugx.org/underdev/singleton/v/stable)](https://packagist.org/packages/underdev/singleton) [![Total Downloads](https://poser.pugx.org/underdev/singleton/downloads)](https://packagist.org/packages/underdev/singleton) [![Latest Unstable Version](https://poser.pugx.org/underdev/singleton/v/unstable)](https://packagist.org/packages/underdev/singleton)

Simple Singleton class

# Usage example

```

require_once( 'vendor/autoload.php' );

use underDEV\Utils\Singleton;

class Example extends Singleton {}

Example::get();

```