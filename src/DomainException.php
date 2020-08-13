<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Exception;
use Orisai\Exceptions\Check\CheckedException;
use Throwable;

abstract class DomainException extends Exception implements CheckedException
{

	use ConfigurableException;

	protected function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}
