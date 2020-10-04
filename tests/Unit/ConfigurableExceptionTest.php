<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use Exception;
use Orisai\Exceptions\Logic\ShouldNotHappen;
use Orisai\Exceptions\Message;
use PHPUnit\Framework\TestCase;

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

}
