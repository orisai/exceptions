# Exceptions

Exceptions designed for static analysis and easy usage

## Content

- [Setup](#setup)
- [Fluent interface](#fluent-interface)
- [Types of exceptions](#types-of-exceptions)
	- [Checked exception](#checked-exception)
	- [Unchecked exception](#unchecked-exception)
- [Messages](#messages)
	- [Line length](#line-length)
	- [Custom fields](#custom-fields)
- [Suppressed exceptions](#suppressed-exceptions)
- [Exception suffix](#exception-suffix)
- [Exceptions as part of the function signature](#exceptions-as-part-of-the-function-signature)
	- [PHPStan exception rules](#phpstan-exception-rules)

## Setup

Install with [Composer](https://getcomposer.org)

```sh
composer require orisai/exceptions
```

## Fluent interface

All of our exceptions use `ConfigurableException` trait which allows to add message, code and previous exception through fluent interface.

```php
throw (new ExampleError())
    ->withMessage('Error message')
    ->withPrevious($previousException)
    ->withCode(666);
```

It nicely works in combination with static constructor which is implemented by all [unchecked exceptions](#unchecked-exception)
and which is recommended to implement by [checked exceptions](#checked-exception)

```php
throw (new ExampleError())
    ->withMessage('Error message');
```

turns into

```php
throw ExampleError::create()
    ->withMessage('Error message');
```

## Types of exceptions

### Checked exception

Exceptions which are used to represent an error caused by user interaction.
- All of them must implement interface `CheckedException` and should extend `\RuntimeException`.
    - You may also extend `DomainException` which implements `CheckedException`, extends `\RuntimeException`, disables default constructor and uses `ConfigurableException` trait.
- Checked exceptions are intended to be handled. They should always be catched or listed in annotations and catched in higher layers.
- The way they are handled is up to you - report the error to user, use a fallback strategy, log the error, or a combination thereof.
- They must be [part of the function signature](#exceptions-as-part-of-the-function-signature).
- They should be always as specific as possible.
    - e.g. `ExpiredToken` exception which implements `InvalidToken` interface allows more granular handling than `InvalidToken` exception which does not explain what exactly is wrong with the token.
    - In case exception is part of an interface then interface signature should specify supertype of exception (`InvalidToken`) which implementations can throw instead of subtypes (`ExpiredToken`, `UnknownToken`, `AlreadyAppliedToken`).

```php
use Orisai\Exceptions\DomainException;

final class AccountBalanceTooLow extends DomainException
{

    private Account $account;
    private Money $neededAmount;

    public static function create(Account $account, Money $neededAmount): self
    {
        $self = new self();
        $self->account = $account;
        $self->neededAmount = $neededAmount;

        return $self;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getNeededAmount(): Money
    {
        return $this->neededAmount;
    }

}
```

If you want add message, code or previous exception to error you can use [fluent interface](#fluent-interface).

### Unchecked exception

Generic exceptions used for programming errors which should likely be fixed.
- All of them must implement interface `UncheckedException`.
    - You may also extend `LogicalException` which implements `UncheckedException`, extends `\LogicException`, disables default constructor and uses `ConfigurableException` trait.
- They should have at least error message.
- In perfectly written code they should never occur.
- Handling of unchecked exceptions should be done by an error handler, e.g. [Tracy debugger](https://tracy.nette.org).
    - Only valid reason to catch them is to add some additional debug info. In this case new exception must be thrown with original exception added as previous (`$new->withPrevious($previous)`).
- They should not be [part of the function signature](#exceptions-as-part-of-the-function-signature).
- Unless the exception subclass covers a common use case, e.g. adds useful info via name or property or the exception has valid reason to be catched then an existing uncatched exception should be used.

We currently provide following unchecked exceptions:

- `LogicalException` - generic, base exception, must be extended
- `Deprecated` - method is no longer supported, implementation was removed
- `InvalidArgument` - argument does not match with expected value
- `InvalidState` - method call is invalid for the object's current state
- `MemberInaccessible` - property or method is not accessible - not visible from calling scope nor by magic method
- `NotImplemented` - method is not implemented
- `ShouldNotHappen` - for cases which should never happen, but it's safer or easier to read with that "dead" branch of code

## Messages

Programming errors (aka unchecked exceptions) should be as consistent and descriptive as possible. `Message` helps you with that by defining interface.

```php
use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Message;

$message = Message::create()
    ->withContext('Trying to commit an import.')
    ->withProblem('There is nothing to commit.')
    ->withSolution('Check that the import files are not empty, and that filters are not too restrictive.');

throw InvalidState::create()
    ->withMessage($message);
```

`Message` casted to string looks like this:

```
Context: Trying to commit an import.
Problem: There is nothing to commit.
Solution: Check that the import files are not empty, and that filters are not
          too restrictive.
```

- context, problem and solution are always in the same order
- only specified parts are rendered

### Line length

Messages longer than 80 characters (including description) are formatted into multiple lines, except these messages
which already contain newlines.

To change the default line length, use `$lineLength` property, `Message::$lineLength = 120;`.

### Custom fields

Unique information above scope of Context-Problem-Solution can be added via `with()` method.

```php
use Orisai\Exceptions\Message;

Message::create()
	->withContext('Message with custom fields.')
	->with('Error hash', 'value');
```

```
Context: Message with custom fields.
Error hash: value
```

## Suppressed exceptions

Aggregate multiple exceptions into one.
Useful for handling unreliable subsystems whose crash should not stop processing by other subsystems.

Feature can be activated by using `ConfigurableException` trait or any of the exceptions from this package.

```php
use Orisai\Exceptions\Logic\ShouldNotHappen;
use Throwable;

$suppressed = [];

foreach ($this->runners as $runner) {
	try {
		$runner->execute($task);
	} catch (Throwable $exception) {
		$suppressed[] = $exception;
	}
}

if ($suppressed !== []) {
	throw ShouldNotHappen::create()
		->withMessage('Some of the runners failed during task execution.')
		->withSuppressed($suppressed);
}
```

Message of exception is an aggregation of its own and suppressed exceptions messages.

```txt
Some of the runners failed during task execution.
Suppressed errors:
    - Error created at /path/to/FooRunner.php:38 with code 0
    Error

    - Exception created at /path/to/BarRunner.php:97 with code 0
    <NO MESSAGE>

    - Orisai\Exceptions\Logic\InvalidState created at /path/to/BazRunner.php:51 with code 0
    Problem: problem
    Solution: solution
```

Suppressed exceptions can also be accessed via `$exception->getSuppressed()`.

## Exception suffix

Why there is no `Exception` suffix? `InvalidState` instead of `InvalidStateException`? There are several reasons:

- IDEs are smart. In catch statement and after throw should be hinted only classes which implement Throwable,
typing the `Exception` part should not be required
- Suffix often lead us to write inaccurate exception names. While `ValidationException` could seem reasonable at first,
what should we except from class `Validation`? Exception name describes where is the problem but not what is the problem.
It forces us to think about the name. What about `InvalidData`? Now source of the problem is obvious which makes the suffix superfluous.

## Exceptions as part of the function signature

One of the main reasons why [checked exceptions](#checked-exception) exist is they provide an easy way how to enforce user errors to be added into function signature.

Enforcement is achieved via static analysis. Officially supported are [PHPStan exception rules](#phpstan-exception-rules) but other tools may support that as well.
Only requirement is to configure thrown `CheckedException` to be either catched or added into method signature.

This approach will not work in case code is not called directly and caller don't know which code will be executed.
Usual cases where this happen are controller actions executed by router, events dispatched by event dispatcher or message handlers executed by message bus.
In these cases the called code should never throw any exceptions unless they are part of the interface which is known by calling code.

### PHPStan exception rules

We use [PHPStan](https://phpstan.org) built-in [exception rules](https://phpstan.org/blog/bring-your-exceptions-under-control) to ensure all checked exceptions are properly handled.

Add following configuration to your `phpstan.neon`:

```neon
parameters:
	exceptions:
		check:
			missingCheckedExceptionInThrows: true
			tooWideThrowType: true
		checkedExceptionClasses:
			- Orisai\Exceptions\Check\CheckedException
```
