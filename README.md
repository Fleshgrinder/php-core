[![Latest Stable Version](https://poser.pugx.org/fleshgrinder/core/v/stable)](https://packagist.org/packages/fleshgrinder/core)
[![License](https://poser.pugx.org/fleshgrinder/core/license)](https://packagist.org/packages/fleshgrinder/core)
[![Travis CI build status](https://img.shields.io/travis/Fleshgrinder/php-core.svg)](https://travis-ci.org/Fleshgrinder/php-core)
[![AppVeyor CI build status](https://ci.appveyor.com/api/projects/status/u5fbfnf7m3ws8l1b/branch/master?svg=true)](https://ci.appveyor.com/project/Fleshgrinder/php-core/branch/master)

[![Coveralls branch](https://img.shields.io/coveralls/Fleshgrinder/php-core/master.svg)](https://coveralls.io/github/Fleshgrinder/php-core)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/Fleshgrinder/php-core.svg)](https://scrutinizer-ci.com/g/Fleshgrinder/php-core/)
[![Code Climate: GPA](https://img.shields.io/codeclimate/github/Fleshgrinder/php-core.svg)](https://codeclimate.com/github/Fleshgrinder/php-core)
[![Total Downloads](https://poser.pugx.org/fleshgrinder/core/downloads)](https://packagist.org/packages/fleshgrinder/core)
# Core
The **core** library provides the most basic functionality that is missing in
 PHP core (and most probably will never make it into it): helpers (in form of
 traits) to disable default PHP object behavior.

- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)

## Installation
Open a terminal, enter your project directory and execute the following command
 to add this package to your dependencies:

```bash
composer require fleshgrinder/core
```

This command requires you to have Composer installed globally, as explained in
 the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the
 Composer documentation.

## Usage
### Disenchant
The [Disenchant](src/Disenchant.php) trait can be used to disable support for
 the magic `__get`, `__isset`, `__set`, and `__unset` methods. Every object in
 PHP by default supports these methods for dynamic property management. However,
 these are features that are actually undesired in almost all situations. While
 support for them does not lead to serious bugs, it can result in in weird
 behavior. Especially in the case of value objects when equality is determined
 with PHP’s equality operator (`==`).

Suppose we have a simple value object:

```php
final class ValueObject {
    private $value;
    public function __construct($value) {
        $this->value = $value;
    }
}
```

Using the equality operator to determine if two instances are equal or not is
 straight forward, the same is true for comparisons:

```php
$v0 = new ValueObject(0);
$v1 = new ValueObject(1);

var_dump(
    $v0 < $v1,  // true
    $v0 <= $v1, // true
    $v0 == $v1, // false
    $v0 >= $v1, // false
    $v0 > $v1   // false
);
```

If we now add a property to `$v0` the result will be different:

```php
$v0 = new ValueObject(0);
$v0->x = 42;
$v1 = new ValueObject(1);

var_dump(
    $v0 < $v1,  // false
    $v0 <= $v1, // false
    $v0 == $v1, // false
    $v0 >= $v1, // true
    $v0 > $v1   // true
);
```

Of course, these operators should not be used and instead specialized
 functionality like [Equalable](https://github.com/fleshgrinder/php-equalable)
 and [Comparable](https://github.com/fleshgrinder/php-comparable) should be
 used, but this trait adds another layer of safety to the whole program at
 almost no cost.

Other situations in which this trait is very handy is if your objects rely on
 the properties they have. For instance if `get_object_vars` is used somewhere.
 However, note that not all possibly weird behavior is fixed through the
 inclusion of this trait. It is, for instance, still possible to dynamically
 add properties via specially crafted `unserialize` strings. This is because
 PHP does not call any of these magic methods in that case but directly adds
 the properties to the object, validate your objects in your `__wakeup` or
 `unserialize` methods instead.

### Uncloneable
The [Uncloneable](src/Uncloneable.php) trait can be used to disable support for
 the `clone` keyword in client code. This is a good idea for objects that
 cannot be cloned for technical reasons, e.g. anything that encloses a
 resource of some kind like a database connection, or should not be cloned
 because it makes no sense, e.g. any kind of immutable implementation like
 value objects.

The magic `__clone` method is defined as _final_ and _protected_ in this trait,
 this ensures that subclasses of the class that uses the trait are not able to
 alter that contract. At the same time it allows the using class to use the
 clone functionality internally to provide copy-on-write support without
 breaking changes; as illustrated in the following example:

```php
final class URI {
    use Fleshgrinder\Core\Uncloneable;

    // ...

    public function withFragment(string $fragment): URI {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }
}
```

Another interesting use-case are friend classes paired with the builder pattern
 to provide immutable entities.

```php
abstract class EntityFriend {
    use Fleshgrinder\Core\Uncloneable;

    protected $value;

    protected function setValue(T $value): void {
        $this->value = $value;
    }
}

final class Entity extends EntityFriend {
    public function getValue(): T {
        return $this->value;
    }
}

final class EntityBuilder extends EntityFriend {
    private $entity;

    public function __construct() {
        $this->entity = new Entity;
    }

    public function build(): Entity {
        return clone $this->entity;
    }

    public function setValue(T $value): void {
        $this->entity->setValue($value);
    }
}
```

### Unconstructable
The [Unconstructable](src/Unconstructable.php) trait can be used to disable
 support for the `new` keyword in client code. This is almost always a good
 idea to [disable multiple constructor calls](https://wiki.php.net/rfc/disallow-multiple-constructor-calls)
 and enforce invariance for actual constructor method arguments. Of course, the
 class requires named constructors, otherwise construction would be impossible.

```php
final class SomeClass {
    use Fleshgrinder\Core\Unconstructable;

    public static function new(): self {
        return new static;
    }
}
```

Another use-case are _final abstract_ classes, which are not available in PHP.

```php
final class AbstractFinalClass {
    use Fleshgrinder\Core\Unconstructable;

    public static function f() { }

    public static function f′() { }

    // ...
}
```

### Immutable
The [Immutable](src/Immutable.php) is a combination of all other traits, and
 is provided for convenience only. It is best used for any kind of immutable
 class, as the name already suggests.

## Testing
Open a terminal, enter the project directory and execute the following commands
 to run the [PHPUnit](https://phpunit.de/) tests with your locally installed
 PHP executable.

```bash
make
```

You can also execute the following two commands, in case `make` is not
 available on our system:

```bash
composer install
composer test
```
