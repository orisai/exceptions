<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Generators;

use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Logic\ShouldNotHappen;

final class ClassExceptionGenerator
{

	public static function create(): InvalidState
	{
		return InvalidState::create()
			->withSuppressed([
				ShouldNotHappen::create()->withMessage('test'),
			]);
	}

}
