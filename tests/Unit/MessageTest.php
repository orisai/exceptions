<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Logic\ShouldNotHappen;
use Orisai\Exceptions\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{

	public function testMinimal(): void
	{
		$message = Message::create()
			->withContext('context');

		self::assertSame('Context: context', $message->toString());

		$message = Message::create()
			->withProblem('problem');

		self::assertSame('Problem: problem', $message->toString());

		$message = Message::create()
			->withSolution('solution');

		self::assertSame('Solution: solution', $message->toString());

		$message = Message::create()
			->withContext('context')
			->withProblem('problem')
			->withSolution('solution');

		self::assertSame(
			<<<'MSG'
Context: context
Problem: problem
Solution: solution
MSG,
			$message->toString(),
		);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMultiLine(): void
	{
		$message = Message::create()
			->withContext(
				'This is really, really, really long context. Lorem ipsum dolor sit amet. I don\'t know what more to write.',
			)
			->withProblem(
				'This is really, really, really long problem. Lorem ipsum dolor sit amet. I don\'t know what more to write.',
			)
			->withSolution(
				// phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
				'This is really, really, really long solution. Lorem ipsum dolor sit amet. I don\'t know what more to write. But result looks really nice.',
			);

		self::assertSame(
			<<<'MSG'
Context: This is really, really, really long context. Lorem ipsum dolor sit
         amet. I don't know what more to write.
Problem: This is really, really, really long problem. Lorem ipsum dolor sit
         amet. I don't know what more to write.
Solution: This is really, really, really long solution. Lorem ipsum dolor sit
          amet. I don't know what more to write. But result looks really nice.
MSG,
			$message->toString(),
		);

		Message::$lineLength = 120;

		self::assertSame(
			<<<'MSG'
Context: This is really, really, really long context. Lorem ipsum dolor sit amet. I don't know what more to write.
Problem: This is really, really, really long problem. Lorem ipsum dolor sit amet. I don't know what more to write.
Solution: This is really, really, really long solution. Lorem ipsum dolor sit amet. I don't know what more to write. But
          result looks really nice.
MSG,
			$message->toString(),
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
MSG);

		self::assertSame(
			<<<'MSG'
Context: This message
         is already
         formatted into
         multiple lines.
MSG,
			$message->toString(),
		);
	}

	public function testMessageInsideMessage(): void
	{
		$innerMessage = Message::create()
			->withContext('inner context')
			->withProblem(<<<'MSG'
This message
is already
formatted into
multiple lines.
MSG)
			->withSolution('This is really, really, really long solution. Lorem ipsum dolor sit amet.');

		$outerMessage = Message::create()
			->withContext('outer context')
			->withProblem('This is really, really, really long problem. Lorem ipsum dolor sit amet.')
			->withSolution($innerMessage->toString());

		self::assertSame(
			<<<'MSG'
Context: outer context
Problem: This is really, really, really long problem. Lorem ipsum dolor sit
         amet.
Solution: Context: inner context
          Problem: This message
                   is already
                   formatted into
                   multiple lines.
          Solution: This is really, really, really long solution. Lorem ipsum dolor sit
                    amet.
MSG,
			$outerMessage->toString(),
		);
	}

	public function testException(): void
	{
		$message = Message::create()
			->withContext('context');

		$exception = ShouldNotHappen::create()
			->withMessage($message->toString());

		self::assertSame(
			'Context: context',
			$exception->getMessage(),
		);
	}

	public function testToString(): void
	{
		$message = Message::create()
			->withContext('context');

		self::assertSame(
			'Context: context',
			$message->toString(),
		);

		self::assertSame(
			(string) $message,
			$message->toString(),
		);
	}

	public function testMissingParameters(): void
	{
		$message = Message::create();

		$this->expectException(InvalidState::class);
		$this->expectExceptionMessage('Error message must specify at least one of context, problem or solution.');

		ShouldNotHappen::create()
			->withMessage($message->toString());
	}

}
