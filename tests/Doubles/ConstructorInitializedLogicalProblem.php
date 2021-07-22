<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Doubles;

use Orisai\Exceptions\LogicalException;

final class ConstructorInitializedLogicalProblem extends LogicalException
{

	public static function create(string $message): self
	{
		return new self($message);
	}

}
