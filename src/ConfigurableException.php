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
use function str_repeat;
use function str_replace;
use const PHP_EOL;

trait ConfigurableException
{

	/** @var array<int, Throwable> */
	private array $suppressed = [];

	private bool $isAnySuppressedInMessage = false;

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

		$this->isAnySuppressedInMessage = false;
		foreach ($this->suppressed as $key => $item) {
			$this->addSuppressedToMessage($item, array_key_first($this->suppressed) === $key);
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
		$property->setAccessible(false);

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
			$this->addSuppressedToMessage($item, count($this->suppressed) === 1);
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
		$message = $throwable->getMessage();
		$class = get_class($throwable);
		$file = $throwable->getFile();
		$line = $throwable->getLine();

		foreach (($trace = $throwable->getTrace()) as $key => $item) {
			if (isset($item['class']) && $item['class'] === $class) {
				$nextItem = $trace[$key + 1] ?? null;
				if ($nextItem !== null && isset($nextItem['class']) && $nextItem['class'] !== $class) {
					$file = $item['file'];
					$line = $item['line'];

					break;
				}
			}
		}

		$newMessage = "- $class created at $file:$line with code {$throwable->getCode()}" . PHP_EOL;
		$newMessage .= $message === '' ? '<NO MESSAGE>' : $message;

		return ($isFirst ? '' : PHP_EOL)
			. $this->indentMessage($newMessage);
	}

	private function indentMessage(string $originalMessage): string
	{
		if (PHP_EOL !== "\n") {
			$originalMessage = str_replace("\n", PHP_EOL, $originalMessage);
		}

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
