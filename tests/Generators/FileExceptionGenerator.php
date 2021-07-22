<?php declare(strict_types = 1);

use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Logic\ShouldNotHappen;

return InvalidState::create()
	->withSuppressed([
		ShouldNotHappen::create()->withMessage('test'),
	]);
