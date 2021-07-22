<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Doubles;

use Orisai\Exceptions\DomainException;

final class ConstructorInitializedDomainProblem extends DomainException
{

	public static function create(string $message): self
	{
		return new self($message);
	}

}
