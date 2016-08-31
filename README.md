# phpintegration
PHP library for writing automated tests. It can help you get started especially when you do TDD and value randomized testing.

![Build Status](https://travis-ci.org/ksamborski/phpintegration.svg?branch=master)

## Installation

```bash
composer install ksamborski/php-integration
```

## Basic usage
You write test. That's cool. And when you write test that uses random data that's even cooler. But what happens if your test find a bug? You fix it and tries again but the test is random and you cannot simple rerun it. You need to change the test's code, run it and when everything's ok hopefully not forget to remove your changes in test's code. You can also use this library.

First of all let's define some tests:

```php
use PHPIntegration\TestParameter;
use PHPIntegration\Test;
use PHPIntegration\Console;

$tests = [
    new Test(
        "Test1",
        "Simple test 1",
        function($p) {
            usleep(rand(10000, 100000));
            return true;
        }
    ),
    new Test(
        "Test2",
        "Failing test",
        function($p) {
            return "this is a test that always fails";
        }
    )
];

```
Test is a simple object that has a name, a description and a function that receives parameters. Don't worry about it now, we will cover it later. That function is your test, it should return true when everything's ok and some message explaining what is wrong otherwise.

Now to avoid changing test code when something fails let's introduce dynamic parameters:
```php

$params = function() {
    return [
        TestParameter::manyFromParameter("departments", ["Warsaw", "Berlin"], ["Warsaw", "Berlin", "Cracow"]),
        TestParameter::stringParameter("currency", "PLN"),
        TestParameter::regexParameter("date", "2015-01-01", "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/"),
        TestParameter::arrayOfParameter("hours", [12], '\PHPIntegration\TestParameter::intParameter')
    ];
};

```

These parameters are of course objects. Depending of your needs you can define a string parameter, regex one, parameter of predefined values etc. or custom one. Parameter usually takes a name and default value. This default value can be override at run time. I'll show you later.

One thing lacking is console. We need to initialize console interface (CLI) to get some way to use it.

```php
Console::main($tests, $params);
```

It takes two arguments: array of tests and function generating dynamic parameters. It is a function in the latter case because we need some way to randomize them again after each iteration when we will run some test n times.

Now let's run it:
```bash
php basic_example.php

Simple test 1 [ OK ] 36.46 ms

Failing test [ FAILED ] 0.00 ms
Parameters: 
- departments:[Warsaw,Berlin]
- currency:PLN
- date:2015-01-01
- hours:[12]
Message: 
this is a test that always fails
```

Now let's override some parameter:

```bash
php basic_example.php -p "currency:EUR"
Simple test 1 [ OK ] 38.27 ms

Failing test [ FAILED ] 0.00 ms
Parameters: 
- departments:[Warsaw,Berlin]
- currency:EUR
- date:2015-01-01
- hours:[12]
Message: 
this is a test that always fails
```

OK, but how to use them in a test? Remember the $p argument in tests that we defined previously? That's the parameters map. To read for example the currency you can write:

```php
new Test(
        "Test1",
        "Simple test 1",
        function($p) {
            return $p['currency'];
        }
    )
```

What if we forget what parameters we can pass? CLI for the rescue!

```bash
php basic_example.php -h
Usage: php basic_example.php [OPTIONS]

  -t, --test TEST_NAME                             Run only given tests (you can pass multiple -t option) 
  -p, --parameter PARAMETER_NAME:PARAMETER_VALUE   Set test parameter (you can pass multiple -p option) 
  -n                                               Number of repeats 

  -h, --help                                       Show this help

Available tests:
- Simple test 1
- Failing test

Available parameters:
- departments 
  Default: [Warsaw,Berlin]
- currency 
  Default: PLN
- date 
  Default: 2015-01-01
- hours 
  Default: [12]
```
As you see we can do many things. Isn't it great?

## Random

Next thing we should look at is random_example.php from the examples directory. Let's take a look at parameters:

```php
use PHPIntegration\Utils\RandomHelper;

$params = function() {
    return [
        TestParameter::manyFromParameter(
            "departments",
            RandomHelper::randomArray(["Warsaw", "Berlin", "Cracow"], false),
            ["Warsaw", "Berlin", "Cracow"]
        ),
        TestParameter::stringParameter("currency", RandomHelper::randomString(3)),
        TestParameter::arrayOfParameter(
            "hours",
            RandomHelper::randomMany(function() { return rand(1,24); },1),
            '\PHPIntegration\TestParameter::intParameter'
        )
    ];
};
```

You can see the same old TestParameter class but there is also RandomHelper. It contains many useful functions for generating random data. For example the randomArray function just generates array containing random elements from the provided list. The last argument decides whether it can contain duplicate values or not. Of course you can generate random string with randomString function and random array with randomMany.

But real the beauty is the CLI:

```bash
Warsaw test 6/100 [ FAILED ] 0.00 ms
Parameters: 
- departments:[]
- currency:rH/
- hours:[20,14,16,16,22,21]
Message: 
this test succeeds only if Warsaw is passed

Failing test 1/100 [ FAILED ] 20.08 ms > 10 ms limit
Parameters: 
- departments:[Cracow]
- currency:jYy
- hours:[22,5,3,13]
Message: 
this is a test that always fails
```

The n parameter to the script tells it to repeat execution of every test n times. Whenever one fails it stops repeating it and goes to next test. You can spot the "> 10 ms limit" in the second test case. This happened because it this test time limit was set. You can do it by providing third parameter to the Test class:

```php
    new Test(
        "Test2",
        "Failing test",
        function($p) {
            usleep(20000);
            return "this is a test that always fails";
        },
        10
    )
```
10 means the test should finish within 10 ms.

## Objects as parameters
So far we defined only string, int and array parameters. But we can do better. We can define objects! Unfortunately to do this we need to implement an interface. Take a look at object_example.php from the examples directory.

```php
use PHPIntegration\Testable;
use PHPIntegration\Utils\RandomHelper;
use PHPIntegration\Randomizable;

class TestObject implements Randomizable, Testable
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function build(string $value) : Testable
    {
        return new TestObject($value);
    }

    public static function validate(string $value, bool $valid = true)
    {
        $fstLetter = substr($value, 0, 1);
        if ($valid === true) {
            if (strtolower($fstLetter) == $fstLetter) {
                return "Value must start from upper case.\n";
            } else {
                return true;
            }
        } else {
            if (strtolower($fstLetter) == $fstLetter) {
                return true;
            } else {
                return "Value must not start from upper case.\n";
            }
        }
    }

    public function asStringParameter() : string
    {
        return $this->name;
    }

    public static function randomValid()
    {
        return new TestObject(strtoupper(RandomHelper::randomString()));
    }

    public static function randomInvalid()
    {
        return new TestObject(strtolower(RandomHelper::randomString()));
    }
}
```

To use object as parameter we need to implement only Testable interface. To make it random we need also implement Randomizable interface. There are 3 methods in the Testable interface: build, validate and asStringParameter. Build is easy, it just takes whatever user wrote in the -p option and must create an object from it. Validate method is executed just before it to make sure that this string makes sense. When not CLI will display error. And asStringParameter is used when test fails to show the parameter value that user can pass again (useful when object is not provided but randomized).

```bash
php object_example.php -p "first name:john"    
Bad param `first name` value `john`
Value must start from upper case.
```

Randomizable is much simpler. There are only 2 methods. One for generating object with valid data. For example when it would be a database connection string it would point to the existing database. And the other one for invalid data (for instance connection string to not existing database).

You can randomize object with randomObject method from the RandomHelper class. To use object as a parameter you need to use objectParameter method from the TestParameter class.

## Other things

You should definitely check the examples folder.
