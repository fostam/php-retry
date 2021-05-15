# fostam/retry

With _Retry_, you can repeat the execution of any callable until it either succeeds, or the maximum
number of attempts has been reached. Several delay policies are available for determining the sleep
time between the attempts.

## Features
- Simple usage
- Different delay strategies
- Works with any PHP Callable
- No dependencies

## Install
The easiest way to install Retry is by using [composer](https://getcomposer.org/): 

```
$> composer require fostam/retry
```

## Usage

```php
try {
    // sleep 1 second between attempts
    $funcResult = Retry::onFailure('myfunc', 3, new ConstantDelayPolicy(1000));
}
catch (RetryLimitException $e) {
    // failed after maximum number of attempts
}

// success before maximum number of attempts have been exceeded
````

## Payload Callables
Payload callables can be functions, class/object methods or closures, as listed in the [PHP documentation](https://www.php.net/manual/en/language.types.callable.php).

### Failure Condition
Payload callables are considered to have failed if they either throw an `Exception` or return `false`.
If `false` is a legitimate result of your callable, use `onException()` instead of `onFailure()`,
in which case the return value of the callable will be ignored.

The original exception that was thrown in the payload callable is chained to the `RetryLimitException`
and can be retrieved via `$e->getPrevious()`.

You can also use the `onCondition()` method and pass a validation callable that takes the result
of the payload callable as argument and returns `true` or `false` to indicate whether the
original callable failed or not:

```php
$password = 'secret';
Retry::onCondition('getInput', function($result) use ($password) {
    return $result === $password;
}, 3);
````


### Passing Parameters
If you use a function or a class/object method as callable and need to pass parameters, you can
wrap the call with a closure:

```php
private function multiply($a, $b) {
    return $a * $b;
}

...

$x = 3;
$y = 4;
Retry::onFailure(function() use ($x, $y) {
    return $this->multiply($x, $y);
}, 3);
````

## Delay Policies
*Retry* sleeps between two payload calls. There are multiple policies available to determine
the time period of the delay. All numerical values for delay policies are interpreted
as milliseconds (ms).

### Constant Sleep Value
```php
// sleep 2000 milliseconds (i.e. 2 seconds) between attempts
$policy = new ConstantDelayPolicy(2000);
```

### Linear Sleep Value Increase
```php
// sleep 1 second after the first, 2 after the second, 3 after the third...
$policy = new LinearDelayPolicy(1000);
```

### Exponential Sleep Value Increase
```php
// sleep 1, 2, 4, 8, 16, ...
$policy = new ExponentialDelayPolicy(1000);

// sleep 1, 3, 9, 27, ...
$policy = new ExponentialDelayPolicy(1000, 3);
```

### Fixed Series of Sleep Values
```php
// sleep 2, 2, 4, 20, 20, 20, 20 ... (stick to last value after series has ended)
$policy = new SeriesDelayPolicy([2000, 2000, 4000, 20000]);

// sleep 2, 2, 4, 20, 2, 2, 4, 20, 2, 2, 4, ... (repeat series after it has ended)
$policy = new SeriesDelayPolicy([2000, 2000, 4000, 20000], true);
```

### Sleep with Random Jitter
```php
// sleep a random amount between 1900 and 2100 milliseconds
$policy = new JitterDelayPolicy(2000, 100);
```

### No Sleep
```php
// don't sleep at all
$policy = new NoneDelayPolicy();
```

## Advanced Features
### Omitting Delay Policy
In case you do not want any delay between the attempts, you can either use the
`NoneDelayPolicy` as described above, pass `NULL` as Delay Policy, or omit the Delay
Policy argument at all:

```php
// retry without delay
Retry::onFailure('myfunc', 3);
```

### Getting the Number of Attempts
If you need to get the number of attempts that were necessary for a successful payload call, e.g. for logging purposes,
you can pass a variable by reference to the `onFailure()`, `onException()` and `onCondition()`methods:

```php
Retry::onFailure('myfunc', 3, null, $tries);
print "success after {$tries} attempts";
```

### Abort the Retry Loop
To abort the retry loop without signalling "success", an `AbortException` can be thrown from inside the
payload function.
