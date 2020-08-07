<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Exception;
use Orisai\Exceptions\Check\CheckedException;

abstract class DomainException extends Exception implements CheckedException
{

	use ConfigurableException;

}
