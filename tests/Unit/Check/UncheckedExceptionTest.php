<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Unit\Check;

use Orisai\Exceptions\Logic\Deprecated;
use Orisai\Exceptions\Logic\InvalidArgument;
use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Logic\MemberInaccessible;
use Orisai\Exceptions\Logic\NotImplemented;
use Orisai\Exceptions\Logic\ShouldNotHappen;
use PHPStan\Testing\TestCase;

final class UncheckedExceptionTest extends TestCase
{

	public function testDeprecatedException(): void
	{
		$this->expectException(Deprecated::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw Deprecated::create()
			->withMessage('test')
			->withCode(666);
	}

	public function testInvalidArgumentException(): void
	{
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw InvalidArgument::create()
			->withMessage('test')
			->withCode(666);
	}

	public function testInvalidStateException(): void
	{
		$this->expectException(InvalidState::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw InvalidState::create()
			->withMessage('test')
			->withCode(666);
	}

	public function testMemberInaccessibleException(): void
	{
		$this->expectException(MemberInaccessible::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw MemberInaccessible::create()
			->withMessage('test')
			->withCode(666);
	}

	public function testNotImplementedException(): void
	{
		$this->expectException(NotImplemented::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw NotImplemented::create()
			->withMessage('test')
			->withCode(666);
	}

	public function testShouldNotHappenException(): void
	{
		$this->expectException(ShouldNotHappen::class);
		$this->expectExceptionCode(666);
		$this->expectExceptionMessage('test');

		throw ShouldNotHappen::create()
			->withMessage('test')
			->withCode(666);
	}

}
