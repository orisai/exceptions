<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use Exception;
use Orisai\Exceptions\DomainException;
use Orisai\Exceptions\Logic\InvalidArgument;
use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Logic\ShouldNotHappen;
use Orisai\Exceptions\LogicalException;
use Orisai\Exceptions\Message;
use PHPUnit\Framework\TestCase;
use Tests\Orisai\Exceptions\Generators\ClassExceptionGenerator;
use function realpath;
use function str_replace;
use const DIRECTORY_SEPARATOR;

final class ConfigurableExceptionTest extends TestCase
{

	public function testFluent(): void
	{
		$previous = new Exception('previous');

		$exception = ShouldNotHappen::create()
			->withCode(666)
			->withMessage('test')
			->withPrevious($previous);

		self::assertSame(666, $exception->getCode());
		self::assertSame('test', $exception->getMessage());
		self::assertSame($previous, $exception->getPrevious());
	}

	public function testStringable(): void
	{
		$message = Message::create()
			->withContext('context');

		$exception = ShouldNotHappen::create()
			->withMessage($message);

		self::assertSame(
			'Context: context',
			$exception->getMessage(),
		);
	}

	public function testSuppressed(): void
	{
		$suppressed = [
			$a = new Exception('Error'),
		];

		$suppressed2 = [
			$b = new Exception(''),
			$c = new Exception(
				Message::create()
					->withProblem('problem')
					->withSolution('solution')
					->toString(),
			),
			$d = InvalidArgument::create()->withMessage('foo'),
		];

		$exception = ShouldNotHappen::create()
			->withMessage('Oh no! This should not happen.')
			->withSuppressed($suppressed)
			->withSuppressed($suppressed2);

		self::assertSame(
			[$a, $b, $c, $d],
			$exception->getSuppressed(),
		);

		self::assertSame(
			<<<'MSG'
Oh no! This should not happen.
Suppressed errors:
- Exception created at /path/to/ConfigurableExceptionTest.php:52 with code 0
  Error

- Exception created at /path/to/ConfigurableExceptionTest.php:56 with code 0
  <NO MESSAGE>

- Exception created at /path/to/ConfigurableExceptionTest.php:57 with code 0
  Problem: problem
  Solution: solution

- Orisai\Exceptions\Logic\InvalidArgument created at /path/to/ConfigurableExceptionTest.php:63 with code 0
  foo
MSG,
			str_replace(__DIR__ . DIRECTORY_SEPARATOR, '/path/to/', $exception->getMessage()),
		);
	}

	public function testSuppressedAddedBeforeMessage(): void
	{
		$suppressed = new Exception('error');

		$exception = ShouldNotHappen::create()
			->withSuppressed([$suppressed]);

		self::assertSame(
			<<<'MSG'
Suppressed errors:
- Exception created at /path/to/ConfigurableExceptionTest.php:99 with code 0
  error
MSG,
			str_replace(__DIR__ . DIRECTORY_SEPARATOR, '/path/to/', $exception->getMessage()),
		);

		$exception->withMessage('message');

		self::assertSame(
			<<<'MSG'
message
Suppressed errors:
- Exception created at /path/to/ConfigurableExceptionTest.php:99 with code 0
  error
MSG,
			str_replace(__DIR__ . DIRECTORY_SEPARATOR, '/path/to/', $exception->getMessage()),
		);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSuppressedMessageDisabled(): void
	{
		// Parent of ShouldNotHappen
		LogicalException::$addSuppressedToMessage = false;

		// Has same property as LogicalException from trait, verify trait behavior is just copy-pasted with default value
		self::assertTrue(DomainException::$addSuppressedToMessage);

		$exception = ShouldNotHappen::create()
			->withSuppressed([
				new Exception('error'),
			])->withMessage('message');

		self::assertSame(
			'message',
			$exception->getMessage(),
		);

		$exception = ShouldNotHappen::create()
			->withMessage('message')
			->withSuppressed([
				new Exception('error'),
			]);

		self::assertSame(
			'message',
			$exception->getMessage(),
		);
	}

	public function testSuppressedWithPrevious(): void
	{
		$exception = ShouldNotHappen::create()
			->withMessage('message')
			->withSuppressed([
				new Exception('error', 0, new Exception('previous from error')),
				new Exception(
					'another one',
					0,
					new Exception('previous from another one', 0, new Exception()),
				),
			]);

		self::assertSame(
			<<<'MSG'
message
Suppressed errors:
- Exception created at /path/to/ConfigurableExceptionTest.php:165 with code 0
  error
      - Exception created at /path/to/ConfigurableExceptionTest.php:165 with code 0
        previous from error

- Exception created at /path/to/ConfigurableExceptionTest.php:166 with code 0
  another one
      - Exception created at /path/to/ConfigurableExceptionTest.php:169 with code 0
        previous from another one
            - Exception created at /path/to/ConfigurableExceptionTest.php:169 with code 0
              <NO MESSAGE>
MSG,
			str_replace(__DIR__ . DIRECTORY_SEPARATOR, '/path/to/', $exception->getMessage()),
		);
	}

	public function testSuppressedCreationContext(): void
	{
		$path = realpath(__DIR__ . '/../Generators/FileExceptionGenerator.php');
		self::assertIsString($path);

		$e = require $path;
		self::assertInstanceOf(InvalidState::class, $e);
		self::assertSame(
			<<<'MSG'
Suppressed errors:
- Orisai\Exceptions\Logic\ShouldNotHappen created at /path/to/TestFile.php:8 with code 0
  test
MSG,
			str_replace($path, '/path/to/TestFile.php', $e->getMessage()),
		);

		$path = realpath(__DIR__ . '/../Generators/ClassExceptionGenerator.php');
		self::assertIsString($path);

		$e = ClassExceptionGenerator::create();
		self::assertSame(
			<<<'MSG'
Suppressed errors:
- Orisai\Exceptions\Logic\ShouldNotHappen created at /path/to/TestFile.php:15 with code 0
  test
MSG,
			str_replace($path, '/path/to/TestFile.php', $e->getMessage()),
		);
	}

}
