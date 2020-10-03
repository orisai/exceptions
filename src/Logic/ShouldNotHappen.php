<?php declare(strict_types = 1);

namespace Orisai\Exceptions\Logic;

use Orisai\Exceptions\LogicalException;

final class ShouldNotHappen extends LogicalException
{

	public static function create(): self
	{
		return new self();
	}

}
