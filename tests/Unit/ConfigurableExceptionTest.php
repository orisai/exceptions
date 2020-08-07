<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use Exception;
use Orisai\Exceptions\Logic\ShouldNotHappen;
use PHPUnit\Framework\TestCase;

final class ConfigurableExceptionTest extends TestCase
{

	public function test(): void
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

}
