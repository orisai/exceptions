<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Orisai\Exceptions\Doubles\ConstructorInitializedDomainProblem;
use Tests\Orisai\Exceptions\Doubles\ConstructorInitializedLogicalProblem;

final class BaseExceptionsTest extends TestCase
{

	public function testProtectedConstructor(): void
	{
		$e = ConstructorInitializedDomainProblem::create('m');
		self::assertSame('m', $e->getMessage());

		$e = ConstructorInitializedLogicalProblem::create('m');
		self::assertSame('m', $e->getMessage());
	}

}
