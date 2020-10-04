<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Exception;
use ReflectionClass;
use Stringable;
use Throwable;

/**
 * @mixin Exception
 */
trait ConfigurableException
{

	/**
	 * @return static
	 */
	public function withCode(int $code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string|Stringable $message
	 * @return static
	 */
	public function withMessage($message)
	{
		$this->message = (string) $message;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withPrevious(Throwable $throwable)
	{
		$reflection = new ReflectionClass(Exception::class);
		$property = $reflection->getProperty('previous');
		$property->setAccessible(true);
		$property->setValue($this, $throwable);
		$property->setAccessible(false);

		return $this;
	}

}
