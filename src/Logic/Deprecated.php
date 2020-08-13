<?php declare(strict_types = 1);

namespace Orisai\Exceptions\Logic;

use Orisai\Exceptions\LogicalException;

final class Deprecated extends LogicalException
{

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new static();
	}

}
