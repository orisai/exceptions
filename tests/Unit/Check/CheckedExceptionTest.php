<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit\Check;

use PHPUnit\Framework\TestCase;
use Tests\Orisai\Exceptions\Doubles\ExampleUserError;

final class CheckedExceptionTest extends TestCase
{

	/**
	 * @throws ExampleUserError
	 */
	public function testAnnotated(): void
	{
		$this->expectException(ExampleUserError::class);
		$this->expectExceptionCode(0);
		$this->expectExceptionMessage('');

		throw new ExampleUserError();
	}

	public function testNotAnnotated(): void
	{
		$this->expectException(ExampleUserError::class);
		$this->expectExceptionCode(0);
		$this->expectExceptionMessage('');

		throw new ExampleUserError();
	}

}
