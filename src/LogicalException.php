<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use LogicException;
use Orisai\Exceptions\Check\UncheckedException;
use Throwable;

abstract class LogicalException extends LogicException implements UncheckedException
{

	use ConfigurableException;

	protected function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}
