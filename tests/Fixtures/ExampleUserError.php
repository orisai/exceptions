<?php declare(strict_types = 1);

namespace Tests\Orisai\Exceptions\Fixtures;

use Orisai\Exceptions\DomainException;

final class ExampleUserError extends DomainException
{

	public function __construct()
	{
		parent::__construct();
	}

}
