# Exceptions

Base exceptions designed for static analysis and easy usage

## Content

- [ConfigurableException](#configurableexception)
- [CheckedException](#checkedexception)
- [UncheckedException](#uncheckedexception)
- [Messages](#messages)
- [Exception suffix](#exception-suffix)
- [PHPStan integration](#phpstan-integration)

## ConfigurableException

All of our exceptions use `ConfigurableException` trait which allows to add message, code and previous exception through fluent interface.

```php
throw (new ExampleError())
    ->withMessage('I am error message')
    ->withPrevious($previousException)
    ->withCode(666);
```

It nicely works in combination with static constructor which is implemented by all [unchecked exceptions](#uncheckedexception)
and which is recommended to implement by [checked exceptions](#checkedexception)

```php
throw (new ExampleError())
    ->withMessage('I am error message');
```

turns into

```php
throw ExampleError::create()
    ->withMessage('I am error message');
```

## CheckedException

Exceptions which are used to represent a single domain-specific error caused by user interaction.
Checked exceptions are intended to be handled. They should always be catched or listed in annotations and catched in higher layers.
All of them must implement interface `CheckedException`.
You may also extend `DomainException` which implements `CheckedException`, disables default constructor and uses `ConfigurableException` trait.

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

If you want add message, code or previous exception to error you can use [fluent interface](#configurableexception).

## UncheckedException

Generic exceptions used for errors in code which should likely be fixed. They should have at least error message.
All of them must implement interface `UncheckedException`.
You may also extend `LogicalException` which implements `UncheckedException`, disables default constructor and uses `ConfigurableException` trait.

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

$message = Message::create()
    ->withContext('Trying to commit an import.')
    ->withProblem('There is nothing to commit.')
    ->withSolution('Check that the import files are not empty, and that filters are not too restrictive.');

throw InvalidState::create()
    ->withMessage((string) $message);
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
- messages longer than 80 characters (including description) are formatted into multiple lines
    - messages which already contain newlines are respected and are not reformatted

## Exception suffix

Why there is no `Exception` suffix? `InvalidState` instead of `InvalidStateException`? There are several reasons:

- IDEs are smart. In catch statement and after throw should be hinted only classes which implement Throwable,
typing the `Exception` part should not be required
- Suffix often lead us to write inaccurate exception names. While `ValidationException` could seem reasonable at first,
what should we except from class `Validation`? Exception name describes where is the problem but not what is the problem.
It forces us to think about the name. What about `InvalidData`? Now source of the problem is obvious which makes the suffix superfluous.

## PHPStan integration

We use [phpstan-exception-rules](https://github.com/pepakriz/phpstan-exception-rules) to check all checked exceptions are properly handled.

Install package and add following configuration:

```yaml
parameters:
    exceptionRules:
        reportUnusedCatchesOfUncheckedExceptions: true
        checkedExceptions:
            - Orisai\Exceptions\Check\CheckedException
```
