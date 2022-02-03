<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Exception;
use ReflectionClass;
use Stringable;
use Throwable;
use function array_key_first;
use function array_key_last;
use function count;
use function explode;
use function get_class;
use function preg_replace;
use function str_repeat;
use const PHP_EOL;

trait ConfigurableException
{

	/** @var array<int, Throwable> */
	private array $suppressed = [];

	private bool $isAnySuppressedInMessage = false;

	public static bool $addSuppressedToMessage = true;

	/**
	 * @return $this
	 */
	public function withCode(int $code): self
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @param string|Stringable $message
	 * @return $this
	 */
	public function withMessage($message): self
	{
		$this->message = (string) $message;

		if (static::$addSuppressedToMessage) {
			$this->isAnySuppressedInMessage = false;
			foreach ($this->suppressed as $key => $item) {
				$this->addSuppressedToMessage($item, array_key_first($this->suppressed) === $key);
			}
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function withPrevious(Throwable $throwable): self
	{
		// phpcs:disable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
		$reflection = new ReflectionClass(Exception::class);
		// phpcs:enable
		$property = $reflection->getProperty('previous');
		$property->setAccessible(true);
		$property->setValue($this, $throwable);

		return $this;
	}

	/**
	 * @param array<Throwable> $suppressed
	 * @return $this
	 */
	public function withSuppressed(array $suppressed): self
	{
		foreach ($suppressed as $item) {
			$this->suppressed[] = $item;
			if (static::$addSuppressedToMessage) {
				$this->addSuppressedToMessage($item, count($this->suppressed) === 1);
			}
		}

		return $this;
	}

	/**
	 * @return array<Throwable>
	 */
	public function getSuppressed(): array
	{
		return $this->suppressed;
	}

	private function addSuppressedToMessage(Throwable $throwable, bool $isFirst): void
	{
		if (!$this->isAnySuppressedInMessage) {
			$this->message .= ($this->message === '' ? '' : PHP_EOL)
				. 'Suppressed errors:';
		}

		$this->isAnySuppressedInMessage = true;
		$this->message .= $this->formatSuppressedExceptionMessage($throwable, $isFirst);
	}

	private function formatSuppressedExceptionMessage(Throwable $throwable, bool $isFirst): string
	{
		return ($isFirst ? '' : PHP_EOL)
			. $this->indentMessage(
				$this->formatSuppressedExceptionMessageInner($throwable),
				2,
				0,
			);
	}

	private function formatSuppressedExceptionMessageInner(Throwable $throwable): string
	{
		$message = preg_replace('~\R~u', PHP_EOL, $throwable->getMessage());
		$class = get_class($throwable);
		$file = $throwable->getFile();
		$line = $throwable->getLine();
		$code = $throwable->getCode();

		// Track exception source in case exception is created by static ctor
		$traceStart = $throwable->getTrace()[0];
		if (
			isset($traceStart['class'], $traceStart['file'], $traceStart['line'])
			&& $traceStart['class'] === $class
		) {
			$file = $traceStart['file'];
			$line = $traceStart['line'];
		}

		$newMessage = "- $class created at $file:$line with code $code" . PHP_EOL;
		$newMessage .= $message === '' ? '<NO MESSAGE>' : $message;

		$previous = $throwable->getPrevious();
		if ($previous !== null) {
			$newMessage .= $this->indentMessage(
				$this->formatSuppressedExceptionMessageInner($previous),
				6,
				4,
			);
		}

		return $newMessage;
	}

	/**
	 * @param positive-int $size
	 */
	private function indentMessage(string $originalMessage, int $size, int $firstLineSize): string
	{
		$lines = explode(PHP_EOL, $originalMessage);
		$firstLine = array_key_first($lines);
		$lastLine = array_key_last($lines);

		$spaces = str_repeat(' ', $size);
		$firstSpaces = str_repeat(' ', $firstLineSize);

		$message = PHP_EOL;
		foreach ($lines as $key => $line) {
			$message .=
				($key === $firstLine ? $firstSpaces : $spaces)
				. $line
				. ($key === $lastLine ? '' : PHP_EOL);
		}

		return $message;
	}

}
