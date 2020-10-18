<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Doubles;

use Orisai\Exceptions\DomainException;

final class ExampleUserError extends DomainException
{

	public function __construct()
	{
		parent::__construct();
	}

}
