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
	 * @return $this
	 */
	public function withCode(int $code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string|Stringable $message
	 * @return $this
	 */
	public function withMessage($message)
	{
		$this->message = (string) $message;

		return $this;
	}

	/**
	 * @return $this
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
