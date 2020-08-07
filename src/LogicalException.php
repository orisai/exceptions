<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use LogicException;
use Orisai\Exceptions\Check\UncheckedException;
use Throwable;

abstract class LogicalException extends LogicException implements UncheckedException
{

	use ConfigurableException;

	final public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new static();
	}

}
