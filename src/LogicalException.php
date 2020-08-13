<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use LogicException;
use Orisai\Exceptions\Check\UncheckedException;

abstract class LogicalException extends LogicException implements UncheckedException
{

	use ConfigurableException;

}
