<?php declare(strict_types = 1);

namespace Orisai\Exceptions\Logic;

use Orisai\Exceptions\LogicalException;

final class NotImplemented extends LogicalException
{

	public static function create(): self
	{
		return new self();
	}

}
