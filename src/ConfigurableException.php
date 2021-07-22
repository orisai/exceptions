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
	public function withPrevious(Throwable $throwable)
	{
		$reflection = new ReflectionClass(Exception::class);
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
		$message = preg_replace('~\R~u', PHP_EOL, $throwable->getMessage());
		$class = get_class($throwable);
		$file = $throwable->getFile();
		$line = $throwable->getLine();

		// Track exception source in case exception is created by static ctor
		$traceStart = $throwable->getTrace()[0];
		if (isset($traceStart['class']) && $traceStart['class'] === $class) {
			$file = $traceStart['file'];
			$line = $traceStart['line'];
		}

		$newMessage = "- $class created at $file:$line with code {$throwable->getCode()}" . PHP_EOL;
		$newMessage .= $message === '' ? '<NO MESSAGE>' : $message;

		return ($isFirst ? '' : PHP_EOL)
			. $this->indentMessage($newMessage);
	}

	private function indentMessage(string $originalMessage): string
	{
		$message = PHP_EOL;
		$lines = explode(PHP_EOL, $originalMessage);
		$lastLine = array_key_last($lines);
		$spaces = str_repeat(' ', 4);
		foreach ($lines as $key => $line) {
			$message .=
				$spaces
				. $line
				. ($key === $lastLine ? '' : PHP_EOL);
		}

		return $message;
	}

}
