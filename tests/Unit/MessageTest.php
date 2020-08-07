<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use Orisai\Exceptions\Logic\ShouldNotHappen;
use Orisai\Exceptions\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{

	public function testMinimal(): void
	{
		$message = Message::create()
			->withContext('context');

		self::assertSame('Context: context', (string) $message);

		$message = Message::create()
			->withProblem('problem');

		self::assertSame('Problem: problem', (string) $message);

		$message = Message::create()
			->withSolution('solution');

		self::assertSame('Solution: solution', (string) $message);

		$message = Message::create()
			->withContext('context')
			->withProblem('problem')
			->withSolution('solution');

		self::assertSame(
			<<<'MSG'
Context: context
Problem: problem
Solution: solution
MSG
			,
			(string) $message
		);
	}

	public function testMultiLine(): void
	{
		$message = Message::create()
			->withContext('This is really, really, really long context. Lorem ipsum dolor sit amet. I don\'t know what more to write.')
			->withProblem('This is really, really, really long problem. Lorem ipsum dolor sit amet. I don\'t know what more to write.')
			->withSolution('This is really, really, really long solution. Lorem ipsum dolor sit amet. I don\'t know what more to write. But result looks really nice.');

		self::assertSame(
			<<<'MSG'
Context: This is really, really, really long context. Lorem ipsum dolor sit
         amet. I don't know what more to write.
Problem: This is really, really, really long problem. Lorem ipsum dolor sit
         amet. I don't know what more to write.
Solution: This is really, really, really long solution. Lorem ipsum dolor sit
          amet. I don't know what more to write. But result looks really nice.
MSG
			,
			(string) $message
		);
	}

	public function testPreformatted(): void
	{
		$message = Message::create()
			->withContext(<<<'MSG'
This message
is already
formatted into
multiple lines.
MSG
			);

		self::assertSame(
			<<<'MSG'
Context: This message
         is already
         formatted into
         multiple lines.
MSG
			,
			(string) $message
		);
	}

	public function testException(): void
	{
		$message = Message::create()
			->withContext('context');

		$exception = ShouldNotHappen::create()
			->withMessage((string) $message);

		self::assertSame(
			'Context: context',
			$exception->getMessage()
		);
	}

}
